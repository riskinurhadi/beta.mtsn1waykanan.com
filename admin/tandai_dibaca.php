<?php
// Memanggil file konfigurasi untuk koneksi database dan session
require_once 'config.php';

// Hanya proses jika permintaan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Pastikan operator sudah login
    if (!isset($_SESSION['operator_id'])) {
        // Kirim response error jika tidak ada session
        echo json_encode(['success' => false, 'message' => 'Akses tidak sah.']);
        exit();
    }

    // Ambil ID notifikasi dari data POST
    $notif_id = $_POST['id'] ?? 0;
    $operator_id = $_SESSION['operator_id'];

    if (filter_var($notif_id, FILTER_VALIDATE_INT) && $notif_id > 0) {
        // Siapkan query untuk menandai notifikasi sebagai sudah dibaca
        // PENTING: Pastikan hanya notifikasi milik operator yang sedang login yang bisa diubah
        $sql = "UPDATE notifikasi_operator SET sudah_dibaca = TRUE WHERE id = ? AND id_penerima = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ii", $notif_id, $operator_id);
            
            if ($stmt->execute()) {
                // Jika berhasil, kirim response sukses
                echo json_encode(['success' => true]);
            } else {
                // Jika query gagal
                echo json_encode(['success' => false, 'message' => 'Gagal memperbarui status notifikasi.']);
            }
            $stmt->close();
        }
    } else {
        // Jika ID notifikasi tidak valid
        echo json_encode(['success' => false, 'message' => 'ID notifikasi tidak valid.']);
    }
    $conn->close();
} else {
    // Jika bukan permintaan POST
    header("Location: index.php");
    exit();
}
?>
