<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: ../login.php");
    exit();
}
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    $_SESSION['error_message'] = "Akses ditolak.";
    header("Location: kelola_berita.php");
    exit();
}

// Validasi ID dan Aksi dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT) || !isset($_GET['aksi'])) {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
    header("Location: kelola_berita.php");
    exit();
}

$id = $_GET['id'];
$aksi = $_GET['aksi'];
$sql = '';
$params = [];
$types = '';
$success_message = '';

// Menentukan query berdasarkan aksi
switch ($aksi) {
    case 'terbitkan':
        $sql = "UPDATE berita SET status_berita = 'Diterbitkan' WHERE id = ?";
        $params = [$id];
        $types = "i";
        $success_message = "Berita berhasil diterbitkan.";
        break;
    
    case 'tolak':
        $sql = "UPDATE berita SET status_berita = 'Ditolak' WHERE id = ?";
        $params = [$id];
        $types = "i";
        $success_message = "Berita berhasil ditolak.";
        break;
    
    default:
        $_SESSION['error_message'] = "Aksi tidak dikenal.";
        header("Location: kelola_berita.php");
        exit();
}

// Eksekusi query
if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param($types, ...$params);
    if ($stmt->execute()) {
        $_SESSION['success_message'] = $success_message;
    } else {
        $_SESSION['error_message'] = "Gagal melakukan aksi: " . $stmt->error;
    }
    $stmt->close();
} else {
    $_SESSION['error_message'] = "Gagal mempersiapkan query.";
}

$conn->close();
header("Location: kelola_berita.php");
exit();
