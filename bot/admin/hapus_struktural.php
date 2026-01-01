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

    // 1. Ambil nama file foto dari database
    $sql_select = "SELECT foto_url FROM struktur_organisasi WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($foto_url);
        
        if ($stmt_select->fetch()) {
            $file_to_delete = $foto_url;
            $stmt_select->close();

            // 2. Hapus record dari database
            $sql_delete = "DELETE FROM struktur_organisasi WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id);
                
                if ($stmt_delete->execute()) {
                    // 3. Jika berhasil, hapus file foto fisiknya
                    if ($file_to_delete && $file_to_delete !== 'default-profile.png') {
                        $file_path = 'uploads/struktur/' . $file_to_delete;
                        if (file_exists($file_path)) {
                            unlink($file_path);
                        }
                    }
                    $_SESSION['success_message'] = "Data anggota berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus data dari database.";
                }
                $stmt_delete->close();
            }
        } else {
             $_SESSION['error_message'] = "Data anggota tidak ditemukan.";
        }
    }
} else {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();
header("Location: kelola_struktural.php");
exit();
