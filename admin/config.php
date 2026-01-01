<?php
// Mulai session di setiap halaman
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Konfigurasi Database
define('DB_HOST', 'localhost');
define('DB_USER', 'mtsn1wkbeta');
define('DB_PASS', 'Aloevera21.');
define('DB_NAME', 'mtsn1wkbeta');

// Buat Koneksi
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
$jumlah_pesan_baru = 0;

// Cek Koneksi
if ($conn->connect_error) {
    die("Koneksi ke database gagal: " . $conn->connect_error);
}

// ==========================================================
// BARU: Logika untuk Notifikasi Pesan Baru
// Diletakkan di sini agar tersedia di semua halaman admin.
// ==========================================================
// Hanya jalankan query jika operator sudah login untuk efisiensi
if (isset($_SESSION['operator_id'])) {
    $sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
    $result_count = $conn->query($sql_count);
    if ($result_count && $result_count->num_rows > 0) {
        $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
    }
}

if (!$conn->set_charset("utf8mb4")) {
    // Jika gagal, tampilkan error (opsional, untuk debugging)
    printf("Error loading character set utf8mb4: %s\n", $conn->error);
    exit();
}

?>