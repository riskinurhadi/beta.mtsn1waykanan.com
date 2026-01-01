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

    // 2. Ambil status 'is_tampil' saat ini dari database
    $current_status = null;
    $sql_select = "SELECT is_tampil FROM testimoni_alumni WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id);
        $stmt_select->execute();
        $stmt_select->bind_result($is_tampil);
        
        if ($stmt_select->fetch()) {
            $current_status = $is_tampil;
        }
        $stmt_select->close();
    }

    // Jika data testimoni ditemukan, lanjutkan proses toggle
    if ($current_status !== null) {
        // 3. Tentukan status baru (kebalikan dari status saat ini)
        // Jika saat ini 1 (TRUE), ubah jadi 0 (FALSE), dan sebaliknya.
        $new_status = $current_status ? 0 : 1;

        // 4. Update status baru ke database
        $sql_update = "UPDATE testimoni_alumni SET is_tampil = ? WHERE id = ?";
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param("ii", $new_status, $id);
            
            if ($stmt_update->execute()) {
                $_SESSION['success_message'] = "Status testimoni berhasil diubah.";
            } else {
                $_SESSION['error_message'] = "Gagal mengubah status testimoni.";
            }
            $stmt_update->close();
        }
    } else {
        $_SESSION['error_message'] = "Data testimoni dengan ID tersebut tidak ditemukan.";
    }

} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola testimoni dengan notifikasi
header("Location: kelola_testimoni.php");
exit();
