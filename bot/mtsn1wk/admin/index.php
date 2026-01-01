<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
// Cek apakah operator sudah login, jika tidak, redirect ke halaman login
if (!isset($_SESSION['operator_id'])) {
    header("Location:login.php");
    exit();
}

// --- AMBIL DATA DARI SESSION ---
$operator_id = $_SESSION['operator_id'];
$nama_lengkap = $_SESSION['nama_lengkap'];
$role = $_SESSION['role'];
// BARU: Ambil nama file foto dari session
$foto_profil = $_SESSION['foto_profil']; 

// --- FUNGSI UNTUK MENGAMBIL JUMLAH DATA (WIDGETS) ---
// Kita akan mengambil data jumlah berita, prestasi, dll.
// Diasumsikan Anda memiliki tabel-tabel ini. Jika belum, angka akan menjadi 0.

// Jumlah Calon Siswa (asumsi nama tabel: calon_siswa)
$result_calon_siswa = $conn->query("SELECT COUNT(id) as total FROM calon_siswa");
$total_calon_siswa = ($result_calon_siswa && $result_calon_siswa->num_rows > 0) ? $result_calon_siswa->fetch_assoc()['total'] : 0;

// Jumlah Berita (asumsi nama tabel: berita)
$result_berita = $conn->query("SELECT COUNT(id) as total FROM berita");
$total_berita = ($result_berita && $result_berita->num_rows > 0) ? $result_berita->fetch_assoc()['total'] : 0;

// Jumlah Prestasi (asumsi nama tabel: prestasi_siswa)
$result_prestasi = $conn->query("SELECT COUNT(id) as total FROM prestasi");
$total_prestasi = ($result_prestasi && $result_prestasi->num_rows > 0) ? $result_prestasi->fetch_assoc()['total'] : 0;

// Jumlah Galeri (asumsi nama tabel: galeri)
$result_galeri = $conn->query("SELECT COUNT(id) as total FROM galeri");
$total_galeri = ($result_galeri && $result_galeri->num_rows > 0) ? $result_galeri->fetch_assoc()['total'] : 0;

// Jumlah Operator
$result_operator = $conn->query("SELECT COUNT(id) as total FROM operator_madrasah");
$total_operator = ($result_operator && $result_operator->num_rows > 0) ? $result_operator->fetch_assoc()['total'] : 0;

// Jumlah Pesan
$result_pesan = $conn->query("SELECT COUNT(id) as total FROM pesan_kontak");
$total_pesan = ($result_pesan && $result_pesan->num_rows > 0) ? $result_pesan->fetch_assoc()['total'] : 0;

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - MTsN 1 Way Kanan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --sidebar-active: #34495e;
            --main-bg: #f4f7f6;
            --text-color: #333;
            --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--main-bg);
            display: flex;
        }

        /* --- SIDEBAR STYLE --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
        }

        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }

        .sidebar-header h3 {
            font-weight: 600;
        }

        .sidebar-nav {
            flex-grow: 1;
            list-style: none;
            padding-top: 20px;
        }

        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 15px;
        }

        .sidebar-nav li a i {
            width: 30px;
            font-size: 18px;
            margin-right: 10px;
        }
        
        .sidebar-nav li a:hover,
        .sidebar-nav li.active a {
            background-color: var(--sidebar-active);
        }

        /* --- MAIN CONTENT STYLE --- */
        .main-content {
            margin-left: 260px;
            width: calc(100% - 260px);
            padding: 20px;
            transition: all 0.3s ease;
        }

        /* --- HEADER STYLE --- */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
        }

        .header-title h1 {
            font-size: 24px;
            font-weight: 600;
            color: var(--text-color);
        }

        .user-profile {
            display: flex;
            align-items: center;
        }

        .user-profile img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }

        .user-profile .user-info p {
            line-height: 1.2;
        }

        .user-profile .user-info .user-name {
            font-weight: 600;
        }

        .user-profile .user-info .user-role {
            font-size: 13px;
            color: #777;
        }

        .logout-link {
            margin-left: 20px;
            color: #e74c3c;
            text-decoration: none;
            font-size: 20px;
            transition: color 0.3s ease;
        }

        .logout-link:hover {
            color: #c0392b;
        }
        
        /* --- STATS WIDGETS --- */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        
        .stat-card {
            background-color: #fff;
            padding: 25px;
            border-radius: 8px;
            box-shadow: var(--card-shadow);
            display: flex;
            align-items: center;
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }

        .stat-card .icon-container {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-right: 20px;
            font-size: 24px;
            color: #fff;
        }

        .stat-card:nth-child(1) .icon-container { background-color: #3498db; } /* Biru */
        .stat-card:nth-child(2) .icon-container { background-color: #2ecc71; } /* Hijau */
        .stat-card:nth-child(3) .icon-container { background-color: #e67e22; } /* Oranye */
        .stat-card:nth-child(4) .icon-container { background-color: #9b59b6; } /* Ungu */
        .stat-card:nth-child(5) .icon-container { background-color: #02c4dd; } /* Ungu */
        .stat-card:nth-child(6) .icon-container { background-color: #a30096; }
        
        .stat-card .info h3 {
            font-size: 28px;
            font-weight: 700;
        }

        .stat-card .info p {
            color: #777;
        }

        /* --- RESPONSIVE DESIGN --- */
        @media (max-width: 992px) {
            .sidebar {
                width: 70px;
            }
            .sidebar-header h3,
            .sidebar-nav li a span {
                display: none;
            }
            .main-content {
                margin-left: 70px;
                width: calc(100% - 70px);
            }
        }
        @media (max-width: 768px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }
        }

    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li class="active"><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <div class="header-title">
                <h1>Dashboard</h1>
            </div>
<div class="user-profile">
    <img src="uploads/operator/<?php echo htmlspecialchars($foto_profil); ?>" alt="Foto Profil">
    <div class="user-info">
        <p class="user-name"><?php echo htmlspecialchars($nama_lengkap); ?></p>
        <p class="user-role"><?php echo htmlspecialchars(ucfirst($role)); ?></p>
    </div>
    <a href="logout.php" class="logout-link" title="Logout">
        <i class="fas fa-sign-out-alt"></i>
    </a>
</div>
        </header>

        <section class="content">
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-newspaper"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_berita; ?></h3>
                        <p>Total Berita</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_prestasi; ?></h3>
                        <p>Total Prestasi</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-images"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_galeri; ?></h3>
                        <p>Total Galeri</p>
                    </div>
                </div>

                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-user-shield"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_operator; ?></h3>
                        <p>Total Operator</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-user-graduate"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_calon_siswa; ?></h3>
                        <p>Total Calon Siswa</p>
                    </div>
                </div>
                
                <div class="stat-card">
                    <div class="icon-container">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <div class="info">
                        <h3><?php echo $total_pesan; ?></h3>
                        <p>Total Pesan</p>
                    </div>
                </div>
                
            </div>
            
            </section>
    </main>

</body>
</html>