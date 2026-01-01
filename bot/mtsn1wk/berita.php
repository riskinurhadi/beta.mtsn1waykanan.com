<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// --- Logika Pagination ---
$limit = 6; // Jumlah berita per halaman.
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;

// Query untuk menghitung total berita.
$total_result = $koneksi->query("SELECT COUNT(*) AS total FROM berita");
$total_records = $total_result->fetch_assoc()['total'];
$total_halaman = ceil($total_records / $limit);

// Query untuk mengambil data berita sesuai halaman.
$query_berita = "SELECT * FROM berita ORDER BY tanggal_publikasi DESC LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($query_berita);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result_berita = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita & Informasi - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="index.html">Beranda</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">Identitas Madrasah</a></li>
                            <li><a class="dropdown-item" href="visi-misi-tujuan.html">Visi, Misi & Tujuan</a></li>
                            <li><a class="dropdown-item" href="sejarah.html">Sejarah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Galeri</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Akademik
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="http://rdm.mtsn1waykanan.com/">Raport Digital (RDM)</a></li>
                            <li><a class="dropdown-item" href="https://pusaka-v3.kemenag.go.id/">Presensi Pegawai</a></li>
                            <li><a class="dropdown-item" href="https://emis.kemenag.go.id/">EMIS</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="">Legalisir Online</a></li>
                            <li><a class="dropdown-item" href="https://emisgtk.kemenag.go.id/">GTK</a></li>
                            <li><a class="dropdown-item" href="https://asndigital.bkn.go.id/">ASN</a></li>
                            <li><a class="dropdown-item" href="">PTSP</a></li>
                            <!--<li><a class="dropdown-item" href="sejarah.html">PTSP</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">Legalisir Online</a></li>-->
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="#">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <!-- Ganti tanda '#' dengan link halaman PPDB Anda -->
                        <a class="btn btn-ppdb" href="#">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container mt-5 mb-5">
            <h1 class="page-title mt-5">Arsip Berita</h1>
            <p class="page-subtitle  mb-2">Kumpulan Informasi, Kegiatan, dan Pengumuman Madrasah</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Berita -->
    <main class="news-page-section">
        <div class="container mt-5">
            <div class="row">
                <!-- Kolom Utama (Daftar Berita) -->
                <div class="col-lg-8">
                    <div class="row g-4">
                        <?php
                        if ($result_berita->num_rows > 0) {
                            while ($berita = $result_berita->fetch_assoc()) {
                                $tanggal = date("d F Y", strtotime($berita['tanggal_publikasi']));
                                $cuplikan = substr(strip_tags($berita['isi']), 0, 100) . '...';
                                // Menggunakan direktori gambar yang baru: admin/uploads/berita/
                                $gambar = (!empty($berita['gambar_utama'])) ? 'admin/uploads/berita/' . htmlspecialchars($berita['gambar_utama']) : 'https://placehold.co/600x400/E0F2F1/198754?text=Berita';
                        ?>
                                <div class="col-md-6">
                                    <!-- Kartu Berita (menggunakan style yang sama dari halaman utama) -->
                                    <div class="news-card">
                                        <div class="news-img-container">
                                            <img src="<?php echo $gambar; ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>">
                                            <div class="news-category-badge"><?php echo htmlspecialchars($berita['kategori']); ?></div>
                                        </div>
                                        <div class="news-content">
                                            <p class="news-meta"><?php echo $tanggal; ?></p>
                                            <h4 class="news-title">
                                                <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>"><?php echo htmlspecialchars($berita['judul']); ?></a>
                                            </h4>
                                            <p class="news-excerpt"><?php echo htmlspecialchars($cuplikan); ?></p>
                                            <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>" class="news-read-more">Baca Selengkapnya →</a>
                                        </div>
                                    </div>
                                </div>
                        <?php
                            }
                        } else {
                            echo '<div class="col-12"><p class="text-center">Tidak ada berita yang ditemukan.</p></div>';
                        }
                        ?>
                    </div>

                    <!-- Navigasi Halaman (Pagination) -->
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                                <li class="page-item <?php if($halaman == $i) echo 'active'; ?>">
                                    <a class="page-link" href="berita.php?halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>
                </div>

                <!-- Sidebar -->
                <div class="col-lg-4">
                    <div class="sidebar-widget">
                        <h4 class="widget-title">Kategori Berita</h4>
                        <ul class="category-list">
                            <?php
                            // Query untuk mengambil daftar kategori.
                            $query_kategori = "SELECT kategori, COUNT(*) as jumlah FROM berita GROUP BY kategori ORDER BY kategori ASC";
                            $result_kategori = $koneksi->query($query_kategori);
                            if ($result_kategori->num_rows > 0) {
                                while($kategori = $result_kategori->fetch_assoc()) {
                                    echo '<li><a href="kategori.php?nama=' . urlencode($kategori['kategori']) . '">' . htmlspecialchars($kategori['kategori']) . '<span>(' . $kategori['jumlah'] . ')</span></a></li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $stmt->close();
    $koneksi->close();
    ?>
    
    <!-- ============================================= -->
    <!-- Bootstrap JS (wajib ada untuk dropdown) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 JS (opsional, untuk notifikasi) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <!-- Custom Script Anda -->
    <script>
        // Efek scroll untuk Navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // --- KODE UNTUK NAVBAR MOBILE (CLOSE ON CLICK OUTSIDE) ---
        document.addEventListener('click', function (event) {
            const navbarMenu = document.querySelector('#navbarNav');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            if (navbarMenu.classList.contains('show')) {
                // Cek apakah klik terjadi di dalam area navbar atau pada toggler itu sendiri
                const isClickInsideNavbar = navbarMenu.contains(event.target) || navbarToggler.contains(event.target);
                
                if (!isClickInsideNavbar) {
                    // Buat instance dari Collapse Bootstrap dan panggil method .hide()
                    let bsCollapse = new bootstrap.Collapse(navbarMenu, {
                        toggle: false
                    });
                    bsCollapse.hide();
                }
            }
        });
    </script>
</body>
</html>