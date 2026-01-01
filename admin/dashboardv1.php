<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
  header("Location:login.php");
  exit();
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
 
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
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
    .header{display:flex;justify-content:space-between;align-items:center;background-color:#fff;padding:15px 20px;border-radius:8px;box-shadow:var(--card-shadow); margin-bottom: 30px;}
    /* Menyesuaikan style header */
    .header-title h2 { font-size: 22px; font-weight: 600; color: var(--text-color); margin: 0; }
    .header-title p { margin: 0; color: #777; font-size: 15px; }
    .user-profile{display:flex;align-items:center}
    .user-profile img{width:40px;height:40px;border-radius:50%;object-fit:cover;margin-right:10px}
    .user-profile .user-info p{line-height:1.2}
    .user-profile .user-info .user-name{font-weight:600}
    .user-profile .user-info .user-role{font-size:13px;color:#777}
    .logout-link{margin-left:20px;color:#e74c3c;text-decoration:none;font-size:20px;transition:color .3s ease}
    .logout-link:hover{color:#c0392b}
    .stats-grid{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:20px;margin-top:20px}
    .stat-card{background-color:#fff;padding:25px;border-radius:8px;box-shadow:var(--card-shadow);display:flex;align-items:center;transition:transform .3s ease}
    .stat-card:hover{transform:translateY(-5px)}
    .stat-card .icon-container{width:60px;height:60px;border-radius:50%;display:flex;justify-content:center;align-items:center;margin-right:20px;font-size:24px;color:#fff}
    .stat-card:nth-child(1) .icon-container{background-color:#3498db}
    .stat-card:nth-child(2) .icon-container{background-color:#2ecc71}
    .stat-card:nth-child(3) .icon-container{background-color:#e67e22}
    .stat-card:nth-child(4) .icon-container{background-color:#9b59b6}
    .stat-card:nth-child(5) .icon-container{background-color:#02c4dd}
    .stat-card:nth-child(6) .icon-container{background-color:#a30096}
    .stat-card .info h3{font-size:28px;font-weight:700}
    .stat-card .info p{color:#777}
    .sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}
    .sidebar-dropdown.open .dropdown-menu{display:block}
    .dropdown-menu li a{padding-left:65px}
    .dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}
    .sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}
    .notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
   
    .dashboard-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 30px; margin-top: 30px; }
    .activity-card { background-color: #fff; border-radius: 8px; box-shadow: var(--card-shadow); }
    .activity-card-header { padding: 15px 20px; border-bottom: 1px solid #f0f0f0; display: flex; justify-content: space-between; align-items: center; }
    .activity-card-header h3 { font-size: 18px; font-weight: 600; margin: 0; }
    .activity-card-header a { font-size: 14px; text-decoration: none; color: var(--primary-color); }
    .activity-list { list-style: none; padding: 0; margin: 0; }
    .activity-list li { display: flex; align-items: center; padding: 15px 20px; border-bottom: 1px solid #f0f0f0; }
    .activity-list li:last-child { border-bottom: none; }
    .activity-list .icon { font-size: 18px; color: var(--secondary-color); margin-right: 15px; }
    .activity-list .details p { margin: 0; line-height: 1.4; }
    .activity-list .details .title { font-weight: 500; color: var(--text-color); }
    .activity-list .details .meta { font-size: 13px; color: #888; }
    .activity-list .badge-baru { font-size: 10px; padding: 3px 8px; margin-left: auto; background-color: var(--info-color); color: #fff; border-radius: 20px; }

    .profile-pic-container {
      position: relative;
      margin-right: 10px;
    }
    .profile-pic-container img {
      width: 40px;
      height: 40px;
      border-radius: 50%;
      object-fit: cover;
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
      background-color: #f5f5f5;
    }
   
.maintenance-alert {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  background-color: #fff3cd; /* Warna kuning untuk peringatan */
  color: #664d03;
  border: 1px solid #ffecb5;
  border-radius: 8px;
  margin-bottom: 30px;
  box-shadow: 0 4px 15px rgba(0,0,0,0.05);
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
 s  opacity: 0.7;
  transition: opacity 0.2s ease;
}
.maintenance-alert .close-btn:hover {
  opacity: 1;
}
   
    .chart-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);margin-top:30px}
   
    @media (max-width: 1200px) { .dashboard-grid { grid-template-columns: 1fr; } }
    @media (max-width: 992px){.sidebar{width:70px}.sidebar-header h3,.sidebar-nav li a span,.dropdown-icon{display:none}.main-content{margin-left:70px;width:calc(100% - 70px)}.sidebar-dropdown .dropdown-menu{position:absolute;left:70px;top:0;background-color:var(--sidebar-bg);width:200px;box-shadow:5px 0 15px rgba(0,0,0,.2);border-left:1px solid var(--sidebar-active)}.dropdown-menu li a{padding-left:20px}.dropdown-menu li a span{display:inline-block!important}}
    @media (max-width: 768px){.header{flex-direction:column;align-items:flex-start;gap:15px}}
   
   
/* =================================================================
 BAGIAN 2: KODE CSS (Diperbarui)
 ================================================================= */

/* Tambahkan kode ini ke dalam blok <style> di halaman dashboard.php Anda */

.independence-banner-container {
  position: relative;
  border-radius: 12px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.1);
  margin-bottom: 30px;
  overflow: hidden;
  /* PERUBAHAN: Menghapus background gradient */
}

.banner-slide {
  display: none; /* Sembunyikan semua slide secara default */
}

/* PERUBAHAN: Menyesuaikan style gambar agar mengisi slide */
.banner-slide img {
  width: 100%;
  height: 180px; /* Atur tinggi banner sesuai keinginan */
  object-fit: cover; /* Memastikan gambar mengisi area tanpa distorsi */
  vertical-align: middle;
}

/* PERUBAHAN: Menghapus style untuk .banner-content, .text-content, dll. */

/* Animasi Fade */
.fade {
  animation-name: fade;
  animation-duration: 1.5s;
}

@keyframes fade {
  from {opacity: .4}
  to {opacity: 1}
}

/* Navigasi Titik */
.banner-dots {
  position: absolute;
  bottom: 15px;
  left: 50%;
  transform: translateX(-50%);
}

.dot {
  height: 12px;
  width: 12px;
  margin: 0 4px;
  background-color: rgba(255, 255, 255, 0.5);
  border-radius: 50%;
  display: inline-block;
  border: 1px solid rgba(0,0,0,0.2);
  cursor: pointer;
}

.dot.active {
  background-color: rgba(255, 255, 255, 0.9);
}

@media (max-width: 768px) {
  .banner-slide img {
    height: 180px; /* Sesuaikan tinggi untuk mobile */
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
    <strong>- Perhatian:</strong> Proses Maintenance sedang terjadi berupa Penambahan beberapa menu, Namun <strong>Sistem tetap dapat di gunakan seperti biasa</strong>, dan keadaan seperti ini dianggap biasa dan merupakan bagian dari progres Development, Terimakasih. <strong>- Developer Team</strong>
  </div>
  <button type="button" class="close-btn" onclick="closeAlert()" aria-label="Close">
    <i class="fas fa-times"></i>
  </button>
</section>
<?php endif; ?>

<section class="independence-banner-container">
    <div class="banner-slide fade">
        <img src="uploads/banner/semangat.png" alt="Banner Kemerdekaan 1">
  </div>

    <div class="banner-slide fade">
        <img src="uploads/banner/semangat2.png" alt="Banner Kemerdekaan 2">
  </div>
 
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
              <li>Tidak ada pesan baru.</li>
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
              <li>Belum ada berita yang dipublikasikan.</li>
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
        <h3 class="mb-2">Ringkasan Grafik Performa Website</h3>
        <canvas id="performanceChart"></canvas>
        <p class="mt-4">Selengkapnya lihat pada menu <a href="performa.php">Site Monitoring</a></p>
     </div>
     
    </section>
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
  });

  // --- Fungsi untuk menutup alert maintenance ---
  function closeAlert() {
    const alertElement = document.getElementById('maintenanceAlert');
    if (alertElement) {
      alertElement.style.display = 'none';
    }
  }
</script>

<script>
/* =================================================================
 BAGIAN 3: KODE JAVASCRIPT
 ================================================================= */

// Tambahkan skrip ini ke dalam blok <script> di bagian bawah halaman dashboard.php Anda
// Pastikan skrip ini berada di dalam event listener DOMContentLoaded

let slideIndex = 0;
showBannerSlides();

function showBannerSlides() {
  let i;
  let slides = document.getElementsByClassName("banner-slide");
  let dots = document.getElementsByClassName("dot");
 
  // Sembunyikan semua slide
  for (i = 0; i < slides.length; i++) {
   slides[i].style.display = "none";
  }
 
  slideIndex++;
 
  // Kembali ke slide pertama jika sudah mencapai akhir
  if (slideIndex > slides.length) {
    slideIndex = 1;
  }
 
  // Hapus class 'active' dari semua titik
  for (i = 0; i < dots.length; i++) {
This    dots[i].className = dots[i].className.replace(" active", "");
  }
 
  // Tampilkan slide saat ini dan aktifkan titik yang sesuai
   if (slides.length > 0) {
    slides[slideIndex-1].style.display = "block";
    dots[slideIndex-1].className += " active";
  }
 
  // Atur waktu pergantian slide (3 detik)
  setTimeout(showBannerSlides, 6000);
}
</script>
 
 
</body>
</html>