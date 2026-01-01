<?php
// Memulai session di baris paling awal
session_start();

// Memanggil file koneksi dan sidebar
require 'koneksi.php';
require 'sidebar.php'; // sidebar.php akan mengecek sesi login

// ----- CONTOH PENGAMBILAN DATA UNTUK WIDGET -----
// Menghitung total berita
$result_berita = $koneksi->query("SELECT COUNT(*) AS total FROM tb_berita");
$total_berita = $result_berita->fetch_assoc()['total'];

// Menghitung total admin
$result_admin = $koneksi->query("SELECT COUNT(*) AS total FROM tb_admin");
$total_admin = $result_admin->fetch_assoc()['total'];

// Menghitung total aduan yang masih 'Menunggu'
$result_aduan = $koneksi->query("SELECT COUNT(*) AS total FROM tb_laporan WHERE status = 'Menunggu'");
$total_aduan_menunggu = $result_aduan->fetch_assoc()['total'];

// Mengambil 5 aduan terbaru yang statusnya 'Menunggu'
$query_aduan_terbaru = "SELECT id_laporan, judul_laporan, nama_pelapor, tanggal_laporan FROM tb_laporan WHERE status = 'Menunggu' ORDER BY tanggal_laporan DESC LIMIT 5";
$result_aduan_terbaru = $koneksi->query($query_aduan_terbaru);

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Menggunakan variabel dari sidebar.php */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .header-dashboard {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .header-dashboard .welcome-text h5 {
            margin: 0;
            font-weight: 600;
        }
        
        .header-dashboard .welcome-text p {
            margin: 0;
            color: var(--text-light);
            font-size: 0.9rem;
        }
        
        .header-dashboard .user-profile {
            display: flex;
            align-items: center;
            font-weight: 500;
        }

        .header-dashboard .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-left: 15px;
            object-fit: cover;
        }

        .stat-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            display: flex;
            align-items: center;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }

        .stat-card .icon-box {
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin-right: 20px;
        }
        
        .icon-box.bg-primary-light { background-color: #e3f2fd; color: #1e88e5; }
        .icon-box.bg-success-light { background-color: #e8f5e9; color: #43a047; }
        .icon-box.bg-warning-light { background-color: #fff8e1; color: #fdd835; }
        .icon-box.bg-danger-light { background-color: #fce4ec; color: #e53935; }

        .stat-card .info h6 {
            font-size: 1rem;
            color: var(--text-light);
            margin: 0;
        }

        .stat-card .info h4 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
        }
        
        .content-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            #menu-toggle {
                display: block !important;
            }
        }
        
        .aduan-terbaru-list {
    list-style: none;
    padding-left: 0;
}
.aduan-terbaru-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid #f0f0f0;
}
.aduan-terbaru-item:last-child {
    border-bottom: none;
}
.aduan-info .aduan-title {
    font-weight: 500;
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}
.aduan-info .aduan-meta {
    font-size: 0.85rem;
    color: var(--text-light);
}
    </style>
</head>
<body>

    <main class="main-content" id="main-content">
        <header class="header-dashboard">
            <div class="d-flex align-items-center">
                <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
                <div class="welcome-text">
                    <h5>Selamat Datang, <?php echo htmlspecialchars($admin_nama); ?>!</h5>
                    <p>Ini adalah ringkasan aktivitas website Anda.</p>
                </div>
            </div>
            <div class="user-profile">
                <span><?php echo htmlspecialchars($admin_nama); ?></span>
                <!-- Ganti dengan foto profil admin jika ada -->
                <img src="https://placehold.co/100x100/0d47a1/FFFFFF?text=A" alt="Admin">
            </div>
        </header>

        <!-- Stat Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="icon-box bg-primary-light"><i class="bi bi-newspaper"></i></div>
                    <div class="info">
                        <h6>Total<br>Berita</h6>
                        <h4><?php echo $total_berita; ?></h4>
                    </div>
                </div>
            </div>
             <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="icon-box bg-success-light"><i class="bi bi-person-fill-gear"></i></div>
                    <div class="info">
                        <h6>Jumlah<br>Admin</h6>
                        <h4><?php echo $total_admin; ?></h4>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <div class="icon-box bg-warning-light"><i class="bi bi-eye-fill"></i></div>
                    <div class="info">
                        <h6>Pengunjung<br>Hari Ini</h6>
                        <h4>1,250</h4>
                    </div>
                </div>
            </div>
<div class="col-md-6 col-xl-3">
    <div class="stat-card">
        <div class="icon-box bg-danger-light"><i class="bi bi-bell-fill"></i></div>
        <div class="info">
            <h6>Aduan<br>Menunggu</h6>
            <h4><?php echo $total_aduan_menunggu; ?></h4>
        </div>
    </div>
</div>
        </div>

<!-- Aduan Terbaru -->
<div class="row g-4 mt-2">
    <div class="col-12">
        <div class="content-card">
            <h5 class="mb-3">Aduan Terbaru Menunggu Tindakan</h5>
            <?php if($result_aduan_terbaru && $result_aduan_terbaru->num_rows > 0): ?>
                <ul class="aduan-terbaru-list">
                    <?php while($aduan = $result_aduan_terbaru->fetch_assoc()): ?>
                    <li class="aduan-terbaru-item">
                        <div class="aduan-info">
                            <div class="aduan-title"><?php echo htmlspecialchars($aduan['judul_laporan']); ?></div>
                            <div class="aduan-meta">
                                Oleh <?php echo htmlspecialchars($aduan['nama_pelapor']); ?> - <?php echo date('d M Y', strtotime($aduan['tanggal_laporan'])); ?>
                            </div>
                        </div>
                        <a href="kelola_laporan.php" class="btn btn-sm btn-outline-primary">Lihat</a>
                    </li>
                    <?php endwhile; ?>
                </ul>
                <!--<div class="text-center mt-3">-->
                <!--    <a href="kelola_laporan.php" class="btn btn-primary">Lihat Semua Aduan</a>-->
                <!--</div>-->
            <?php else: ?>
                <p class="text-center text-muted">Tidak ada aduan baru yang menunggu tindakan. Kerja bagus!</p>
            <?php endif; ?>
        </div>
    </div>
</div>

    </main>
    
    <script>
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
