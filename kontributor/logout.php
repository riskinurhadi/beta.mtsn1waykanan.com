<?php
// Selalu mulai session di awal
session_start();

// Hapus semua variabel session yang berhubungan dengan kontributor
unset($_SESSION['kontributor_id']);
unset($_SESSION['kontributor_nama']);
unset($_SESSION['kontributor_email']);
unset($_SESSION['kontributor_api_key']);

// Hancurkan session
session_destroy();

// Arahkan kembali ke halaman login kontributor
header("location: login.php");
exit;
?>
