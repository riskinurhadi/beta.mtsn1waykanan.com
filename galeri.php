<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// 1. Ambil semua data foto dari galeri.
$query_foto = "SELECT * FROM galeri ORDER BY tanggal_upload DESC";
$result_foto = $koneksi->query($query_foto);

// 2. Ambil semua kategori unik untuk tombol filter.
$query_kategori = "SELECT DISTINCT kategori FROM galeri ORDER BY kategori ASC";
$result_kategori = $koneksi->query($query_kategori);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Kegiatan - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
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
                        <a class="nav-link" href="berita.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="galeri.php">Galeri</a>
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
            <h1 class="page-title">Galeri Kegiatan</h1>
            <p class="page-subtitle">Dokumentasi Momen Berharga di MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Galeri (Konten Asli Anda) -->
    <main class="gallery-full-page-section">
        <div class="container">
            <div class="row">
                <div class="col-12 text-center mb-5">
                    <div class="filter-buttons">
                        <button class="btn btn-filter active" data-filter="*">Semua</button>
                        <?php if ($result_kategori && $result_kategori->num_rows > 0): ?>
                            <?php while($kategori = $result_kategori->fetch_assoc()): ?>
                                <button class="btn btn-filter" data-filter=".<?php echo str_replace(' ', '-', strtolower(htmlspecialchars($kategori['kategori']))); ?>"><?php echo htmlspecialchars($kategori['kategori']); ?></button>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="row g-4 gallery-container">
                <?php
                if ($result_foto && $result_foto->num_rows > 0):
                    while ($item = $result_foto->fetch_assoc()):
                        $foto_url = 'admin/uploads/galeri/' . htmlspecialchars($item['foto_url']);
                        $kategori_class = str_replace(' ', '-', strtolower(htmlspecialchars($item['kategori'])));
                ?>
                        <div class="col-lg-4 col-md-6 gallery-item-wrapper <?php echo $kategori_class; ?>">
                            <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#galleryModal" data-img-src="<?php echo $foto_url; ?>" data-title="<?php echo htmlspecialchars($item['deskripsi']); ?>">
                                <img src="<?php echo $foto_url; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['deskripsi']); ?>">
                                <div class="gallery-overlay">
                                    <p class="gallery-category"><?php echo htmlspecialchars($item['kategori']); ?></p>
                                    <h4 class="gallery-description"><?php echo htmlspecialchars($item['deskripsi']); ?></h4>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                    echo '<div class="col-12 text-center"><p>Belum ada foto untuk ditampilkan di galeri.</p></div>';
                endif;
                ?>
            </div>
        </div>
    </main>

    <!-- Modal Lightbox untuk Galeri (Konten Asli Anda) -->
    <div class="modal fade" id="galleryModal" tabindex="-1" aria-labelledby="galleryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="galleryModalLabel">Detail Gambar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img src="" class="img-fluid" id="modalImage" alt="Detail Gambar">
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (Konten Asli Anda) -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php $koneksi->close(); ?>
    
    <!-- ============================================= -->
    <!-- SCRIPT JS BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/isotope-layout@3/dist/isotope.pkgd.min.js"></script>
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

        // Script untuk Isotope Filter & Lightbox Modal
        document.addEventListener('DOMContentLoaded', function() {
            var iso = new Isotope('.gallery-container', {
                itemSelector: '.gallery-item-wrapper',
                layoutMode: 'fitRows'
            });

            var filterButtons = document.querySelectorAll('.filter-buttons .btn');
            filterButtons.forEach(function(button) {
                button.addEventListener('click', function() {
                    filterButtons.forEach(btn => btn.classList.remove('active'));
                    this.classList.add('active');
                    var filterValue = this.getAttribute('data-filter');
                    iso.arrange({ filter: filterValue });
                });
            });

            const galleryModal = document.getElementById('galleryModal');
            galleryModal.addEventListener('show.bs.modal', event => {
                const triggerElement = event.relatedTarget;
                const imgSrc = triggerElement.getAttribute('data-img-src');
                const title = triggerElement.getAttribute('data-title');
                const modalImage = galleryModal.querySelector('#modalImage');
                const modalTitle = galleryModal.querySelector('#galleryModalLabel');
                modalImage.src = imgSrc;
                modalImage.alt = title;
                modalTitle.textContent = title;
            });
        });
    </script>
</body>
</html>
