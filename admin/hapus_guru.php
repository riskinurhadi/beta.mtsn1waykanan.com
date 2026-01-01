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
    $file_foto_untuk_dihapus = null;

    // 2. Ambil nama file foto dari database SEBELUM menghapus record
    // Ini penting agar kita bisa menghapus file fisiknya dari server
    $sql_select = "SELECT foto_guru FROM data_guru WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($foto_guru);
        
        // Simpan nama file jika ada
        if ($stmt_select->fetch()) {
            $file_foto_untuk_dihapus = $foto_guru;
        }
        $stmt_select->close();
    }

    // Jika data guru dengan ID tersebut ada, lanjutkan proses hapus
    if ($file_foto_untuk_dihapus !== null) {
        // 3. Hapus record dari tabel 'data_guru' di database
        $sql_delete = "DELETE FROM data_guru WHERE id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id);
            
            if ($stmt_delete->execute()) {
                // 4. Jika record berhasil dihapus, hapus file foto fisiknya
                // Jangan hapus file 'default-profile.png'
                if ($file_foto_untuk_dihapus && $file_foto_untuk_dihapus !== 'default-profile.png') {
                    $file_path = 'uploads/guru/' . $file_foto_untuk_dihapus;
                    if (file_exists($file_path)) {
                        unlink($file_path); // Fungsi PHP untuk menghapus file
                    }
                }
                // Siapkan pesan sukses untuk notifikasi
                $_SESSION['success_message'] = "Data guru berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus data dari database.";
            }
            $stmt_delete->close();
        }
    } else {
        $_SESSION['error_message'] = "Data guru dengan ID tersebut tidak ditemukan.";
    }

} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola guru dengan notifikasi
header("Location: kelola_guru.php");
exit();
