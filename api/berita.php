<?php
// Mengatur header agar output selalu dalam format JSON dan UTF-8
header("Content-Type: application/json; charset=UTF-8");

// Memanggil file koneksi.
require_once '../admin/config.php';

// Fungsi untuk mengirim response JSON dan menghentikan skrip
function send_json_response($data, $status_code = 200) {
    http_response_code($status_code);
    echo json_encode($data);
    exit();
}

// --- Keamanan: Verifikasi API Key ---
$api_key = $_GET['api_key'] ?? '';
if (empty($api_key)) {
    send_json_response(['error' => 'API Key dibutuhkan'], 401);
}

$izin_pengguna = null;
$sql_check_key = "SELECT izin FROM api_keys WHERE api_key = ? AND status = 'aktif' LIMIT 1";
if ($stmt_check = $conn->prepare($sql_check_key)) {
    $stmt_check->bind_param("s", $api_key);
    $stmt_check->execute();
    $result_key = $stmt_check->get_result();

    if ($result_key->num_rows === 0) {
        send_json_response(['error' => 'API Key tidak valid atau tidak aktif'], 403);
    }
    $izin_pengguna = $result_key->fetch_assoc()['izin'];
    $stmt_check->close();
} else {
    send_json_response(['error' => 'Kesalahan server saat verifikasi kunci'], 500);
}

// --- Menangani Permintaan (Request) ---
$method = $_SERVER['REQUEST_METHOD'];

// Logika untuk MEMBACA berita (GET)
if ($method == 'GET') {
    // ... (Logika GET Anda tetap sama)
}

// Logika untuk MENULIS berita (POST)
elseif ($method == 'POST') {
    // Keamanan: Cek izin
    if (strpos($izin_pengguna, 'tulis_berita') === false) {
        send_json_response(['error' => 'API Key ini tidak memiliki izin untuk menulis berita.'], 403);
    }

    // Ambil data dari body request
    $judul = $_POST['judul'] ?? '';
    $isi = $_POST['isi'] ?? '';
    $penulis = $_POST['penulis'] ?? 'Kontributor';
    $kategori = $_POST['kategori'] ?? 'Umum';
    // PERUBAHAN: Ambil id_kontributor dari form
    $id_kontributor = $_POST['id_kontributor'] ?? null;

    // Validasi data
    if (empty($judul) || empty($isi) || empty($id_kontributor)) {
        send_json_response(['error' => 'Judul, Isi, dan ID Kontributor tidak boleh kosong.'], 400);
    }
    if (!isset($_FILES['gambar_utama']) || $_FILES['gambar_utama']['error'] != 0) {
        send_json_response(['error' => 'Gambar utama wajib diunggah.'], 400);
    }

    $target_dir = "../admin/uploads/berita/";
    $file_ext = strtolower(pathinfo($_FILES['gambar_utama']['name'], PATHINFO_EXTENSION));
    $gambar_utama_db = uniqid('berita_', true) . '.' . $file_ext;

    if (!move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $target_dir . $gambar_utama_db)) {
        send_json_response(['error' => 'Gagal mengunggah gambar utama.'], 500);
    }

    // Buat slug
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));

    // PERUBAHAN: Simpan ke database dengan status 'Pending' dan id_kontributor
    $sql_insert = "INSERT INTO berita (judul, isi, penulis, kategori, gambar_utama, slug, id_kontributor, status_berita) VALUES (?, ?, ?, ?, ?, ?, ?, 'Pending')";
    if ($stmt_insert = $conn->prepare($sql_insert)) {
        // PERUBAHAN: Menambahkan 'i' untuk integer id_kontributor
        $stmt_insert->bind_param("ssssssi", $judul, $isi, $penulis, $kategori, $gambar_utama_db, $slug, $id_kontributor);
        if ($stmt_insert->execute()) {
            send_json_response(['success' => true, 'message' => 'Berita berhasil dikirim dan sedang menunggu persetujuan admin.'], 201);
        } else {
            send_json_response(['error' => 'Gagal menyimpan berita ke database.'], 500);
        }
        $stmt_insert->close();
    }
}

// Jika metode lain (PUT, DELETE), kirim pesan error
else {
    send_json_response(['error' => 'Metode permintaan tidak diizinkan'], 405);
}

$conn->close();
?>
