<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Cek apakah ID ada dan valid
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // 1. Ambil nama file gambar dari database
    $sql_select = "SELECT gambar_utama FROM berita WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($gambar_utama);
        
        if ($stmt_select->fetch()) {
            $file_path = 'uploads/berita/' . $gambar_utama;
            $stmt_select->close();

            // 2. Hapus record dari database
            $sql_delete = "DELETE FROM berita WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id);
                
                if ($stmt_delete->execute()) {
                    // 3. Jika berhasil, hapus file gambar fisiknya
                    if ($gambar_utama && file_exists($file_path)) {
                        unlink($file_path);
                    }
                    $_SESSION['success_message'] = "Berita berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus berita dari database.";
                }
                $stmt_delete->close();
            }
        } else {
             $_SESSION['error_message'] = "Berita tidak ditemukan.";
        }
    }
} else {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();
header("Location: kelola_berita.php");
exit();