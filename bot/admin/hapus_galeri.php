<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location:login.php");
    exit();
}

// Cek apakah ID ada dan valid
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];

    // 1. Ambil nama file dari database SEBELUM menghapus recordnya
    $sql_select = "SELECT foto_url FROM galeri WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($foto_url);
        
        if ($stmt_select->fetch()) {
            // Path lengkap ke file gambar
            $file_path = 'uploads/galeri/' . $foto_url;
            $stmt_select->close();

            // 2. Hapus record dari database
            $sql_delete = "DELETE FROM galeri WHERE id = ?";
            if ($stmt_delete = $conn->prepare($sql_delete)) {
                $stmt_delete->bind_param("i", $id);
                
                if ($stmt_delete->execute()) {
                    // 3. Jika record berhasil dihapus, hapus file fisiknya dari server
                    if (file_exists($file_path)) {
                        unlink($file_path); // Fungsi untuk menghapus file
                    }
                    $_SESSION['success_message'] = "Foto berhasil dihapus.";
                } else {
                    $_SESSION['error_message'] = "Gagal menghapus data dari database.";
                }
                $stmt_delete->close();
            }
        } else {
            // Jika ID tidak ditemukan
             $_SESSION['error_message'] = "Data galeri tidak ditemukan.";
        }
    }
} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola galeri
header("Location: kelola_galeri.php");
exit();