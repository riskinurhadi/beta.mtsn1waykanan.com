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
    $file_to_delete = null;

    // 2. Ambil nama file foto dari database SEBELUM menghapus recordnya
    // Ini penting agar kita bisa menghapus file fisiknya
    $sql_select = "SELECT foto_url FROM prestasi WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($foto_url);
        
        // Simpan nama file jika ada
        if ($stmt_select->fetch()) {
            $file_to_delete = $foto_url;
        }
        $stmt_select->close();
    }

    // 3. Hapus record dari database
    $sql_delete = "DELETE FROM prestasi WHERE id = ?";
    if ($stmt_delete = $conn->prepare($sql_delete)) {
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            // 4. Jika record berhasil dihapus, hapus file fisiknya dari server
            if ($file_to_delete) {
                $file_path = 'uploads/prestasi/' . $file_to_delete;
                if (file_exists($file_path)) {
                    unlink($file_path); // Fungsi PHP untuk menghapus file
                }
            }
            // Set pesan sukses untuk notifikasi SweetAlert
            $_SESSION['success_message'] = "Data prestasi berhasil dihapus.";
        } else {
            $_SESSION['error_message'] = "Gagal menghapus data dari database.";
        }
        $stmt_delete->close();
    }
} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola prestasi
header("Location: kelola_prestasi.php");
exit();