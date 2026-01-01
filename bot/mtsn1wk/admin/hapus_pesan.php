<?php
require_once 'config.php';

// --- SECURITY CHECK ---
// Pastikan hanya operator yang sudah login yang bisa mengakses
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Validasi ID dari URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // 2. Hapus record dari database
    $sql = "DELETE FROM pesan_kontak WHERE id = ?";
    
    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Cek apakah ada baris yang terpengaruh (dihapus)
            if ($stmt->affected_rows > 0) {
                // Set pesan sukses untuk notifikasi SweetAlert
                $_SESSION['success_message'] = "Pesan berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Pesan tidak ditemukan atau sudah dihapus.";
            }
        } else {
            $_SESSION['error_message'] = "Gagal menghapus pesan dari database.";
        }
        $stmt->close();
    }
} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola pesan
header("Location: kelola_kontak_pesan.php");
exit();
