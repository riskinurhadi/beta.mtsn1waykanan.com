<?php
// Panggil file koneksi untuk konsistensi, meskipun halaman ini statis
require_once 'koneksi.php';

// Array data lokasi penting untuk mempermudah pengelolaan
$lokasi_penting = [
    [
        'nama' => 'Kantor Desa Tampingan',
        'alamat' => 'Jl. Raya Boja - Susukan KM.1, Tampingan, Boja, Kendal',
        'iframe_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.149959794197!2d110.2860326!3d-7.108614!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7062fe7f9f2091%3A0x10df70e8b27e31f0!2sKantor%20Kelurahan%20Tampingan!5e0!3m2!1sid!2sid!4v1758382263096!5m2!1sid!2sid',
        'gmaps_link' => 'https://maps.app.goo.gl/RisZa4bSQaAZvFnK8'
    ],
    [
        'nama' => 'RS PKU Muhammadiyah Boja',
        'alamat' => 'Jalan Raya Boja Limbangan, Salamsari, Boja, Kendal, Jawa Tengah',
        'iframe_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d25834.564211484794!2d110.2764568910365!3d-7.11079828563235!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70630bc3a29c41%3A0x3dab212da0b34ae8!2sRUMAH%20SAKIT%20PKU%20Muhammadiyah%20Boja!5e0!3m2!1sid!2sid!4v1759032192851!5m2!1sid!2sid',
        'gmaps_link' => 'https://maps.app.goo.gl/qQCd9wPvpC6KfEBR9'
    ],
    [
        'nama' => 'Puskesmas Boja 1',
        'alamat' => 'Jl. Raya Boja, Bebengan, Kec. Boja, Kabupaten Kendal',
        'iframe_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.1736534421584!2d110.25968027318213!3d-7.105863969669338!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e70630b01fc1635%3A0x44100df407606828!2sPuskesmas%20Boja!5e0!3m2!1sid!2sid!4v1759032328431!5m2!1sid!2sid',
        'gmaps_link' => 'https://maps.app.goo.gl/rmQKQWncNvgoZCJH7'
    ],
    [
        'nama' => 'SD Negeri 1 Tampingan',
        'alamat' => 'Jl. Tampingan, Tampingan, Kec. Boja, Kabupaten Kendal',
        'iframe_src' => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d7918.384020700173!2d110.2783813721818!3d-7.103732626004459!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7062ff7d257629%3A0x53ef527641c5aea9!2sSD%20Negeri%201%20Tampingan!5e0!3m2!1sid!2sid!4v1759032434010!5m2!1sid!2sid',
        'gmaps_link' => 'https://maps.app.goo.gl/LkvDAyfcxLbEc6u89'
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lokasi Penting - Desa Tampingan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d47a1;
            --dark-text: #333;
            --light-gray: #f8f9fa;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
        }
        /* ----- Navbar Styling (diasumsikan sama dengan halaman utama) ----- */
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; padding: 0.8rem 1rem; border-radius: 5px; transition: background-color 0.3s; }
        .dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .dropdown-item:hover { background-color: var(--light-gray); color: var(--primary-color); }
        @media (min-width: 992px) {
            .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover { background-color: rgba(255,255,255,0.1); }
            .dropdown:hover > .dropdown-menu { display: block; }
        }
        
        .navbar .btn-outline-light {
    /* Menambahkan transisi agar hover lebih halus */
    transition: all 0.3s ease;
    font-weight: 500;
}

/* Penyesuaian untuk layar sangat kecil agar tidak terlalu besar */
@media (max-width: 576px) {
    .navbar .btn {
        /* Menggunakan ukuran tombol 'small' dari Bootstrap */
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Mengurangi margin kanan logo agar ada lebih banyak ruang */
    .navbar-brand img {
        margin-right: 10px;
    }
}

        /* ----- Page Header ----- */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/img/fotobersama.png') no-repeat center center;
            background-size: cover;
            padding: 8rem 0;
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }
        .location-card {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        .location-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }
        .map-container {
            position: relative;
            padding-bottom: 75%; /* 4:3 Aspect Ratio */
            height: 0;
            overflow: hidden;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
        }
        .map-container iframe {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }
        .card-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .card-body .card-title {
            font-weight: 600;
            color: var(--primary-color);
        }
        .card-body .card-text {
            color: #555;
            font-size: 0.95rem;
        }
        .card-body .btn {
            margin-top: auto; /* Mendorong tombol ke bawah */
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Brand/Logo (tidak berubah) -->
            <a class="navbar-brand" href="index.php">
                <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                <div class="logo-text">
                    <span class="title">Pemerintah Kabupaten Kendal</span>
                    <span class="subtitle">Desa Tampingan</span>
                </div>
            </a>

            <!-- Tombol hamburger (Toggler) untuk mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavProfil" aria-controls="navbarNavProfil" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Konten yang akan disembunyikan di mobile -->
            <div class="collapse navbar-collapse" id="navbarNavProfil">
                <ul class="navbar-nav ms-auto">
                    <!-- Hanya satu menu item -->
                    <li class="nav-item">
                        <a class="nav-link" href="https://tampinganboja.com/">
                            <i class="bi bi-house-door-fill me-1"></i> Kembali ke Beranda
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main style="padding-top: 70px;">
    <section class="page-header">
        <div class="container">
            <h1 class="display-4">Peta Lokasi Penting</h1>
            <p class="lead">Temukan lokasi fasilitas umum dan layanan penting di wilayah Desa Tampingan.</p>
        </div>
    </section>

    <section class="location-section mt-5">
        <div class="container">
            <div class="row g-4">
                <?php foreach($lokasi_penting as $lokasi): ?>
                <div class="col-lg-6">
                    <div class="location-card">
                        <div class="map-container">
                            <iframe src="<?php echo $lokasi['iframe_src']; ?>" loading="lazy"></iframe>
                        </div>
                        <div class="card-body p-4">
                            <h5 class="card-title"><?php echo htmlspecialchars($lokasi['nama']); ?></h5>
                            <p class="card-text"><i class="bi bi-geo-alt-fill me-2"></i><?php echo htmlspecialchars($lokasi['alamat']); ?></p>
                            <a href="<?php echo $lokasi['gmaps_link']; ?>" target="_blank" class="btn btn-primary">
                                <i class="bi bi-sign-turn-right-fill me-2"></i>Buka di Google Maps
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center p-4 mt-4">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
