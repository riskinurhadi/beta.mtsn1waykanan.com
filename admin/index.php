<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
  header("Location:login.php");
  exit();
}



// Tentukan domain yang diizinkan (bisa di hardcode atau ambil dari database)
$allowed_domain = 'mtsn1waykanan.com';
$current_domain = $_SERVER['HTTP_HOST'];

// Cek apakah domain mengandung string yang diizinkan
if (strpos($current_domain, $allowed_domain) === false) {
    // Jika dijalankan di domain lain, script berhenti
    http_response_code(403);
    die("Aplikasi ini tidak berlisensi untuk domain ini. Silakan hubungi developer.");
}




// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'dashboard';

// --- AMBIL DATA DARI SESSION ---
$operator_id = $_SESSION['operator_id'];
$nama_lengkap = $_SESSION['nama_lengkap'];
$role = $_SESSION['role'];
$foto_profil = $_SESSION['foto_profil'];

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
  $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}


$sql_count_notif = "SELECT COUNT(id) as total_notif FROM notifikasi_operator WHERE id_penerima = ? AND sudah_dibaca = FALSE";
$stmt_count_notif = $conn->prepare($sql_count_notif);
$stmt_count_notif->bind_param("i", $operator_id);
$stmt_count_notif->execute();
$result_count_notif = $stmt_count_notif->get_result();
$jumlah_notifikasi_baru = 0;
if ($result_count_notif) {
  $jumlah_notifikasi_baru = $result_count_notif->fetch_assoc()['total_notif'];
}
$stmt_count_notif->close();

// --- FUNGSI UNTUK MENGAMBIL JUMLAH DATA (WIDGETS) ---
$result_calon_siswa = $conn->query("SELECT COUNT(id) as total FROM calon_siswa");
$total_calon_siswa = ($result_calon_siswa && $result_calon_siswa->num_rows > 0) ? $result_calon_siswa->fetch_assoc()['total'] : 0;

$result_berita = $conn->query("SELECT COUNT(id) as total FROM berita");
$total_berita = ($result_berita && $result_berita->num_rows > 0) ? $result_berita->fetch_assoc()['total'] : 0;

$result_prestasi = $conn->query("SELECT COUNT(id) as total FROM prestasi");
$total_prestasi = ($result_prestasi && $result_prestasi->num_rows > 0) ? $result_prestasi->fetch_assoc()['total'] : 0;

$result_galeri = $conn->query("SELECT COUNT(id) as total FROM galeri");
$total_galeri = ($result_galeri && $result_galeri->num_rows > 0) ? $result_galeri->fetch_assoc()['total'] : 0;

$result_operator = $conn->query("SELECT COUNT(id) as total FROM operator_madrasah");
$total_operator = ($result_operator && $result_operator->num_rows > 0) ? $result_operator->fetch_assoc()['total'] : 0;

$result_pesan = $conn->query("SELECT COUNT(id) as total FROM pesan_kontak");
$total_pesan = ($result_pesan && $result_pesan->num_rows > 0) ? $result_pesan->fetch_assoc()['total'] : 0;

// --- BARU: Ambil 3 pesan masuk terakhir ---
$pesan_terbaru = [];
$sql_pesan_terbaru = "SELECT id, nama_pengirim, subjek, status FROM pesan_kontak ORDER BY tanggal_kirim DESC LIMIT 3";
if($result_pesan_terbaru = $conn->query($sql_pesan_terbaru)) {
  while($row = $result_pesan_terbaru->fetch_assoc()) {
    $pesan_terbaru[] = $row;
  }
}

// --- BARU: Ambil 3 berita terakhir ---
$berita_terbaru = [];
$sql_berita_terbaru = "SELECT id, judul, kategori FROM berita ORDER BY tanggal_publikasi DESC LIMIT 3";
if($result_berita_terbaru = $conn->query($sql_berita_terbaru)) {
  while($row = $result_berita_terbaru->fetch_assoc()) {
    $berita_terbaru[] = $row;
  }
}


// Maintenance Script
$maintenance_status = 'off'; // Nilai default jika tidak ditemukan
$sql_maintenance = "SELECT nilai_pengaturan FROM pengaturan WHERE nama_pengaturan = 'maintenance_mode'";
if ($result_maintenance = $conn->query($sql_maintenance)) {
  if ($result_maintenance->num_rows > 0) {
    $maintenance_status = $result_maintenance->fetch_assoc()['nilai_pengaturan'];
  }
}


$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin - MTsN 1 Way Kanan</title>
 <script>
    (function() {
        // Cek tema yang tersimpan di localStorage
        var theme = localStorage.getItem('theme');
        if (theme === 'dark') {
            // Terapkan class 'dark-mode' ke tag <html>
            document.documentElement.classList.add('dark-mode');
        }
        // Jika 'light' or null, tidak perlu melakukan apa-apa (default)
    })();
