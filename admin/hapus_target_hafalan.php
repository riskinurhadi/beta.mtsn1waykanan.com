<?php
require_once 'config.php';

// --- PEMERIKSAAN KEAMANAN ---
// Memastikan hanya operator yang sudah login yang bisa mengakses
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// 1. Validasi ID yang dikirim melalui URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id = $_GET['id'];
    $file_untuk_dihapus = null;

    // 2. Ambil nama file dari database SEBELUM menghapus recordnya
    $sql_select = "SELECT nama_file FROM target_hafalan WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($nama_file);
        
        // Simpan nama file jika ada
        if ($stmt_select->fetch()) {
            $file_untuk_dihapus = $nama_file;
        }
        $stmt_select->close();
    }

    // Jika data ditemukan, lanjutkan proses hapus
    if ($file_untuk_dihapus !== null) {
        // 3. Hapus record dari tabel 'target_hafalan' di database
        $sql_delete = "DELETE FROM target_hafalan WHERE id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id);
            
            if ($stmt_delete->execute()) {
                // 4. Jika record berhasil dihapus, hapus file fisiknya dari server
                if ($file_untuk_dihapus) {
                    $file_path = 'uploads/hafalan/' . $file_untuk_dihapus;
                    if (file_exists($file_path)) {
                        unlink($file_path); // Fungsi PHP untuk menghapus file
                    }
                }
                // Siapkan pesan sukses untuk notifikasi
                $_SESSION['success_message'] = "File target hafalan berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data dari database.";
            }
            $stmt_delete->close();
        }
    } else {
        $_SESSION['error_message'] = "File target hafalan dengan ID tersebut tidak ditemukan.";
    }

} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola target hafalan dengan notifikasi
header("Location: kelola_target_hafalan.php");
exit();
