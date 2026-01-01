<?php
// Selalu mulai session di awal
session_start();

// Hapus semua variabel session yang berhubungan dengan admin/operator
unset($_SESSION['operator_id']);
unset($_SESSION['nama_lengkap']);
unset($_SESSION['username']);
unset($_SESSION['role']);
unset($_SESSION['foto_profil']);

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman login admin
header("location: login.php");
exit;
?>
