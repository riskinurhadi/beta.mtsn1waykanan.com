<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pusat Layanan - MTs Negeri 1 Way Kanan</title>
    
    <!-- Path sudah disesuaikan untuk file di root directory -->
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Menggunakan file style.css eksternal -->
    <link rel="stylesheet" href="../style.css"> 
</head>
<body>

    <!-- ============================================= -->
    <!-- NAVBAR BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="../img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/guru.php">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/berita.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/index.php#galeri">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="https://mtsn1waykanan.com/layanan.php">Layanan</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tata-tertib.php">Tata Tertib Madrasah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/index.php#kontak">Kontak</a>
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
            <h1 class="page-title">Pusat Layanan Digital</h1>
            <p class="page-subtitle">Akses berbagai layanan madrasah dengan mudah dan cepat.</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Layanan (Konten Asli Anda) -->
    <main class="services-page-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://emis.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/emis.png" alt="Ikon EMIS">
                        </div>
                        <h5 class="service-title">EMIS</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://rdm.mtsn1waykanan.sch.id/ " target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/rdm.png" alt="Ikon Raport Digital">
                        </div>
                        <h5 class="service-title">RDM</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://erkam.kemenag.go.id/#/passport/login" class="service-card">
                        <div class="service-icon">
                            <img src="../img/erkam.png" alt="Ikon ERKAM">
                        </div>
                        <h5 class="service-title">ERKAM</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://bos.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/bos.png" alt="Ikon BOS">
                        </div>
                        <h5 class="service-title">BOS</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://pusaka.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/pusaka.png" alt="Ikon PUSAKA">
                        </div>
                        <h5 class="service-title">PUSAKA</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://emisgtk.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/gtk.png" alt="Ikon GTK">
                        </div>
                        <h5 class="service-title">GTK</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://asndigital.bkn.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <img src="../img/bkn.png" alt="Ikon BKN">
                        </div>
                        <h5 class="service-title">BKN</h5>
                    </a>
                </div>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="" class="service-card">
                        <div class="service-icon">
                            <img src="../img/PTSP.png" alt="Ikon PTSP">
                        </div>
                        <h5 class="service-title">PTSP</h5>
                    </a>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (Konten Asli Anda) -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <!-- ============================================= -->
    <!-- SCRIPT JS BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
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