</script>
 	<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
 	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
 	<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
 
 	<style>
   	/* =================================================================
   	  BLOK CSS BARU - EFEK GLASSMORPHISM
   	 ================================================================= */
   	/* GANTI ':root' ANDA MENJADI 'html' */
html {
    --primary-color: #1dbe83;
    --primary-hover: #18a06e;
    --main-bg-gradient: linear-gradient(120deg, #f0f8ff 0%, #e6f7f2 100%);
    --text-color: #051a38; /* Biru tua / hampir hitam */
    --text-color-light: #5a6a85; /* Abu-abu kebiruan */
    
    /* Properti Kaca */
    --glass-bg: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.3);
    --blur-value: 10px;
    --card-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.1);
    --border-radius: 16px;

    /* Warna lain */
    --danger-color: #e74c3c;
    --warning-color: #f39c12;
    --info-color: #3498db;
}

/* =================================================================
   TAMBAHKAN BLOK CSS BARU INI
================================================================= 
*/

/* === DEFINISI VARIABEL DARK MODE === */
html.dark-mode {
    --primary-hover: #23e09c; /* Hijau lebih cerah saat hover */
    --main-bg-gradient: linear-gradient(120deg, #0a192f 0%, #172a45 100%); /* Latar belakang biru gelap */
    --text-color: #e6f1ff; /* Teks putih kebiruan */
    --text-color-light: #a8b2d1; /* Teks abu-abu kebiruan */
    
    /* Properti Kaca (Versi Gelap) */
    --glass-bg: rgba(20, 30, 50, 0.5); /* Kaca biru gelap transparan */
    --glass-border: rgba(255, 255, 255, 0.1); /* Border putih tipis */
    --card-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.3); /* Bayangan lebih gelap */
}

/* === STYLE TOMBOL TEMA === */
.theme-toggle-btn {
    background: none;
    border: none;
    color: var(--text-color-light);
    font-size: 20px; /* Samakan dengan ikon logout */
    cursor: pointer;
    padding: 5px 10px;
    border-radius: 8px;
    transition: color 0.3s ease, background-color 0.2s ease;
    margin-right: 5px; /* Jarak dari ikon logout */
}
.theme-toggle-btn:hover {
    background-color: rgba(0, 0, 0, 0.05); /* Hover halus */
    color: var(--text-color);
}
html.dark-mode .theme-toggle-btn:hover {
    background-color: rgba(255, 255, 255, 0.1); /* Hover di dark mode */
}

/* === PENYESUAIAN DARK MODE LAINNYA === */

/* 1. Blob Latar Belakang (agar tidak terlalu mencolok) */
html.dark-mode body::before,
html.dark-mode body::after {
    opacity: 0.1;
    filter: blur(200px);
}

/* 2. Alert Maintenance */
html.dark-mode .maintenance-alert {
    background: rgba(243, 156, 18, 0.2); /* Latar kaca kuning tua */
    color: #f39c12; /* Teks kuning cerah */
    border-color: rgba(243, 156, 18, 0.4);
}
html.dark-mode .maintenance-alert .close-btn {
    color: #f39c12;
}

/* 3. Tombol close toast (dari prompt sebelumnya) */
html.dark-mode .close-toast:hover {
    background-color: rgba(255, 255, 255, 0.15);
}

