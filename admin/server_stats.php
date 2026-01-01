<?php
// Mengatur header agar output berupa JSON
header('Content-Type: application/json');
require_once 'config.php'; // Untuk check session

// Hanya Super Admin dan Developer yang bisa mengakses
if (!in_array($_SESSION['role'], [' ', 'developer'])) {
    // Tampilkan halaman akses ditolak
    http_response_code(403);
    echo <<<HTML
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Akses Ditolak</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>:root{--danger-color:#e74c3c;--main-bg:#f4f7f6;--text-color:#333}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;color:var(--text-color)}.access-denied-container{text-align:center;background-color:#fff;padding:40px 50px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1)}.icon-wrapper i{font-size:60px;color:var(--danger-color);margin-bottom:20px}h1{font-size:28px;font-weight:600;margin-bottom:10px}p{font-size:16px;color:#666;margin-bottom:30px}.btn-back{background-color:#6c757d;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px;transition:background-color .3s ease}.btn-back:hover{background-color:#5a6268}</style>
</head>
<body>
<div class="access-denied-container">
<div class="icon-wrapper">
<i class="fas fa-ban"></i>
</div>
<h1>Akses Ditolak</h1>
<p>Anda tidak memiliki Akses<br>Halaman ini hanya bisa di akses oleh Pengembang sistem ini.</p>
<a href="index.php" class="btn-back"><i class="fas fa-arrow-left">
</i> Kembali ke Dashboard</a></div></body></html>
HTML;
    exit();
}

// --- Mengambil Data Performa Server ---

// 1. CPU Load (Beban CPU)
// Mengambil rata-rata beban dalam 1, 5, dan 15 menit terakhir.
// Catatan: Fungsi ini mungkin dinonaktifkan pada beberapa shared hosting.
$cpu_load = function_exists('sys_getloadavg') ? sys_getloadavg() : [0, 0, 0];

// 2. Memory Usage (Penggunaan Memori)
$memory_usage = memory_get_usage();
$memory_peak_usage = memory_get_peak_usage();
// Mengubah byte ke Megabyte (MB) agar mudah dibaca
$memory_usage_mb = round($memory_usage / 1048576, 2);
$memory_peak_mb = round($memory_peak_usage / 1048576, 2);

// 3. Disk Space (Kapasitas Penyimpanan)
$disk_free = disk_free_space("/");
$disk_total = disk_total_space("/");
$disk_used = $disk_total - $disk_free;
// Mengubah byte ke Gigabyte (GB)
$disk_free_gb = round($disk_free / 1073741824, 2);
$disk_used_gb = round($disk_used / 1073741824, 2);
$disk_total_gb = round($disk_total / 1073741824, 2);
$disk_usage_percent = round(($disk_used / $disk_total) * 100, 2);

// 4. Informasi Server Lainnya
$php_version = phpversion();
$server_software = $_SERVER['SERVER_SOFTWARE'];

// Menggabungkan semua data ke dalam satu array
$stats = [
    'cpu_load' => $cpu_load[0], // Ambil beban 1 menit terakhir untuk grafik
    'memory_usage' => $memory_usage_mb,
    'memory_peak' => $memory_peak_mb,
    'disk' => [
        'free' => $disk_free_gb,
        'used' => $disk_used_gb,
        'total' => $disk_total_gb,
        'percent' => $disk_usage_percent
    ],
    'server' => [
        'php_version' => $php_version,
        'web_server' => $server_software
    ],
    'timestamp' => date('H:i:s') // Waktu saat data diambil
];

// Mengubah array menjadi format JSON dan menampilkannya
echo json_encode($stats);
?>
