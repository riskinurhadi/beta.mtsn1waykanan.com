<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Validasi ID dari URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // 1. Ambil nama file dari database sebelum menghapus record
    $sql_select = "SELECT nama_file FROM gambar_struktur WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($nama_file);
        
        if ($stmt_select->fetch()) {
            $file_to_delete = $nama_file;
            $stmt_select->close();

            // 2. Hapus record dari database
            $sql_delete = "DELETE FROM gambar_struktur WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id);
                
                if ($stmt_delete->execute()) {
                    // 3. Jika berhasil, hapus file gambar fisiknya
                    if ($file_to_delete) {
                        $file_path = 'uploads/struktur/' . $file_to_delete;
                        if (file_exists($file_path)) {
                            unlink($file_path); // Fungsi untuk menghapus file
                        }
                    }
                    $_SESSION['success_message'] = "Gambar struktur berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus data dari database.";
                }
                $stmt_delete->close();
            }
        } else {
             $_SESSION['error_message'] = "Gambar struktur tidak ditemukan.";
        }
    }
} else {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();
// Redirect kembali ke halaman kelola struktural
header("Location: kelola_struktural.php");
exit();
