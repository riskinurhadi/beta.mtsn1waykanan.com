<?php
header('Content-Type: application/json');
require_once 'config.php';

// Keamanan: Pastikan hanya Super Admin dan Developer yang bisa mengakses
if (!isset($_SESSION['operator_id']) || !in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

// Hanya proses jika permintaan adalah POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil nama tabel dari data POST
    $table_name = $_POST['table_name'] ?? '';

    if (!empty($table_name)) {
        // Sanitasi nama tabel untuk keamanan dasar
        $safe_table_name = '`' . str_replace('`', '', $table_name) . '`';

        // Jalankan query OPTIMIZE TABLE
        if ($conn->query("OPTIMIZE TABLE " . $safe_table_name)) {
            echo json_encode(['success' => true, 'message' => 'Tabel ' . htmlspecialchars($table_name) . ' berhasil dioptimalkan.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal mengoptimalkan tabel: ' . $conn->error]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Nama tabel tidak valid.']);
    }
    $conn->close();
} else {
    // Jika bukan permintaan POST, tolak akses
    header("Location: index.php");
    exit();
}
?>
