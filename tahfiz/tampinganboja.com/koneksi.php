<?php

// --- PENGATURAN MODE PERBAIKAN ---
// Ubah menjadi 'true' untuk mengaktifkan popup, 'false' untuk menonaktifkan.
$maintenanceMode = false;

// Konfigurasi koneksi database
$db_host = 'localhost';      // Biasanya 'localhost'
$db_user = 'mtsp7673_tampingan';           // User default XAMPP
$db_pass = 'Aloevera21.';               // Password default XAMPP kosong
$db_name = 'mtsp7673_tampingan'; // Nama database yang Anda buat

// Membuat koneksi menggunakan MySQLi
$koneksi = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Memeriksa koneksi
if ($koneksi->connect_error) {
    // Jika koneksi gagal, hentikan skrip dan tampilkan pesan error
    die("Koneksi ke database gagal: " . $koneksi->connect_error);
}


// Mengatur character set ke utf8mb4 untuk mendukung karakter yang lebih luas
$koneksi->set_charset("utf8mb4");
?>
