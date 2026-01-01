<?php
// Pastikan session sudah dimulai di halaman yang memanggil file ini
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Cek jika admin belum login, redirect ke halaman login
if (!isset($_SESSION['admin_id'])) {
    header('Location: auth.php');
    exit();
}

$admin_nama = $_SESSION['admin_nama'] ?? 'Admin';
// Baris ini mengambil nama file yang sedang dibuka (misal: "index.php")
$current_page = basename($_SERVER['PHP_SELF']); 
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <!-- Head content akan ada di file utama -->
    <style>
        :root {
            --primary-color: #0d47a1;
            --primary-light: #e3f2fd;
            --secondary-color: #4fc3f7;
            --bg-color: #f4f7fc;
            --text-dark: #333;
            --text-light: #777;
            --sidebar-width: 260px;
        }

        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            width: var(--sidebar-width);
            background-color: white;
            padding: 20px;
            box-shadow: 2px 0 15px rgba(0,0,0,0.05);
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease;
            z-index: 1000;
        }

        .sidebar-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
        }

        .sidebar-header .logo-icon {
            font-size: 2rem;
            color: var(--primary-color);
            margin-right: 15px;
        }

        .sidebar-header h4 {
            margin: 0;
            font-weight: 700;
            color: var(--text-dark);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0;
            margin: 0;
            flex-grow: 1;
        }

        .sidebar-menu li a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: var(--text-light);
            text-decoration: none;
            border-radius: 8px;
            margin-bottom: 5px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .sidebar-menu li a .bi {
            font-size: 1.2rem;
            margin-right: 15px;
            width: 20px;
            text-align: center;
        }

        .sidebar-menu li a:hover {
            background-color: var(--primary-light);
            color: var(--primary-color);
        }

        .sidebar-menu li a.active {
            background-color: var(--primary-color);
            color: white;
            box-shadow: 0 4px 10px rgba(13, 71, 161, 0.3);
        }

        .sidebar-footer {
            margin-top: auto;
        }

        .sidebar-footer a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #d32f2f; /* Merah untuk logout */
            text-decoration: none;
            border-radius: 8px;
            font-weight: 500;
            transition: background-color 0.3s ease;
        }

        .sidebar-footer a:hover {
            background-color: #ffebee;
        }
        
        .sidebar-footer .bi {
            font-size: 1.2rem;
            margin-right: 15px;
        }
    </style>
</head>
<body>
    <nav class="sidebar" id="sidebar">
        <div class="sidebar-header mt-3">
            <i class="bi bi-speedometer2 logo-icon"></i>
            <h4>Panel Admin</h4>
        </div>
        <ul class="sidebar-menu">
            <!-- Logika PHP ditambahkan di class untuk mengecek halaman aktif -->
            <li><a href="index.php" class="<?php echo ($current_page == 'index.php') ? 'active' : ''; ?>"><i class="bi bi-grid-1x2-fill"></i> Dashboard</a></li>
            <li><a href="kelola_berita.php" class="<?php echo ($current_page == 'kelola_berita.php') ? 'active' : ''; ?>"><i class="bi bi-newspaper"></i> Kelola Berita</a></li>
            <li><a href="kelola_admin.php" class="<?php echo ($current_page == 'kelola_admin.php') ? 'active' : ''; ?>"><i class="bi bi-person-fill-gear"></i> Kelola Admin</a></li>
            <li><a href="kelola_pejabat.php" class="<?php echo ($current_page == 'kelola_pejabat.php') ? 'active' : ''; ?>"><i class="bi bi-person-fill"></i> Kelola Pejabat</a></li>
            <li><a href="kelola_galeri.php" class="<?php echo ($current_page == 'kelola_galeri.php') ? 'active' : ''; ?>"><i class="bi bi-images"></i> Galeri</a></li>
            <li><a href="kelola_laporan.php" class="<?php echo ($current_page == 'kelola_laporan.php') ? 'active' : ''; ?>"><i class="bi bi-chat-left-text-fill"></i> Aduan Masyarakat</a></li>
            <li><a href="kelola_anggaran.php" class="<?php echo ($current_page == 'kelola_anggaran.php') ? 'active' : ''; ?>"><i class="bi bi-currency-exchange"></i> Anggaran</a></li>
            <li><a href="kelola_anggaran.php" class="<?php echo ($current_page == 'kelola_anggaran_rincian.php') ? 'active' : ''; ?>"><i class="bi bi-currency-exchange"></i> Anggaran</a></li>
        </ul>
        <div class="sidebar-footer">
            <a href="logout.php"><i class="bi bi-box-arrow-left"></i> Log Out</a>
        </div>
    </nav>
</body>
</html>

