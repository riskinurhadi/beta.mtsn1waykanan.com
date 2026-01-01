<?php
require_once 'config.php';

// // --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'performa';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}


// --- BARU: Mengambil Informasi Server & PHP ---
$server_info = [
    'php_version' => phpversion(),
    'db_server_version' => $conn->server_info,
    'web_server' => $_SERVER['SERVER_SOFTWARE'],
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time') . ' detik',
    'upload_max_filesize' => ini_get('upload_max_filesize')
];

// --- BARU: Membaca Log Kesalahan ---
$error_log_content = 'Tidak dapat membaca file log atau file kosong.';
$error_log_path = ini_get('error_log');
if ($error_log_path && is_readable($error_log_path)) {
    $log_lines = file($error_log_path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($log_lines) {
        // Ambil 20 baris terakhir
        $error_log_content = implode("\n", array_slice($log_lines, -20));
    }
}

// --- BARU: Memeriksa Izin Direktori ---
$writable_dirs = [
    'uploads/',
    'uploads/berita/',
    'uploads/galeri/',
    'uploads/guru/',
    'uploads/operator/',
    'uploads/prestasi/',
    'uploads/struktur/',
    'uploads/tata_tertib/'
];

// --- Mengambil Status Tabel Database ---
$db_tables_status = [];
$sql_db_status = "SHOW TABLE STATUS";
if ($result_db_status = $conn->query($sql_db_status)) {
    while($row = $result_db_status->fetch_assoc()) {
        $db_tables_status[] = $row;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performa Website - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Memuat library Chart.js untuk grafik -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--warning-color:#f39c12;--secondary-color:#6c757d;--info-color:#3498db}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}
        .sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}
        .sidebar-header h3{font-weight:600;color:#fff}
        .sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}
        .sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}
        .sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}
        .sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}
        .sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}
        .main-content{margin-left:260px;width:calc(100% - 260px);padding:20px;transition:all .3s ease}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
        .page-header h1{font-size:24px;font-weight:600}
        .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px}
        .stat-card{background-color:#fff;padding:25px;border-radius:8px;box-shadow:var(--card-shadow)}
        .stat-card-header{display:flex;align-items:center;color:#777;margin-bottom:10px}
        .stat-card-header i{font-size:20px;margin-right:10px}
        .stat-card-body h2{font-size:2.2rem;font-weight:700;color:var(--text-color)}
        .stat-card-body p{color:#999}
        .chart-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);margin-top:30px}
        .progress-bar{width:100%;background-color:#e9ecef;border-radius:5px;height:20px;overflow:hidden;margin-top:5px}
        .progress-bar-inner{height:100%;background-color:var(--primary-color);border-radius:5px;transition:width .5s ease-in-out;text-align:center;color:#fff;font-size:12px;line-height:20px}
        .sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}
        .sidebar-dropdown.open .dropdown-menu{display:block}
        .dropdown-menu li a{padding-left:65px}
        .dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}
        .sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}
        .notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 30px; margin-top: 30px; }
        .info-card { background-color: #fff; border-radius: 8px; box-shadow: var(--card-shadow); }
        .info-card-header { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; }
        .info-card-header h3 { font-size: 18px; font-weight: 600; margin: 0; }
        .info-card-body { padding: 20px; }
        .info-list { list-style: none; padding: 0; }
        .info-list li { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #f5f5f5; }
        .info-list li:last-child { border-bottom: none; }
        .info-list .label { color: #777; }
        .info-list .value { font-weight: 500; }
        .error-log-box { background-color: #2c3e50; color: #ecf0f1; font-family: monospace; font-size: 13px; padding: 15px; border-radius: 6px; height: 300px; overflow: auto; white-space: pre; }
        .status-ok { color: var(--primary-color); }
        .status-error { color: var(--danger-color); }
        .btn-action{padding:8px 16px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-clear-cache{background-color:var(--warning-color)}
        .table-container{overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:10px 15px;text-align:left;border-bottom:1px solid #f0f0f0;white-space:nowrap}
        .btn-phpinfo{background-color:var(--info-color)}
        .btn-optimize{background-color:var(--primary-color); padding: 4px 8px; font-size: 12px;}
        .table-container{overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:10px 15px;text-align:left;border-bottom:1px solid #f0f0f0;white-space:nowrap}
        
        @media (max-width: 1200px) { .info-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
            <!-- ... (Menu sidebar Anda yang lain) ... -->
            <!-- Menambahkan menu baru untuk Performa -->
            <li class="<?php echo ($halaman_aktif == 'performa') ? 'active' : ''; ?>"><a href="performa.php"><i class="fas fa-server"></i><span>Performa Web</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
    
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Monitor Performa Website</h1>
        </header>

        <section class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-card-header"><i class="fas fa-microchip"></i><span>Beban CPU (1 Menit)</span></div>
                    <div class="stat-card-body"><h2 id="cpuLoad">Memuat...</h2><p>Rata-rata proses</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><i class="fas fa-memory"></i><span>Penggunaan Memori</span></div>
                    <div class="stat-card-body"><h2 id="memoryUsage">Memuat...</h2><p>Peak: <span id="memoryPeak">...</span> MB</p></div>
                </div>
                <div class="stat-card">
                    <div class="stat-card-header"><i class="fas fa-hdd"></i><span>Kapasitas Disk</span></div>
                    <div class="stat-card-body">
                        <h2 id="diskPercent">Memuat...</h2>
                        <div class="progress-bar"><div id="diskProgressBar" class="progress-bar-inner"></div></div>
                        <p style="font-size: 13px; margin-top: 5px;">Terpakai: <span id="diskUsed">...</span> GB dari <span id="diskTotal">...</span> GB</p>
                    </div>
                </div>
            </div>

            <div class="chart-container">
                <canvas id="performanceChart"></canvas>
            </div>
            
            

            <!-- PERUBAHAN: Menambahkan Grid Informasi Baru -->
            <div class="info-grid">
                <!-- Kolom Informasi Server & PHP -->
                <div class="info-card">
                    <div class="info-card-header"><h3><i class="fas fa-info-circle"></i> Informasi Server & PHP</h3></div>
                    <div class="info-card-body">
                        <ul class="info-list">
                            <li><span class="label">Versi PHP</span><span class="value"><?php echo htmlspecialchars($server_info['php_version']); ?></span></li>
                            <li><span class="label">Server Database</span><span class="value"><?php echo htmlspecialchars($server_info['db_server_version']); ?></span></li>
                            <li><span class="label">Web Server</span><span class="value"><?php echo htmlspecialchars($server_info['web_server']); ?></span></li>
                            <li><span class="label">Batas Memori PHP</span><span class="value"><?php echo htmlspecialchars($server_info['memory_limit']); ?></span></li>
                            <li><span class="label">Waktu Eksekusi Maks.</span><span class="value"><?php echo htmlspecialchars($server_info['max_execution_time']); ?></span></li>
                            <li><span class="label">Ukuran Upload Maks.</span><span class="value"><?php echo htmlspecialchars($server_info['upload_max_filesize']); ?></span></li>
                        </ul>
                    </div>
                </div>

                <!-- Kolom Status Direktori -->
                <div class="info-card">
                    <div class="info-card-header"><h3><i class="fas fa-folder-check"></i> Status Direktori</h3></div>
                    <div class="info-card-body">
                        <ul class="info-list">
                            <?php foreach ($writable_dirs as $dir): ?>
                            <li>
                                <span class="label"><?php echo $dir; ?></span>
                                <?php if (is_writable($dir)): ?>
                                    <span class="value status-ok"><i class="fas fa-check-circle"></i> Writable</span>
                                <?php else: ?>
                                    <span class="value status-error"><i class="fas fa-times-circle"></i> Not Writable</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
            
            <!-- Bagian Log Kesalahan -->
            <!--<div class="info-card" style="margin-top: 30px;">-->
            <!--    <div class="info-card-header"><h3><i class="fas fa-bug"></i> Log Kesalahan PHP (20 Baris Terakhir)</h3></div>-->
            <!--    <div class="info-card-body">-->
            <!--        <div class="error-log-box">-->
            <!--            <?php echo htmlspecialchars($error_log_content); ?>-->
            <!--        </div>-->
            <!--    </div>-->
            <!--</div>-->
            
            
            <div class="info-grid">
                <div class="info-card">
                    <div class="info-card-header"><h3><i class="fas fa-bolt"></i> Tindakan Cepat</h3></div>
                    <div class="info-card-body">
                        <p style="color: #777; margin-bottom: 15px;">Gunakan tombol ini jika Anda baru saja mengubah file kode dan perubahan tidak muncul.</p>
                        <button class="btn-action btn-clear-cache" id="clearCacheBtn"><i class="fas fa-broom"></i> Kosongkan OPcache</button>
                    </div>
                </div>

                <div class="info-card">
                    <div class="info-card-header"><h3><i class="fas fa-folder-check"></i> Keamanan Direktori</h3></div>
                    <div class="info-card-body">
                        <ul class="info-list">
                            <?php foreach ($writable_dirs as $dir): ?>
                            <li>
                                <span class="label"><?php echo $dir; ?></span>
                                <?php if (is_writable($dir)): ?>
                                    <span class="value status-ok"><i class="fas fa-check-circle"></i> Secure</span>
                                <?php else: ?>
                                    <span class="value status-error"><i class="fas fa-times-circle"></i> Not Secure</span>
                                <?php endif; ?>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="info-card" style="margin-top: 30px;">
                <div class="info-card-header"><h3><i class="fas fa-database"></i> Diagnostik Database</h3></div>
                <div class="info-card-body table-container">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Nama Tabel</th>
                                <th>Jumlah Baris</th>
                                <th>Ukuran Data</th>
                                <th>Ukuran Indeks</th>
                                <th>Collation</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($db_tables_status as $table): ?>
                            <tr>
                                <td><?php echo $table['Name']; ?></td>
                                <td><?php echo $table['Rows']; ?></td>
                                <td><?php echo round($table['Data_length'] / 1024, 2); ?> KB</td>
                                <td><?php echo round($table['Index_length'] / 1024, 2); ?> KB</td>
                                <td><?php echo $table['Collation']; ?></td>
                                <td><button class="btn-action btn-optimize" onclick="optimizeTable('<?php echo $table['Name']; ?>')">Optimalkan</button></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="info-card" style="margin-top: 30px;">
                <div class="info-card-header"><h3><i class="fas fa-bug"></i> Notifikasi Error dan Bug (20 Baris Terakhir)</h3></div>
                <div class="info-card-body">
                    <div class="error-log-box">
                        <?php echo htmlspecialchars($error_log_content); ?>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    
     <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            const performanceChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [], // Akan diisi dengan waktu
                    datasets: [{
                        label: 'Beban CPU (%)',
                        data: [],
                        borderColor: 'rgba(243, 156, 18, 0.8)',
                        backgroundColor: 'rgba(243, 156, 18, 0.1)',
                        fill: true,
                        yAxisID: 'y',
                    }, {
                        label: 'Memori (MB)',
                        data: [],
                        borderColor: 'rgba(52, 152, 219, 0.8)',
                        backgroundColor: 'rgba(52, 152, 219, 0.1)',
                        fill: true,
                        yAxisID: 'y1',
                    }]
                },
                options: {
                    responsive: true,
                    interaction: { mode: 'index', intersect: false, },
                    stacked: false,
                    scales: {
                        y: {
                            type: 'linear',
                            display: true,
                            position: 'left',
                            title: { display: true, text: 'CPU Load (%)' }
                        },
                        y1: {
                            type: 'linear',
                            display: true,
                            position: 'right',
                            title: { display: true, text: 'Memory (MB)' },
                            grid: { drawOnChartArea: false, },
                        },
                    }
                }
            });

            function updateStats() {
                fetch('server_stats.php')
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            console.error(data.error);
                            return;
                        }

                        // Update Kartu Statistik
                        document.getElementById('cpuLoad').textContent = data.cpu_load;
                        document.getElementById('memoryUsage').textContent = data.memory_usage + ' MB';
                        document.getElementById('memoryPeak').textContent = data.memory_peak;
                        document.getElementById('diskPercent').textContent = data.disk.percent + '%';
                        document.getElementById('diskUsed').textContent = data.disk.used;
                        document.getElementById('diskTotal').textContent = data.disk.total;
                        
                        const progressBar = document.getElementById('diskProgressBar');
                        progressBar.style.width = data.disk.percent + '%';
                        progressBar.textContent = data.disk.percent + '%';

                        // Update Grafik
                        const chart = performanceChart.data;
                        if (chart.labels.length > 20) { // Batasi data agar grafik tidak terlalu padat
                            chart.labels.shift();
                            chart.datasets[0].data.shift();
                            chart.datasets[1].data.shift();
                        }
                        chart.labels.push(data.timestamp);
                        chart.datasets[0].data.push(data.cpu_load * 100); // Dikalikan 100 untuk persentase
                        chart.datasets[1].data.push(data.memory_usage);
                        performanceChart.update();
                    })
                    .catch(error => console.error('Gagal mengambil data performa:', error));
            }

            // Panggil pertama kali, lalu ulangi setiap 3 detik
            updateStats();
            setInterval(updateStats, 3000); 
            
            const clearCacheBtn = document.getElementById('clearCacheBtn');
            if (clearCacheBtn) {
                clearCacheBtn.addEventListener('click', function() {
                    // Untuk debugging, pastikan listener ini terpanggil
                    console.log('Tombol Kosongkan OPcache diklik!'); 

                    Swal.fire({
                        title: 'Mengosongkan Cache...',
                        text: 'Mohon tunggu sebentar.',
                        allowOutsideClick: false,
                        didOpen: () => { Swal.showLoading(); }
                    });

                    fetch('clear_cache.php', { method: 'POST' })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                Swal.fire('Berhasil!', data.message, 'success');
                            } else {
                                Swal.fire('Gagal!', data.message, 'error');
                            }
                        })
                        .catch(error => {
                            console.error('Error saat fetch:', error);
                            Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server.', 'error');
                        });
                });
            }
        });
        
        function optimizeTable(tableName) {
            Swal.fire({
                title: 'Mengoptimalkan Tabel...',
                text: `Mohon tunggu, tabel '${tableName}' sedang diproses.`,
                allowOutsideClick: false,
                didOpen: () => { Swal.showLoading(); }
            });

            const formData = new FormData();
            formData.append('table_name', tableName);

            fetch('optimize_table.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire('Berhasil!', data.message, 'success');
                } else {
                    Swal.fire('Gagal!', data.message, 'error');
                }
            })
            .catch(error => {
                Swal.fire('Error!', 'Terjadi kesalahan saat menghubungi server.', 'error');
            });
        }
    </script>
</body>
</html>
