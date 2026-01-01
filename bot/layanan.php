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
    
    <!-- Menggunakan file style.css eksternal -->
    <link rel="stylesheet" href="style.css"> 
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolled">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil/identitas.php">Identitas Madrasah</a></li>
                            <li><a class="dropdown-item" href="visi-misi-tujuan.html">Visi, Misi & Tujuan</a></li>
                            <li><a class="dropdown-item" href="sejarah.html">Sejarah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="berita.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="galeri.php">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" aria-current="page" href="layanan.php">Layanan</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-ppdb" href="ppdb-form.php">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Pusat Layanan Digital</h1>
            <p class="page-subtitle">Akses berbagai layanan madrasah dengan mudah dan cepat.</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Layanan -->
    <main class="services-page-section">
        <div class="container">
            <div class="row g-4 justify-content-center">
                
                <!-- Kartu Layanan 1: PPDB -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/emis.png" alt="Ikon PPDB">
                        </div>
                        <h5 class="service-title">EMIS</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 2: Raport Digital -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="http://rdm.mtsn1waykanan.com/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/rdm.png" alt="Ikon Raport Digital">
                        </div>
                        <h5 class="service-title">RDM</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 3: Legalisir Online -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/erkam.png" alt="Ikon Legalisir">
                        </div>
                        <h5 class="service-title">ERKAM</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 4: Presensi Pegawai -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://pusaka-v3.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/bos.png" alt="Ikon Presensi">
                        </div>
                        <h5 class="service-title">BOS</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 5: Perpustakaan Digital -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/pusaka.png" alt="Ikon Perpustakaan">
                        </div>
                        <h5 class="service-title">PUSAKA</h5>
                    </a>
                </div>
                
                <!-- Kartu Layanan 6: EMIS -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="https://emis.kemenag.go.id/" target="_blank" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/gtk.png" alt="Ikon Database">
                        </div>
                        <h5 class="service-title">GTK</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 7: Informasi Humas -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="#" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/bkn.png" alt="Ikon Humas">
                        </div>
                        <h5 class="service-title">BKN</h5>
                    </a>
                </div>

                <!-- Kartu Layanan 8: Kontak -->
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <a href="index.php#kontak" class="service-card">
                        <div class="service-icon">
                            <!-- GANTI GAMBAR DI SINI -->
                            <img src="img/PTSP.png" alt="Ikon Kontak">
                        </div>
                        <h5 class="service-title">PTSP</h5>
                    </a>
                </div>

            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
