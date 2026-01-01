<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// Cek apakah parameter 'nama' kategori ada di URL.
if (!isset($_GET['nama']) || empty($_GET['nama'])) {
    // Jika tidak ada, redirect ke halaman berita utama.
    header("Location: berita.php");
    exit();
}

// Ambil dan bersihkan nama kategori dari URL.
$nama_kategori = urldecode($_GET['nama']);

// --- Logika Pagination ---
$limit = 6; // Jumlah berita per halaman.
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;

// Query untuk menghitung total berita dalam kategori ini.
$stmt_total = $koneksi->prepare("SELECT COUNT(*) AS total FROM berita WHERE kategori = ?");
$stmt_total->bind_param("s", $nama_kategori);
$stmt_total->execute();
$total_result = $stmt_total->get_result();
$total_records = $total_result->fetch_assoc()['total'];
$total_halaman = ceil($total_records / $limit);
$stmt_total->close();

// Query untuk mengambil data berita sesuai halaman dan kategori.
$query_berita = "SELECT * FROM berita WHERE kategori = ? AND status_berita = 'Diterbitkan' ORDER BY tanggal_publikasi DESC LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($query_berita);
$stmt->bind_param("sii", $nama_kategori, $limit, $offset);
$stmt->execute();
$result_berita = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori: <?php echo htmlspecialchars($nama_kategori); ?> - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

<style>
    /* =============================================
   STYLE UNTUK HALAMAN GALERI LENGKAP
   ============================================= */

.gallery-full-page-section {
    padding: 80px 0;
}

/* Tombol Filter */
.filter-buttons .btn-filter {
    background-color: #e9ecef;
    border: none;
    color: #495057;
    padding: 8px 20px;
    margin: 5px;
    border-radius: 50px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.filter-buttons .btn-filter:hover {
    background-color: #d1d5db;
}

.filter-buttons .btn-filter.active {
    background-color: #198754;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(25, 135, 84, 0.3);
}

/* Menggunakan style .gallery-item yang sudah ada dari halaman utama */
.gallery-item-wrapper {
    /* Isotope akan menangani penempatan */
}

/* Modal Lightbox */
#galleryModal .modal-content {
    background-color: transparent;
    border: none;
}
#galleryModal .modal-header {
    border-bottom: none;
}
#galleryModal .modal-header .modal-title {
    color: #fff;
    font-weight: 500;
}
#galleryModal .modal-header .btn-close {
    filter: invert(1) grayscale(100%) brightness(200%);
}
#galleryModal .modal-body {
    padding: 0;
}
#galleryModal .modal-body img {
    border-radius: 10px;
}

/* =============================================
   STYLE UNTUK SIDEBAR BERITA (DIPERBARUI)
   ============================================= */

.sidebar-widget {
    background-color: #ffffff;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 5px 20px rgba(0, 0, 0, 0.07);
    margin-bottom: 30px;
}

.widget-title {
    font-size: 1.3rem;
    font-weight: 700;
    color: #333;
    padding-bottom: 10px;
    margin-bottom: 15px;
    border-bottom: 2px solid #e9ecef;
}

.category-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.category-list li {
    margin-bottom: 0;
}

.category-list a {
    text-decoration: none;
    color: #555;
    font-weight: 500;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 15px;
    border-radius: 8px;
    transition: all 0.2s ease-in-out;
    border-bottom: 1px solid #f0f0f0;
}

.category-list li:last-child a {
    border-bottom: none;
}

.category-list a:hover {
    background-color: #e0f2f1;
    color: #198754;
}

.category-list a span {
    font-size: 0.85rem;
    color: #ffffff;
    background-color: #198754;
    padding: 3px 9px;
    border-radius: 50px;
    font-weight: 600;
    min-width: 28px;
    text-align: center;
}

</style>
    <!-- ============================================= -->
    <!-- NAVBAR BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="guru.php">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="berita.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#galeri">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="layanan.php">Layanan</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="tata-tertib.php">Tata Tertib Madrasah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-ppdb" href="https://mtsn1waykanan.com/ppdb">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header (Konten Asli Anda) -->
    <header class="page-header">
        <div class="container mt-5">
            <p class="page-subtitle">Menampilkan Berita Kategori</p>
            <h1 class="page-title"><?php echo htmlspecialchars($nama_kategori); ?></h1>
        </div>
    </header>

    <!-- Konten Utama Halaman Berita (Konten Asli Anda) -->
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
                                $gambar = (!empty($berita['gambar_utama'])) ? 'admin/uploads/berita/' . htmlspecialchars($berita['gambar_utama']) : 'https://placehold.co/600x400/E0F2F1/198754?text=Berita';
                        ?>
                                <div class="col-md-6">
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
                            echo '<div class="col-12"><p class="text-center">Tidak ada berita yang ditemukan dalam kategori ini.</p></div>';
                        }
                        ?>
                    </div>

                    <!-- Navigasi Halaman (Pagination) -->
                    <nav aria-label="Page navigation" class="mt-5">
                        <ul class="pagination justify-content-center">
                            <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                                <li class="page-item <?php if($halaman == $i) echo 'active'; ?>">
                                    <a class="page-link" href="kategori.php?nama=<?php echo urlencode($nama_kategori); ?>&halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
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

    <!-- Footer (Konten Asli Anda) -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $stmt->close();
    $koneksi->close();
    ?>
    
    <!-- ============================================= -->
    <!-- SCRIPT JS BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ambil elemen navbar
        const navbar = document.querySelector('.navbar');

        // Fungsi untuk memperbarui tampilan navbar
        function updateNavbar() {
            // Di halaman selain Beranda, navbar selalu 'scrolled'
            navbar.classList.add('scrolled');
        }

        // --- Event Listeners untuk Navbar ---
        document.addEventListener('DOMContentLoaded', updateNavbar);
        window.addEventListener('scroll', updateNavbar);
        window.addEventListener('resize', updateNavbar);

        // --- Kode untuk menutup menu mobile saat klik di luar ---
        document.addEventListener('click', function (event) {
            const navbarMenu = document.querySelector('#navbarNav');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            if (navbarMenu && navbarMenu.classList.contains('show')) {
                const isClickInsideNavbar = navbarMenu.contains(event.target) || (navbarToggler && navbarToggler.contains(event.target));
                if (!isClickInsideNavbar) {
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
