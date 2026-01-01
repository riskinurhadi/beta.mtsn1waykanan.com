<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'mtsp7673_kartono');
define('DB_PASS', 'Aloevera21.');
define('DB_NAME', 'mtsp7673_mtsn1wk');

try {
    $koneksi = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // Memeriksa apakah terjadi error saat koneksi
    if ($koneksi->connect_error) {
        die("Koneksi ke database gagal: " . $koneksi->connect_error);
    }
    $koneksi->set_charset("utf8mb4");

} catch (Exception $e) {
    // Menangkap error lain yang mungkin terjadi
    die("Terjadi error pada koneksi database: " . $e->getMessage());
}


?>