<?php
require_once 'config.php';

// Keamanan: Pastikan hanya Super Admin dan Developer yang bisa mengakses
if (!isset($_SESSION['operator_id']) || !in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    http_response_code(403);
    die("Akses Ditolak. Anda tidak memiliki izin untuk melihat halaman ini.");
}

// Menampilkan seluruh informasi PHP
phpinfo();

?>
