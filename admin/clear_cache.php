<?php
header('Content-Type: application/json');
require_once 'config.php'; // Untuk check session

// Keamanan: Pastikan hanya operator yang login yang bisa akses
if (!isset($_SESSION['operator_id'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Akses ditolak']);
    exit();
}

// Periksa apakah OPcache diaktifkan
if (function_exists('opcache_reset') && opcache_get_status()) {
    // Kosongkan OPcache
    if (opcache_reset()) {
        echo json_encode(['success' => true, 'message' => 'PHP OPcache berhasil dikosongkan.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal mengosongkan OPcache.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'OPcache tidak aktif atau tidak tersedia di server ini.']);
}
?>