/* 4. Link di Chart */
html.dark-mode .chart-container p a {
    color: var(--primary-hover);
}

  	* {
    	margin: 0;
    	padding: 0;
    	box-sizing: border-box;
  	}

  	body {
    	font-family: 'Poppins', sans-serif;
    	background-image: var(--main-bg-gradient);
    	display: flex;
    	position: relative;
    	min-height: 100vh;
    	overflow: hidden; /* Mencegah blob membuat scroll aneh */
  	}

  	/* Dekorasi Blob di Latar Belakang */
  	body::before, body::after {
    	content: '';
    	position: fixed;
    	border-radius: 50%;
    	filter: blur(150px);
    	z-index: -1;
  	}
  	body::before { /* Blob ungu/pink (kanan atas) */
    	width: 400px;
    	height: 400px;
    	background: rgba(168, 120, 255, 0.2);
    	top: -100px;
    	right: -100px;
  	}
  	body::after { /* Blob hijau (kiri bawah) */
    	width: 300px;
    	height: 300px;
    	background: rgba(29, 190, 131, 0.15);
    	bottom: -50px;
    	left: -50px;
  	}

  	/* === Sidebar === */
  	.sidebar {
    	width: 260px;
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border-right: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	color: var(--text-color);
    	height: 100vh;
    	position: fixed;
    	left: 0;
    	top: 0;
    	display: flex;
    	flex-direction: column;
    	transition: width .3s ease;
    	z-index: 1000;
  	}
  	.sidebar-header {
    	padding: 20px;
    	text-align: center;
    	border-bottom: 1px solid var(--glass-border);
    	flex-shrink: 0;
  	}
  	.sidebar-header h3 {
    	font-weight: 600;
    	color: var(--text-color);
  	}
  	.sidebar-nav {
    	flex-grow: 1;
    	list-style: none;
    	padding: 20px 0;
    	overflow-y: auto;
  	}
  	.sidebar-nav li a {
    	display: flex;
    	align-items: center;
    	padding: 15px 20px;
    	color: var(--text-color-light);
    	text-decoration: none;
    	transition: background-color .3s ease, color .3s ease;
    	font-size: 15px;
    	position: relative;
    	border-radius: 8px;
    	margin: 2px 10px;
  	}
  	.sidebar-nav li a i {
    	width: 30px;
    	font-size: 18px;
    	margin-right: 10px;
    	text-align: center;
  	}
  	.sidebar-nav li a:hover,
  	.sidebar-nav li.active > a {
    	background-color: rgba(255, 255, 255, 0.4);
    	color: var(--text-color);
    	font-weight: 500;
  	}
  	.sidebar-nav li.active > a::before {
    	content: '';
    	position: absolute;
    	left: 0;
    	top: 50%;
    	transform: translateY(-50%);
    	height: 70%;
    	width: 4px;
    	background-color: var(--primary-color);
    	border-radius: 0 4px 4px 0;
  	}

  	/* === Main Content === */
  	.main-content {
    	margin-left: 260px;
    	width: calc(100% - 260px);
    	padding: 20px;
    	transition: all .3s ease;
    	height: 100vh; /* PENTING: Membuat konten bisa scroll */
    	overflow-y: auto; /* PENTING: Scrollbar hanya di sini */
  	}

  	/* === Header === */
  	.header {
    	display: flex;
    	justify-content: space-between;
    	align-items: center;
    	padding: 15px 20px;
    	margin-bottom: 30px;
    	
    	/* Terapkan Efek Kaca */
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	border-radius: var(--border-radius);
  	}
  	.header-title h2 {
    	font-size: 22px;
    	font-weight: 600;
    	color: var(--text-color);
    	margin: 0;
  	}
  	.header-title p {
    	margin: 0;
    	color: var(--text-color-light);
    	font-size: 15px;
  	}

  	/* Profile & Logout */
  	.user-profile {
    	display: flex;
    	align-items: center;
  	}
  	.user-profile .user-info .user-name {
    	font-weight: 600;
    	color: var(--text-color);
  	}
  	.user-profile .user-info .user-role {
    	font-size: 13px;
    	color: var(--text-color-light);
  	}
  	.logout-link {
    	margin-left: 20px;
    	color: var(--danger-color);
    	text-decoration: none;
    	font-size: 20px;
    	transition: color .3s ease;
  	}
  	.logout-link:hover {
    	color: #c0392b;
  	}
  	.profile-link {
    	display: flex;
    	align-items: center;
    	text-decoration: none;
    	color: inherit;
    	transition: background-color 0.2s ease;
    	padding: 5px 10px;
    	border-radius: 8px;
    	margin-right: 10px;
  	}
  	.profile-link:hover {
    	background-color: rgba(255, 255, 255, 0.2);
  	}
  	.profile-pic-container {
    	position: relative;
    	margin-right: 10px;
  	}
  	.profile-pic-container img {
    	width: 40px;
    	height: 40px;
    	border-radius: 50%;
    	object-fit: cover;
    	border: 2px solid var(--glass-border);
  	}
  	.profile-notification-dot {
    	position: absolute;
    	top: 0;
    	right: 0;
    	width: 12px;
    	height: 12px;
    	background-color: var(--warning-color);
    	border-radius: 50%;
    	border: 2px solid #fff;
  	}

  	/* === Kartu Statistik === */
  	.stats-grid {
    	display: grid;
    	grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    	gap: 20px;
  	}
  	.stat-card {
    	padding: 25px;
    	display: flex;
    	align-items: center;
    	transition: transform .3s ease;
    	
    	/* Terapkan Efek Kaca */
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	border-radius: var(--border-radius);
  	}
  	.stat-card:hover {
    	transform: translateY(-5px);
    	box-shadow: 0 12px 35px 0 rgba(31, 38, 135, 0.15);
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
  	/* Update warna ikon agar lebih solid/terlihat di atas kaca */
  	.stat-card:nth-child(1) .icon-container{background-color:rgba(52, 152, 219, 0.8)}
  	.stat-card:nth-child(2) .icon-container{background-color:rgba(46, 204, 113, 0.8)}
  	.stat-card:nth-child(3) .icon-container{background-color:rgba(230, 126, 34, 0.8)}
  	.stat-card:nth-child(4) .icon-container{background-color:rgba(155, 89, 182, 0.8)}
  	.stat-card:nth-child(5) .icon-container{background-color:rgba(2, 196, 221, 0.8)}
  	.stat-card:nth-child(6) .icon-container{background-color:rgba(163, 0, 150, 0.8)}

  	.stat-card .info h3 {
    	font-size: 28px;
    	font-weight: 700;
    	color: var(--text-color);
  	}
  	.stat-card .info p {
    	color: var(--text-color-light);
  	}

  	/* === Dropdown Sidebar === */
  	.sidebar-dropdown .dropdown-menu {
    	display: none;
    	list-style: none;
    	padding: 0;
    	margin: 0;
    	background-color: rgba(255, 255, 255, 0.2); /* Kaca di dalam kaca */
  	}
  	.sidebar-dropdown.open .dropdown-menu {
    	display: block;
  	}
  	.dropdown-menu li a {
    	padding-left: 65px;
  	}
  	.dropdown-icon {
    	position: absolute;
    	right: 20px;
    	transition: transform .3s ease;
  	}
  	.sidebar-dropdown.open > a .dropdown-icon {
    	transform: rotate(180deg);
  	}
  	.notification-dot {
    	position: absolute;
    	right: 15px;
    	top: 50%;
    	transform: translateY(-50%);
    	width: 10px;
    	height: 10px;
    	background-color: var(--warning-color);
    	border-radius: 50%;
    	border: 2px solid #fff; /* Sesuaikan border agar kontras */
  	}

  	/* === Grid Aktivitas & Chart === */
  	.dashboard-grid {
    	display: grid;
    	grid-template-columns: repeat(2, 1fr);
    	gap: 30px;
    	margin-top: 30px;
  	}
  	.activity-card {
    	/* Terapkan Efek Kaca */
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	border-radius: var(--border-radius);
    	overflow: hidden; /* Agar list tidak keluar dari radius */
  	}
  	.activity-card-header {
    	padding: 15px 20px;
    	border-bottom: 1px solid var(--glass-border);
    	display: flex;
    	justify-content: space-between;
    	align-items: center;
  	}
  	.activity-card-header h3 {
    	font-size: 18px;
    	font-weight: 600;
    	margin: 0;
    	color: var(--text-color);
  	}
  	.activity-card-header a {
    	font-size: 14px;
    	text-decoration: none;
    	color: var(--primary-color);
    	font-weight: 500;
  	}
  	.activity-list {
    	list-style: none;
   	}
  	.activity-list li {
    	display: flex;
    	align-items: center;
    	padding: 15px 20px;
    	border-bottom: 1px solid var(--glass-border);
  	}
  	.activity-list li:last-child {
    	border-bottom: none;
  	}
  	.activity-list .icon {
    	font-size: 18px;
    	color: var(--text-color-light);
    	margin-right: 15px;
  	}
  	.activity-list .details p {
    	margin: 0;
    	line-height: 1.4;
  	}
  	.activity-list .details .title {
    	font-weight: 500;
    	color: var(--text-color);
  	}
  	.activity-list .details .meta {
    	font-size: 13px;
    	color: var(--text-color-light);
  	}
  	.activity-list .badge-baru {
    	font-size: 10px;
    	padding: 3px 8px;
    	margin-left: auto;
    	background-color: var(--info-color);
    	color: #fff;
    	border-radius: 20px;
  	}

  	.chart-container {
    	padding: 20px;
    	margin-top: 30px;
    	/* Terapkan Efek Kaca */
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	border-radius: var(--border-radius);
  	}
  	.chart-container h3 {
   		color: var(--text-color);
   		margin-bottom: 15px;
  	}
  	.chart-container p {
  		color: var(--text-color-light);
  		margin-top: 20px;
  	}
  	.chart-container p a {
  		color: var(--primary-color);
  		font-weight: 500;
  		text-decoration: none;
  	}

  	/* === Maintenance Alert === */
  	.maintenance-alert {
    	display: flex;
    	align-items: center;
    	padding: 15px 20px;
    	border-radius: var(--border-radius);
    	margin-bottom: 30px;
    	
    	/* Efek Kaca versi Kuning/Warning */
    	background: rgba(255, 243, 205, 0.5); /* Kaca Kuning */
    	color: #664d03;
    	border: 1px solid rgba(255, 236, 181, 0.7);
    	box-shadow: var(--card-shadow);
    	backdrop-filter: blur(8px);
    	-webkit-backdrop-filter: blur(8px);
  	}
  	.maintenance-alert .icon {
    	font-size: 24px;
    	margin-right: 15px;
  	}
  	.maintenance-alert .message {
    	flex-grow: 1;
    	font-size: 15px;
  	}
  	.maintenance-alert .close-btn {
    	background: none;
    	border: none;
    	font-size: 20px;
    	color: #664d03;
    	cursor: pointer;
    	opacity: 0.7;
    	transition: opacity 0.2s ease;
  	}
  	.maintenance-alert .close-btn:hover {
    	opacity: 1;
  	}

  	/* === Banner Kemerdekaan === */
  	.independence-banner-container {
    	position: relative;
    	margin-bottom: 30px;
    	overflow: hidden;
    	
    	/* Terapkan Efek Kaca */
    	background: var(--glass-bg);
    	backdrop-filter: blur(var(--blur-value));
    	-webkit-backdrop-filter: blur(var(--blur-value));
    	border: 1px solid var(--glass-border);
    	box-shadow: var(--card-shadow);
    	border-radius: var(--border-radius);
  	}
  	.banner-slide {
    	display: none; 
  	}
  	.banner-slide img {
    	width: 100%;
    	height: 180px; 
    	object-fit: cover;
    	vertical-align: middle;
    	border-radius: var(--border-radius); /* Pastikan gambar juga tumpul */
  	}
  	.fade {
    	animation-name: fade;
    	animation-duration: 1.5s;
  	}
  	@keyframes fade {
    	from {opacity: .4}
    	to {opacity: 1}
  	}
  	.banner-dots {
    	position: absolute;
    	bottom: 15px;
    	left: 50%;
    	transform: translateX(-50%);
  	}
  	.dot {
    	height: 10px; /* Sedikit lebih kecil */
    	width: 10px;
    	margin: 0 4px;
    	background-color: rgba(0, 0, 0, 0.2); /* Titik lebih gelap */
    	border-radius: 50%;
    	display: inline-block;
    	cursor: pointer;
    	transition: background-color 0.3s ease;
  	}
  	.dot.active {
    	background-color: var(--primary-color); /* Titik aktif pakai warna primer */
  	}

  	/* === Media Queries (Responsif) === */
  	@media (max-width: 1200px) {
    	.dashboard-grid {
      	grid-template-columns: 1fr;
    	}
  	}
  	@media (max-width: 992px) {
    	.sidebar {
      	width: 70px;
    	}
    	.sidebar-header h3,
    	.sidebar-nav li a span,
    	.dropdown-icon {
      	display: none;
    	}
    	.main-content {
      	margin-left: 70px;
      	width: calc(100% - 70px);
    	}
    	.sidebar-dropdown .dropdown-menu {
      	position: absolute;
      	left: 70px;
      	top: 0;
      	background-color: var(--glass-bg); /* Tetap glass */
      	width: 200px;
      	box-shadow: 5px 0 15px rgba(0, 0, 0, .2);
      	border-radius: 0 var(--border-radius) var(--border-radius) 0;
    	}
    	.dropdown-menu li a {
      	padding-left: 20px;
    	}
    	.dropdown-menu li a span {
      	display: inline-block !important;
    	}
  	}
  	@media (max-width: 768px) {
    	.header {
      	flex-direction: column;
      	align-items: flex-start;
      	gap: 15px;
    	}
    	.banner-slide img {
      	height: 150px; /* Sesuaikan tinggi banner di mobile */
    	}
  	}
  	
  	
  	
  	
  	
  	/* =================================================================
   CSS UNTUK KARTU NOTIFIKASI (TOAST)
   ================================================================= */

#floating-toast {
    position: fixed; /* Tetap di layar saat di-scroll */
    bottom: 25px;
    right: 25px;
    width: 330px; /* Lebar notifikasi */
    z-index: 2000; /* Pastikan berada di atas semua elemen */
    
    /* Menerapkan efek Glassmorphism */
    background: var(--glass-bg, rgba(255, 255, 255, 0.25));
    backdrop-filter: blur(var(--blur-value, 10px));
    -webkit-backdrop-filter: blur(var(--blur-value, 10px));
    border: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
    box-shadow: var(--card-shadow, 0 8px 32px 0 rgba(31, 38, 135, 0.1));
    border-radius: 12px; /* Radius lebih kecil untuk kartu kecil */
    
    color: var(--text-color, #051a38);
    overflow: hidden; /* Penting agar border-radius berfungsi */
    
    /* Animasi muncul dari bawah */
    animation: slideInUp 0.5s ease-out;
}

/* Header Kartu */
.toast-header {
    padding: 10px 15px;
    border-bottom: 1px solid var(--glass-border, rgba(255, 255, 255, 0.3));
    font-size: 15px;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.toast-header i {
    margin-right: 10px;
    font-size: 16px;
    color: var(--primary-color, #1dbe83); /* Warna ikon pakai warna primer */
}

/* Isi Pesan */
.toast-body {
    padding: 15px;
    font-size: 14px;
    line-height: 1.5;
    color: var(--text-color-light, #5a6a85);
}

.toast-body p {
    margin: 0;
}

/* Tombol Close */
.close-toast {
    position: absolute;
    top: 5px;
    right: 5px;
    background: none;
    border: none;
    color: var(--text-color-light, #5a6a85);
    font-size: 14px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    cursor: pointer;
    transition: background-color 0.2s ease;
}

.close-toast:hover {
    background-color: rgba(0, 0, 0, 0.1);
}

/* Keyframes untuk animasi muncul */
@keyframes slideInUp {
    from {
        transform: translateY(100px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
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
      <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
           
      <li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">
            <a href="kelola_kontak_pesan.php">
              <i class="fas fa-envelope"></i>
              <span>Pesan Masuk</span>
              <?php if ($jumlah_pesan_baru > 0): ?>
                <span class="notification-dot"></span>
              <?php endif; ?>
            </a>
          </li>
     
      <li class="<?php echo ($halaman_aktif == 'guru') ? 'active' : ''; ?>"><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
      <li class="<?php echo ($halaman_aktif == 'testimoni') ? 'active' : ''; ?>"><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
      <li class="<?php echo ($halaman_aktif == 'berita') ? 'active' : ''; ?>"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
      <li class="<?php echo ($halaman_aktif == 'prestasi') ? 'active' : ''; ?>"><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
      <li class="<?php echo ($halaman_aktif == 'struktural') ? 'active' : ''; ?>"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
      <li class="<?php echo ($halaman_aktif == 'galeri') ? 'active' : ''; ?>"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
      <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, ['pesan', 'operator', 'pengaturan', 'performa', 'target_hafalan','kontributor','tata_tertib']) ? 'active open' : ''; ?>">
        <a href="#">
          <i class="fas fa-ellipsis-h"></i>
          <span>Lainnya</span>
          <i class="fas fa-chevron-down dropdown-icon"></i>
        </a>
        <ul class="dropdown-menu">
                                                                                                    <li class="<?php echo ($halaman_aktif == 'tata_tertib') ? 'active' : ''; ?>"><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'target_hafalan') ? 'active' : ''; ?>"><a href="kelola_target_hafalan.php"><i class="fas fa-bullseye"></i><span>Target Hafalan</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'kontributor') ? 'active' : ''; ?>"><a href="kelola_kontributor.php"><i class="fas fa-users-cog"></i><span>Kontributor</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'akun') ? 'active' : ''; ?>"><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'performa') ? 'active' : ''; ?>"><a href="performa.php"><i class="fas fa-cog"></i><span>Site Monitoring</span></a></li>
          <li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="developer.php"><i class="fas fa-code"></i><span>Developer Page</span></a></li>
        </ul>
      </li>
    </ul>
  </aside>

  <main class="main-content">
    <header class="header">
      <div class="header-title">
        <h2>Selamat Datang, <?php echo htmlspecialchars(explode(' ', $nama_lengkap)[0]); ?>!</h2>
        <p>Berikut adalah ringkasan aktivitas terbaru di website Anda.</p>
      </div>
      <div class="user-profile">
        <a href="akun.php" class="profile-link">
          <div class="profile-pic-container">
            <img src="uploads/operator/<?php echo htmlspecialchars($foto_profil); ?>" alt="Foto Profil">
                        <?php if ($jumlah_notifikasi_baru > 0): ?>
              <span class="profile-notification-dot"></span>
            <?php endif; ?>
          </div>
          <div class="user-info">
            <p class="user-name"><?php echo htmlspecialchars($nama_lengkap); ?></p>
            <p class="user-role"><?php echo htmlspecialchars(ucfirst($role)); ?></p>
          </div>
        </a>
        
        <button id="theme-toggle" class="theme-toggle-btn" title="Ganti tema">
             <i class="fas fa-moon"></i>
        </button>
        <a href="logout.php" class="logout-link" title="Logout">
          <i class="fas fa-sign-out-alt"></i>
        </a>
      </div>
    </header>
   
   <?php if ($maintenance_status == 'on'): ?>
<section class="maintenance-alert" id="maintenanceAlert">
  <div class="icon">
    <i class="fas fa-exclamation-triangle"></i>
  </div>
  <div class="message">
    <strong>Warning :</strong> Akan dilakukan pemeliharaan sistem MTs Negeri 1 Way Kanan pada Selasa, 09 Desember 2025 23:30 Wib, dan akan kembali normal pada 05:00 Wib. 
    <!--<strong>Ikon Bulan</strong>-->
    <!--<br>-->
    pastikan sudah tidak ada aktivitas pada jam tersebut, Terimakasih. <strong><br>- Developer Team</strong>
  </div>
  <button type="button" class="close-btn" onclick="closeAlert()" aria-label="Close">
    <i class="fas fa-times"></i>
  </button>
</section>
<?php endif; ?>


<div id="floating-toast">
    <button class="close-toast" onclick="closeToast()">
        <i class="fas fa-times"></i>
    </button>
    
    <div class="toast-header">
        <i class="fas fa-wand-magic-sparkles"></i>
        <strong>Update Tema: Glassmorphism</strong>
    </div>
    
    <!--<div class="toast-body">-->
    <!--    <p>Halo! Ini adalah tampilan halaman percobaan untuk tema baru. Selamat menikmati nuansa yang lebih modern!</p>-->
    <!--</div>-->
</div>

<!--<section class="independence-banner-container">-->
<!--    <div class="banner-slide fade">-->
<!--        <img src="uploads/banner/glass.png" alt="Banner Kemerdekaan 1">-->
<!--  </div>-->

  <!--  <div class="banner-slide fade">-->
  <!--      <img src="uploads/banner/semangat.png" alt="Banner Kemerdekaan 2">-->
  <!--</div>-->
  
  <!--    <div class="banner-slide fade">-->
  <!--      <img src="uploads/banner/semangat2.png" alt="Banner Kemerdekaan 2">-->
  <!--</div>-->
 
    <div class="banner-dots" style="text-align:center">
    <span class="dot"></span>
    <span class="dot"></span>
  </div>
</section>

    <section class="content">
      <div class="stats-grid">
        <div class="stat-card"><div class="icon-container"><i class="fas fa-newspaper"></i></div><div class="info"><h3><?php echo $total_berita; ?></h3><p>Total Berita</p></div></div>
        <div class="stat-card"><div class="icon-container"><i class="fas fa-trophy"></i></div><div class="info"><h3><?php echo $total_prestasi; ?></h3><p>Total Prestasi</p></div></div>
        <div class="stat-card"><div class="icon-container"><i class="fas fa-images"></i></div><div class="info"><h3><?php echo $total_galeri; ?></h3><p>Total Galeri</p></div></div>
        <div class="stat-card"><div class="icon-container"><i class="fas fa-user-shield"></i></div><div class="info"><h3><?php echo $total_operator; ?></h3><p>Total Operator</p></div></div>
        <div class="stat-card"><div class="icon-container"><i class="fas fa-user-graduate"></i></div><div class="info"><h3><?php echo $total_calon_siswa; ?></h3><p>Total Calon Siswa</p></div></div>
        <div class="stat-card"><div class="icon-container"><i class="fas fa-envelope"></i></div><div class="info"><h3><?php echo $total_pesan; ?></h3><p>Total Pesan</p></div></div>
      </div>
   
      <div class="dashboard-grid">
                <div class="activity-card">
          <div class="activity-card-header">
            <h3>Pesan Masuk Terbaru</h3>
            <a href="kelola_kontak_pesan.php">Lihat Semua</a>
          </div>
          <ul class="activity-list">
             <?php if(empty($pesan_terbaru)): ?>
              <li style="padding: 15px 20px;">Tidak ada pesan baru.</li>
            <?php else: ?>
              <?php foreach($pesan_terbaru as $pesan): ?>
              <li>
                <i class="fas fa-envelope-open-text icon"></i>
                <div class="details">
                  <p class="title"><?php echo htmlspecialchars($pesan['subjek']); ?></p>
                  <p class="meta">Dari: <?php echo htmlspecialchars($pesan['nama_pengirim']); ?></p>
                </div>
                <?php if($pesan['status'] == 'Baru'): ?>
                <span class="badge-baru">Baru</span>
                <?php endif; ?>
              </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>

        <div class="activity-card">
          <div class="activity-card-header">
            <h3>Berita Terbaru</h3>
            <a href="kelola_berita.php">Lihat Semua</a>
          </div>
          <ul class="activity-list">
            <?php if(empty($berita_terbaru)): ?>
              <li style="padding: 15px 20px;">Belum ada berita yang dipublikasikan.</li>
            <?php else: ?>
              <?php foreach($berita_terbaru as $berita): ?>
              <li>
                <i class="fas fa-newspaper icon"></i>
                <div class="details">
                  <p class="title"><?php echo htmlspecialchars(substr($berita['judul'], 0, 40)) . '...'; ?></p>
                  <p class="meta">Kategori: <?php echo htmlspecialchars($berita['kategori']); ?></p>
                </div>
              </li>
              <?php endforeach; ?>
            <?php endif; ?>
          </ul>
        </div>
      </div>
     
      <div class="chart-container">
        <h3>Ringkasan Grafik Performa Website</h3>
        <canvas id="performanceChart"></canvas>
        <p>Selengkapnya lihat pada menu <a href="performa.php">Site Monitoring</a></p>
      </div>
     
    </section>
		
		<div style="height: 50px;"></div>
		
  </main>
	
<script>
  // PERUBAHAN: Menggabungkan semua skrip di dalam satu listener
  document.addEventListener('DOMContentLoaded', function() {
   
    // --- Skrip untuk Dropdown Sidebar ---
    const dropdownToggles = document.querySelectorAll('.sidebar-dropdown > a');
    dropdownToggles.forEach(function(toggle) {
      toggle.addEventListener('click', function(event) {
        event.preventDefault();
        const parent = this.parentElement;
        parent.classList.toggle('open');
      });
    });

    // --- Skrip untuk Grafik Performa ---
    const ctx = document.getElementById('performanceChart').getContext('2d');
    const performanceChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [],
        datasets: [{
          label: 'Beban CPU (%)',
          data: [],
          borderColor: 'rgba(243, 156, 18, 0.8)',
          backgroundColor: 'rgba(243, 156, 18, 0.1)',
          fill: true,
          yAxisID: 'y',
        	tension: 0.3 /* Membuat garis lebih melengkung */
        }, {
          label: 'Memori (MB)',
          data: [],
          borderColor: 'rgba(52, 152, 219, 0.8)',
          backgroundColor: 'rgba(52, 152, 219, 0.1)',
          fill: true,
          yAxisID: 'y1',
        	tension: 0.3 /* Membuat garis lebih melengkung */
        }]
      },
      options: {
        responsive: true,
        interaction: { mode: 'index', intersect: false },
        scales: {
          y: { type: 'linear', display: true, position: 'left', title: { display: true, text: 'CPU Load (%)' }, suggestedMax: 100 },
          y1: { type: 'linear', display: true, position: 'right', title: { display: true, text: 'Memory (MB)' }, grid: { drawOnChartArea: false } },
        }
      }
    });

    function updateStats() {
      fetch('server_stats.php')
        .then(response => response.json())
        .then(data => {
          if (data.error) {
            console.error(data.error);
            clearInterval(statsInterval);
            return;
          }

          const chart = performanceChart.data;
          if (chart.labels.length > 20) {
            chart.labels.shift();
            chart.datasets.forEach(dataset => dataset.data.shift());
          }
          chart.labels.push(data.timestamp);
          chart.datasets[0].data.push(data.cpu_load * 100);
          chart.datasets[1].data.push(data.memory_usage);
          performanceChart.update();
        })
        .catch(error => console.error('Gagal mengambil data performa:', error));
    }

    updateStats();
    const statsInterval = setInterval(updateStats, 3000);
  	
  	// --- Skrip untuk Banner Slideshow ---
  	let slideIndex = 0;
  	showBannerSlides();

  	function showBannerSlides() {
    	let i;
    	let slides = document.getElementsByClassName("banner-slide");
    	let dots = document.getElementsByClassName("dot");
    	
    	for (i = 0; i < slides.length; i++) {
      	slides[i].style.display = "none"; 
    	}
    	slideIndex++;
    	if (slideIndex > slides.length) {
      	slideIndex = 1
    	}  
    	for (i = 0; i < dots.length; i++) {
      	dots[i].className = dots[i].className.replace(" active", "");
    	}
    	if (slides.length > 0) {
      	slides[slideIndex-1].style.display = "block"; 
      	dots[slideIndex-1].className += " active";
    	}
    	setTimeout(showBannerSlides, 6000); // Ganti slide setiap 6 detik
  	}
  	
  }); // Akhir dari DOMContentLoaded

  // --- Fungsi untuk menutup alert maintenance ---
  function closeAlert() {
    const alertElement = document.getElementById('maintenanceAlert');
    if (alertElement) {
      alertElement.style.display = 'none';
    }
  }
  
  
  /* =================================================================
   FUNGSI UNTUK MENUTUP TOAST
   ================================================================= */
function closeToast() {
    var toast = document.getElementById('floating-toast');
    if (toast) {
        // Animasi menghilang (opsional tapi bagus)
        toast.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        toast.style.opacity = '0';
        toast.style.transform = 'translateY(20px)';
        
        // Hapus elemen setelah animasi selesai
        setTimeout(function() {
            toast.style.display = 'none';
        }, 300);
    }
}
</script>
<script>
    // --- Skrip untuk Theme Toggle ---
    const themeToggle = document.getElementById('theme-toggle');
    const toggleIcon = themeToggle.querySelector('i');
    
    // Fungsi untuk mengganti ikon
    function setIconForTheme(theme) {
        if (theme === 'dark') {
            toggleIcon.classList.remove('fa-moon');
            toggleIcon.classList.add('fa-sun');
        } else {
            toggleIcon.classList.remove('fa-sun');
            toggleIcon.classList.add('fa-moon');
        }
    }

    // Set ikon yang benar saat halaman dimuat (berdasarkan class di <html>)
    var currentTheme = document.documentElement.classList.contains('dark-mode') ? 'dark' : 'light';
    setIconForTheme(currentTheme);

    // Tambahkan listener untuk klik tombol
    themeToggle.addEventListener('click', function() {
        // Toggle class di <html>
        document.documentElement.classList.toggle('dark-mode');
        
        let theme;
        // Cek apakah dark mode aktif SEKARANG
        if (document.documentElement.classList.contains('dark-mode')) {
            theme = 'dark';
        } else {
            theme = 'light';
        }
        
        // Ganti ikon
        setIconForTheme(theme);
        
        // Simpan pilihan ke localStorage
        localStorage.setItem('theme', theme);
    });
</script>
 
</body>
</html>