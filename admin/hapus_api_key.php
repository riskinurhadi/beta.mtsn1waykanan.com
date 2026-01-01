<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}
// Hanya Super Admin dan Developer yang bisa mengakses
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    $_SESSION['error_message'] = "Akses ditolak.";
    header("Location: kelola_api_keys.php");
    exit();
}

// Validasi ID dari URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // Hapus record dari database
    $sql = "DELETE FROM api_keys WHERE id = ?";
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $_SESSION['success_message'] = "API Key berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus API Key.";
        }
        $stmt->close();
    }
} else {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();
header("Location: kelola_api_keys.php");
exit();
