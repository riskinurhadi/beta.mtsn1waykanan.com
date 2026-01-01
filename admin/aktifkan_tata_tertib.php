<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Validasi ID dari URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id_to_activate = $_GET['id'];

    // Gunakan transaksi untuk memastikan integritas data
    $conn->begin_transaction();

    try {
        // 1. Nonaktifkan semua file tata tertib yang lain
        $sql_deactivate = "UPDATE tata_tertib SET is_active = FALSE WHERE is_active = TRUE";
        $conn->query($sql_deactivate);

        // 2. Aktifkan file tata tertib yang dipilih
        $sql_activate = "UPDATE tata_tertib SET is_active = TRUE WHERE id = ?";
        $stmt_activate = $conn->prepare($sql_activate);
        $stmt_activate->bind_param("i", $id_to_activate);
        $stmt_activate->execute();

        // Jika kedua query berhasil, commit transaksi
        $conn->commit();
        $_SESSION['success_message'] = "File tata tertib berhasil diaktifkan.";

    } catch (mysqli_sql_exception $exception) {
        // Jika terjadi error, batalkan semua perubahan (rollback)
        $conn->rollback();
        $_SESSION['error_message'] = "Gagal mengaktifkan file: " . $exception->getMessage();
    }

} else {
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();
// Redirect kembali ke halaman kelola tata tertib
header("Location: kelola_tata_tertib.php");
exit();
