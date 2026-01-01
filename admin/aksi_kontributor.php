<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: ../login.php");
    exit();
}
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    $_SESSION['error_message'] = "Akses ditolak.";
    header("Location: kelola_kontributor.php");
    exit();
}

if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT) || !isset($_GET['aksi'])) {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
    header("Location: kelola_kontributor.php");
    exit();
}

$id = $_GET['id'];
$aksi = $_GET['aksi'];

// Fungsi untuk membuat API Key acak
function generate_api_key($prefix = 'mtsn1_live_sk_', $length = 32) {
    return $prefix . bin2hex(random_bytes($length));
}

// Menggunakan transaksi untuk memastikan kedua query berhasil
$conn->begin_transaction();

try {
    if ($aksi == 'setujui') {
        // 1. Ubah status akun di tabel kontributor
        $stmt1 = $conn->prepare("UPDATE kontributor SET status_akun = 'aktif' WHERE id = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $stmt1->close();

        // 2. Buat API Key baru di tabel api_keys
        $api_key = generate_api_key();
        $nama_pengguna = "Kontributor ID: " . $id; // Nama default untuk pengguna
        $izin = "tulis_berita"; // Izin default
        
        $stmt2 = $conn->prepare("INSERT INTO api_keys (nama_pengguna, api_key, izin, id_kontributor) VALUES (?, ?, ?, ?)");
        $stmt2->bind_param("sssi", $nama_pengguna, $api_key, $izin, $id);
        $stmt2->execute();
        $stmt2->close();
        
        $_SESSION['success_message'] = "Kontributor berhasil disetujui dan API Key telah dibuat.";

    } elseif ($aksi == 'tolak') {
        $stmt = $conn->prepare("UPDATE kontributor SET status_akun = 'ditolak' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Pendaftaran kontributor berhasil ditolak.";

    } elseif ($aksi == 'blokir') {
        $stmt = $conn->prepare("UPDATE kontributor SET status_akun = 'diblokir' WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $stmt->close();
        $_SESSION['success_message'] = "Akun kontributor berhasil diblokir.";

    } elseif ($aksi == 'hapus') {
        // Hapus juga API Key yang terkait
        $stmt1 = $conn->prepare("DELETE FROM api_keys WHERE id_kontributor = ?");
        $stmt1->bind_param("i", $id);
        $stmt1->execute();
        $stmt1->close();

        // Hapus kontributor
        $stmt2 = $conn->prepare("DELETE FROM kontributor WHERE id = ?");
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $stmt2->close();
        $_SESSION['success_message'] = "Data kontributor berhasil dihapus.";
    }

    // Jika semua berhasil, simpan perubahan
    $conn->commit();

} catch (mysqli_sql_exception $exception) {
    $conn->rollback(); // Batalkan semua jika ada error
    $_SESSION['error_message'] = "Terjadi kesalahan: " . $exception->getMessage();
}

$conn->close();
header("Location: kelola_kontributor.php");
exit();
