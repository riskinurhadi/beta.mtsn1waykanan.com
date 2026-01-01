<?php
require 'koneksi.php';

// BERITA
// Fungsi untuk membuat ringkasan/excerpt dari isi berita
function buat_ringkasan($konten, $panjang = 120) {
    // Menghapus tag HTML dari konten editor
    $teks_polos = strip_tags($konten);
    if (strlen($teks_polos) > $panjang) {
        $potong_teks = substr($teks_polos, 0, $panjang);
        // Memastikan tidak memotong kata di tengah
        $akhir_spasi = strrpos($potong_teks, ' ');
        $ringkasan = substr($potong_teks, 0, $akhir_spasi);
        return $ringkasan . '...';
    }
    return $teks_polos;
}

// Mengambil 3 berita terbaru dari database
$query_berita_terkini = "SELECT judul, slug, gambar, tanggal_publish, isi FROM tb_berita ORDER BY tanggal_publish DESC LIMIT 3";
$hasil_berita_terkini = $koneksi->query($query_berita_terkini);


// PEJABAT
$upload_dir = 'admin/uploads/pejabat/';
// Query untuk mengambil 4 data pejabat pertama, diurutkan berdasarkan kolom 'urutan'
$query_pejabat = "SELECT nama_pejabat, jabatan, foto FROM tb_pejabat ORDER BY urutan ASC, nama_pejabat ASC LIMIT 4";
$result_pejabat = $koneksi->query($query_pejabat);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Website Resmi Desa Tampingan - Kendal</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Fonts: Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Internal CSS -->
    <style>
        :root {
            --primary-color: #0d47a1; /* Biru tua formal */
            --secondary-color: #ff9800; /* Oranye untuk aksen */
            --light-gray: #f8f9fa;
            --dark-text: #333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            color: var(--dark-text);
        }

        /* ----- Navbar ----- */
        .navbar {
            background-color: var(--primary-color);
            padding: 0.8rem 1rem;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }
        .navbar-brand {
            display: flex;
            align-items: center;
        }
        .navbar-brand img {
            height: 50px;
            margin-right: 15px;
        }
        .navbar-brand .logo-text {
            color: white;
            line-height: 1.2;
        }
        .navbar-brand .logo-text .title {
            font-size: 0.9rem;
            font-weight: 300;
            display: block;
        }
        .navbar-brand .logo-text .subtitle {
            font-size: 1.1rem;
            font-weight: 600;
            display: block;
        }
        .navbar-nav .nav-link {
            color: white;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar-nav .nav-link:hover, .navbar-nav .nav-link.active {
            background-color: rgba(255,255,255,0.15);
        }
        .navbar-toggler {
            border-color: rgba(255,255,255,0.5);
        }
        .navbar-toggler-icon {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 30 30'%3e%3cpath stroke='rgba(255, 255, 255, 0.8)' stroke-linecap='round' stroke-miterlimit='10' stroke-width='2' d='M4 7h22M4 15h22M4 23h22'/%3e%3c/svg%3e");
        }


        /* ----- Hero Slider (UPDATED) ----- */
        .hero-slider {
            position: relative;
            height: 80vh; /* Tinggi slider diubah menjadi 80vh */ 
            min-height: 500px; /* Disesuaikan agar tidak terlalu pendek di layar kecil */
            color: white;
        }
        .carousel-item {
            height: 80vh; /* Tinggi carousel item diubah menjadi 80vh */
            min-height: 500px;
        }
        .carousel-item img {
            object-fit: cover;
            height: 100%;
            width: 100%;
        }
        .carousel-caption-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5); /* Overlay gelap untuk keterbacaan teks */
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 20px;
        }
        .hero-slider h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.7);
        }
        .hero-slider p {
            font-size: 1.2rem;
            margin-bottom: 2rem;
        }
        .search-bar {
            max-width: 600px;
            width: 100%;
        }
        .search-bar .form-control {
            height: 50px;
        }
        .search-bar .btn {
            height: 50px;
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        /* ----- Quick Access / Icon Bar ----- */
        .quick-access {
            background-color: white;
            padding: 2.5rem 0;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        .quick-access-item {
            text-decoration: none;
            color: var(--dark-text);
            display: block;
            padding: 15px;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
        }
        .quick-access-item:hover {
            transform: translateY(-5px);
            color: var(--primary-color);
        }
        .quick-access-item .icon-wrapper {
            width: 70px;
            height: 70px;
            margin: 0 auto 15px auto;
            border-radius: 50%;
            background-color: var(--light-gray);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2.2rem;
            color: var(--primary-color);
            transition: background-color 0.3s;
        }
        .quick-access-item:hover .icon-wrapper {
            background-color: var(--secondary-color);
            color: white;
        }
        .quick-access-item h6 {
            font-weight: 600;
        }

        /* ----- Content Sections ----- */
        .section-title {
            text-align: center;
            margin-bottom: 3rem;
            font-weight: 700;
            color: var(--primary-color);
        }
        .news-card {
            border: none;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .news-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }

        /* ----- Main Footer Section ----- */
.main-footer {
    background-color: #1e3a56; /* Warna biru gelap seperti referensi */
    color: #bdc3c7; /* Warna teks abu-abu terang */
    padding: 4rem 0 0 0;
    font-size: 0.95rem;
}

.main-footer h5, .main-footer h6 {
    color: white;
    font-weight: 600;
}

.footer-info-brand {
    display: flex;
    align-items: center;
}
.footer-info-brand img {
    height: 50px;
    margin-right: 15px;
}
.footer-info-brand .logo-text span {
    font-size: 0.8rem;
    display: block;
    line-height: 1.2;
}
.footer-info-brand .logo-text h6 {
    font-size: 1rem;
    margin: 0;
}

.info-list li {
    margin-bottom: 0.8rem;
}
.info-list i {
    font-size: 1.1rem;
    margin-top: 4px;
    color: var(--secondary-color);
}
.social-links a {
    color: #bdc3c7;
    font-size: 1.5rem;
    transition: color 0.3s;
}
.social-links a:hover {
    color: white;
}

.map-container iframe {
    width: 100%;
    height: 200px;
    border-radius: 10px;
    border: 0;
}

.visitor-stats li {
    display: flex;
    justify-content: space-between;
    padding: 0.5rem 0;
    border-bottom: 1px solid #34495e;
}
.visitor-stats li:last-child {
    border-bottom: none;
}

.survey-form p {
    font-size: 0.9rem;
}
.survey-form .form-check {
    margin-bottom: 0.5rem;
}
.survey-form .btn {
    background-color: #3498db;
    border: none;
    font-weight: 500;
    transition: background-color 0.3s;
}
.survey-form .btn:hover {
    background-color: #2980b9;
}

.footer-bottom {
    border-top: 1px solid #34495e;
    padding: 1.5rem 0;
    margin-top: 2rem;
    text-align: center;
    font-size: 0.9rem;
}


        /* ----- Announcement Ticker ----- */
        .announcement-ticker {
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
            padding: 0.5rem 1rem;
            z-index: 1020; /* Di bawah navbar */
            display: flex;
            align-items: center;
        }
        .announcement-ticker .btn {
            background-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            flex-shrink: 0;
        }
        .announcement-ticker p {
            margin: 0;
            margin-left: 1rem;
            white-space: nowrap;
            overflow: hidden;
        }
        
        /* ----- Parallax Section ----- */
        .parallax-section {
            position: relative;
            padding: 5rem 0;
            background-image: url('https://www.toptal.com/designers/subtlepatterns/uploads/motif-diagonal-striped-brick.png');
            background-attachment: fixed;
            background-position: center;
            background-repeat: repeat;
            background-size: auto;
            color: white;
        }
        .parallax-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(13, 71, 161, 0.92); /* Overlay with primary color */
            z-index: 1;
        }
        .parallax-section .container {
            position: relative;
            z-index: 2;
        }
        .parallax-section .section-title {
            color: white;
            margin-bottom: 1rem;
        }
        .parallax-section .subtitle {
            text-align: center;
            margin-bottom: 3rem;
            color: #e0e0e0;
        }
        .service-card {
            background-color: white;
            color: var(--dark-text);
            padding: 1.5rem 1rem;
            border-radius: 15px;
            text-align: center;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
            transition: transform 0.3s, box-shadow 0.3s;
            text-decoration: none;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100%;
        }
        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.15);
            color: var(--primary-color);
        }
        .service-card img {
            height: 60px;
            margin-bottom: 1rem;
        }
        .service-card h6 {
            font-weight: 600;
            font-size: 0.9rem;
            flex-grow: 1;
        }
        .layanan-search-bar {
            max-width: 700px;
            margin: 4rem auto 0 auto;
        }
        .layanan-search-bar .form-control {
            border-radius: 50px;
            padding-left: 20px;
        }
        
        /* ----- Informasi Publik Section ----- */
.info-publik-section {
    padding: 5rem 0;
    overflow: hidden; /* Mencegah pattern keluar dari container */
}
.info-publik-content h6 {
    font-weight: 600;
    color: var(--secondary-color);
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}
.info-publik-content .info-list li {
    margin-bottom: 0.8rem;
    font-weight: 500;
}
.info-publik-content .info-list li i {
    color: var(--secondary-color);
    font-size: 1.2rem;
}

.image-collage {
    position: relative;
    min-height: 400px;
}
.dots-pattern {
    position: absolute;
    top: -20px;
    left: -20px;
    width: 100px;
    height: 100px;
    background-image: radial-gradient(circle, var(--primary-color) 1.5px, transparent 1.5px);
    background-size: 15px 15px;
    z-index: 1;
    opacity: 0.3;
}
.collage-img {
    position: absolute;
    border-radius: 15px;
    box-shadow: 0 10px 30px rgba(0,0,0,0.1);
    object-fit: cover;
}
.collage-img-1 { /* Top-right image */
    width: 65%;
    top: 0;
    right: 0;
    z-index: 2;
}
.collage-img-2 { /* Bottom-left image */
    width: 55%;
    bottom: 0;
    left: 0;
    z-index: 3;
}
.data-circle {
    position: absolute;
    width: 160px;
    height: 160px;
    background-color: var(--primary-color);
    color: white;
    border-radius: 50%;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    text-align: center;
    z-index: 4;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    box-shadow: 0 0 0 10px rgba(255,255,255,0.5);
}
.data-circle span {
    font-size: 2.8rem;
    font-weight: 700;
    line-height: 1;
}
.data-circle p {
    margin: 0;
    font-size: 1rem;
}
/* Responsive for collage */
@media (max-width: 991px) {
    .image-collage {
        margin-top: 3rem;
        min-height: 350px; /* Adjust height for smaller screens */
    }
    .data-circle {
        width: 140px;
        height: 140px;
    }
    .data-circle span {
        font-size: 2.2rem;
    }
}

/* ----- Akses Cepat Section ----- */
.akses-cepat-section {
    padding: 5rem 0;
    background-color: #f8f9fa; /* Warna latar belakang terang */
    position: relative;
    overflow: hidden;
}

/* Pola dot di background */
.akses-cepat-section::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-image: radial-gradient(circle, #dee2e6 1px, transparent 1px);
    background-size: 20px 20px;
    opacity: 0.5;
}

.header-akses-cepat {
    text-align: center;
    margin-bottom: 3rem;
}

.header-akses-cepat h6 {
    font-weight: 600;
    color: var(--secondary-color);
    letter-spacing: 1px;
    margin-bottom: 0.5rem;
}

.header-akses-cepat h2 {
    font-weight: 700;
    color: var(--primary-color);
}

.akses-card {
    display: block;
    border-radius: 20px;
    overflow: hidden;
    position: relative;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease-in-out;
    text-decoration: none;
    height: 150px;
    background-color: white;
}

.akses-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
}

.akses-card img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

/* Styling khusus untuk tiap kartu */
.card-qris, .card-geoportal {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    color: white;
}
.card-qris {
    background: linear-gradient(45deg, #0052D4, #4364F7, #6FB1FC);
}
.card-geoportal {
    background: linear-gradient(45deg, #c0392b, #e74c3c);
}

.card-qris i, .card-geoportal i {
    font-size: 3rem;
    margin-bottom: 0.5rem;
}

.card-qris h5, .card-geoportal h5 {
    font-weight: 600;
    font-size: 1.1rem;
    margin: 0;
}
.card-qris p {
    font-size: 0.8rem;
    margin: 0;
    opacity: 0.8;
}

/* Responsive */
@media (max-width: 767px) {
    .akses-card {
        height: 120px; /* Sedikit lebih kecil di mobile */
    }
}



        /* ----- Responsive Adjustments ----- */
        @media (max-width: 768px) {
            .hero-slider h1 {
                font-size: 2rem;
            }
            .hero-slider p {
                font-size: 1rem;
            }
            .navbar-brand .logo-text .title {
                font-size: 0.8rem;
            }
            .navbar-brand .logo-text .subtitle {
                font-size: 1rem;
            }
            .quick-access-item {
                margin-bottom: 20px;
            }
        }

/* ----- Pejabat Section ----- */
.pejabat-section {
    padding: 5rem 0;
    background-color: #ffffff; /* Latar belakang putih bersih */
}

.pejabat-card {
    position: relative;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
}

.pejabat-card:hover {
    transform: translateY(-10px);
    box-shadow: 0 12px 35px rgba(0,0,0,0.15);
}

.pejabat-card img {
    width: 100%;
    display: block;
    aspect-ratio: 4/5; /* Menjaga rasio foto potret */
    object-fit: cover;
}

.pejabat-info {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
    color: white;
    padding: 2.5rem 1.5rem 1.5rem 1.5rem;
    text-align: center;
    transition: background 0.3s;
}

.pejabat-info h5 {
    font-weight: 600;
    margin-bottom: 0.25rem;
    font-size: 1.1rem;
}

.pejabat-info p {
    margin: 0;
    font-size: 0.9rem;
    opacity: 0.8;
}

.pejabat-section .btn-outline-primary {
    /* Pastikan variabel --primary-color sudah terdefinisi di file CSS utama Anda */
    border-color: var(--primary-color, #0d6efd);
    color: var(--primary-color, #0d6efd);
    font-weight: 600;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    transition: all 0.3s;
}

.pejabat-section .btn-outline-primary:hover {
    background-color: var(--primary-color, #0d6efd);
    color: white;
}

.news .btn-outline-primary {
    /* Pastikan variabel --primary-color sudah terdefinisi di file CSS utama Anda */
    border-color: var(--primary-color, #0d6efd);
    color: var(--primary-color, #0d6efd);
    font-weight: 600;
    padding: 0.8rem 2rem;
    border-radius: 50px;
    transition: all 0.3s;
} 
.news .btn-outline-primary:hover {
    background-color: var(--primary-color, #0d6efd);
    color: white;
}
        
    </style>
</head>
<body>

    <!-- Header / Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <!-- Ganti logo.png dengan link logo Anda -->
                    <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                    <div class="logo-text">
                        <span class="title">Pemerintah Kabupaten Kendal</span>
                        <span class="subtitle">Desa Tampingan</span>
                    </div>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto">
                        <li class="nav-item">
                            <a class="nav-link active" aria-current="page" href="#">Beranda</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="profilDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Profil
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="profilDropdown">
                                <li><a class="dropdown-item" href="#">Visi & Misi</a></li>
                                <li><a class="dropdown-item" href="#">Sejarah</a></li>
                                <li><a class="dropdown-item" href="#">Struktur Organisasi</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Layanan Publik</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Berita</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Kontak</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="https://tampinganboja.com/admin/">Admin</a>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    </header>

    <main style="padding-top: 80px;"> <!-- Padding top seukuran navbar -->
        
        <!-- Hero Section with Auto Slider -->
        <section class="hero-slider">
            <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
                <div class="carousel-indicators">
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1" aria-label="Slide 2"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="2" aria-label="Slide 3"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="3" aria-label="Slide 4"></button>
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="4" aria-label="Slide 5"></button>
                </div>
                <div class="carousel-inner">
                    <!-- Ganti URL gambar dengan gambar yang relevan -->
                    <!--<div class="carousel-item active">-->
                    <!--    <img src="https://boja.kendalkab.go.id/style_template/img/bg-img/1726113898FOTO BERSAMA KADES DAN PERADES DESA BOJA 2.jpeg" class="d-block w-100" alt="Kantor Desa Tampingan">-->
                    <!--</div>-->
                    <div class="carousel-item active">
                        <img src="img/kkn2.png" class="d-block w-100" alt="Potensi Wisata">
                    </div>
                    <div class="carousel-item">
                        <img src="https://boja.kendalkab.go.id/style_template/img/bg-img/1734060330PATUNG NYI PANDANSARI.jpg" class="d-block w-100" alt="Pelayanan Masyarakat">
                    </div>
                    <div class="carousel-item">
                        <img src="img/kkn1.png" class="d-block w-100" alt="Potensi Wisata">
                    </div>
                    <div class="carousel-item">
                        <img src="img/kkn3.png" class="d-block w-100" alt="Potensi Wisata">
                    </div>
                    <div class="carousel-item">
                        <img src="img/kkn4.png" class="d-block w-100" alt="Potensi Wisata">
                    </div>
                    
                </div>
            </div>
            <div class="carousel-caption-overlay">
                <h1>Selamat Datang di Desa Tampingan</h1>
                <p>Website resmi untuk informasi dan layanan publik Desa Tampingan, Kabupaten Kendal.</p>
                <div class="search-bar">
                    <form class="d-flex">
                        <input class="form-control me-2" type="search" placeholder="Cari informasi seputar Desa Tampingan..." aria-label="Search">
                        <button class="btn btn-warning" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>
            </div>
        </section>

        <!-- Quick Access Icon Bar -->
        <section class="quick-access">
            <div class="container">
                <div class="row">
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-calendar-event"></i></div>
                            <h6>Agenda</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-people-fill"></i></div>
                            <h6>Layanan Kependudukan</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-headset"></i></div>
                            <h6>Aduan Warga</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-file-earmark-arrow-down"></i></div>
                            <h6>Download</h6>
                        </a>
                    </div>
                     <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-bar-chart-line"></i></div>
                            <h6>Infografis</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="quick-access-item">
                            <div class="icon-wrapper"><i class="bi bi-camera-fill"></i></div>
                            <h6>Galeri Foto</h6>
                        </a>
                    </div>
                </div>
            </div>
        </section>

        
        
        <!-- Parallax Online Services Section -->
        <section class="parallax-section">
            <div class="container">
                <h2 class="section-title">LAYANAN ONLINE</h2>
                <p class="subtitle">Daftar Layanan Online Pemerintah Desa Tampingan Untuk Masyarakat</p>
                <div class="row g-4 justify-content-center">
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/3498db?text=ICON" alt="Layanan 1">
                            <h6>Sistem Informasi Kependudukan</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/2ecc71?text=ICON" alt="Layanan 2">
                            <h6>Lapor Boja</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/e74c3c?text=ICON" alt="Layanan 3">
                            <h6>E-Procurement</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/f1c40f?text=ICON" alt="Layanan 4">
                            <h6>Sistem Perizinan Online</h6>
                        </a>
                    </div>
                    <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/9b59b6?text=ICON" alt="Layanan 5">
                            <h6>Informasi Tata Ruang</h6>
                        </a>
                    </div>
                     <div class="col-6 col-md-4 col-lg-2">
                        <a href="#" class="service-card">
                            <img src="https://placehold.co/100x100/ffffff/1abc9c?text=ICON" alt="Layanan 6">
                            <h6>Informasi Eksekutif</h6>
                        </a>
                    </div>
                </div>
                <div class="layanan-search-bar">
                     <form class="d-flex">
                        <input class="form-control form-control-lg" type="search" placeholder="Temukan Layanan Kami..." aria-label="Search">
                    </form>
                </div>
            </div>
        </section>
        
        <!-- Informasi Publik Section -->
<section class="info-publik-section bg-white">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 info-publik-content mb-5 mb-lg-0">
                <h6>PPID</h6>
                <h2 class="section-title text-start" style="margin-bottom: 1.5rem;">INFORMASI PUBLIK</h2>
                <p class="text-muted">
                    Pemerintah Desa Tampingan menyediakan berbagai informasi sebagai bentuk dukungan terhadap transparansi data. Masyarakat dapat pula mengajukan permohonan informasi yang dibutuhkan apabila informasi tersebut belum tersedia.
                </p>
                <ul class="info-list list-unstyled">
                    <li class="d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i> Profil</li>
                    <li class="d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i> Informasi Publik</li>
                    <li class="d-flex align-items-center"><i class="bi bi-check-circle-fill me-2"></i> Permohonan Informasi</li>
                </ul>
            </div>
            <div class="col-lg-6">
                <div class="image-collage">
                    <div class="dots-pattern"></div>
                    <img src="https://i.pinimg.com/1200x/52/7a/83/527a834c1678ccb9ec4f3bf0ab15d819.jpg" alt="Informasi Publik 1" class="collage-img collage-img-1">
                    <img src="https://i.pinimg.com/736x/f8/bb/26/f8bb26f7387644df73e2660ab988ea81.jpg" alt="Informasi Publik 2" class="collage-img collage-img-2">
                    <div class="data-circle">
                        <span>300+</span>
                        <p>Total Data</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Pimpinan Section -->
<section class="pejabat-section">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">PIMPINAN DESA TAMPINGAN</h2>
            <p class="text-muted">Mengenal lebih dekat para pimpinan di lingkungan Desa Tampingan.</p>
        </div>
        <div class="row justify-content-center g-4">

            <?php if ($result_pejabat && $result_pejabat->num_rows > 0): ?>
                <?php while ($pejabat = $result_pejabat->fetch_assoc()): ?>
                    <!-- Card Pejabat akan di-generate oleh loop ini -->
                    <div class="col-lg-3 col-md-6">
                        <div class="pejabat-card">
                            <img src="<?php echo $upload_dir . htmlspecialchars($pejabat['foto']); ?>" alt="Foto <?php echo htmlspecialchars($pejabat['nama_pejabat']); ?>">
                            <div class="pejabat-info">
                                <h5><?php echo htmlspecialchars($pejabat['nama_pejabat']); ?></h5>
                                <p><?php echo htmlspecialchars($pejabat['jabatan']); ?></p>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <!-- Pesan ini akan tampil jika tidak ada data di database -->
                <div class="col-12">
                    <p class="text-center text-muted">Data pimpinan belum tersedia saat ini.</p>
                </div>
            <?php endif; ?>

        </div>
        <div class="text-center mt-5">
            <a href="semua-pejabat.php" class="btn btn-outline-primary btn-lg">Lihat Semua Pejabat</a>
        </div>
    </div>
</section>



<!-- Akses Cepat Section -->
<section class="akses-cepat-section">
    <div class="container">
        <div class="header-akses-cepat">
            <h6>QUICK ACCESS</h6>
            <h2>AKSES CEPAT</h2>
        </div>
        <div class="row g-4">
            <!-- Card 1: Smart Regency -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-smart-regency">
                    <img src="https://placehold.co/400x250/c0392b/ffffff?text=Smart+Regency" alt="Smart Regency">
                </a>
            </div>
            <!-- Card 2: QR Code Kinerja -->
            <div class="col-lg-3 col-md-4 col-6">
                 <a href="#" class="akses-card card-qr">
                    <img src="https://placehold.co/400x250/ffffff/333333?text=QR+CODE" alt="QR Code Kinerja">
                </a>
            </div>
            <!-- Card 3: QRIS -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-qris">
                    <i class="bi bi-qr-code-scan"></i>
                    <h5>QRIS</h5>
                    <p>PAJAK & RETRIBUSI</p>
                </a>
            </div>
            <!-- Card 4: Digital Service -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-digital-service">
                    <img src="https://placehold.co/400x250/3498db/ffffff?text=Digital+Service" alt="Digital Service">
                </a>
            </div>
            <!-- Card 5: Survey -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-survey">
                     <img src="https://placehold.co/400x250/f1c40f/ffffff?text=Survey+Pemerintahan" alt="Survey">
                </a>
            </div>
             <!-- Card 6: Transparansi Keuangan -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-transparansi">
                    <img src="https://placehold.co/400x250/2ecc71/ffffff?text=Transparansi+Keuangan" alt="Transparansi Keuangan">
                </a>
            </div>
            <!-- Card 7: Geoportal -->
            <div class="col-lg-3 col-md-4 col-6">
                <a href="#" class="akses-card card-geoportal">
                    <i class="bi bi-geo-alt-fill"></i>
                    <h5>GEOPORTAL</h5>
                </a>
            </div>
            <!-- Card 8: SAKIP Publik -->
            <div class="col-lg-3 col-md-4 col-6">
                 <a href="#" class="akses-card card-sakip">
                    <img src="https://placehold.co/400x250/9b59b6/ffffff?text=SAKIP+Publik" alt="SAKIP Publik">
                </a>
            </div>
        </div>
    </div>
</section>

<!-- News Section -->
<section id="news" class="news py-5">
    <div class="container">
        <h2 class="section-title">BERITA TERKINI</h2>
        <div class="row">
            <?php if ($hasil_berita_terkini->num_rows > 0): ?>
                <?php while($berita = $hasil_berita_terkini->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card news-card h-100">
                            <img src="admin/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($berita['judul']); ?>" style="height: 200px; object-fit: cover;">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($berita['judul']); ?></h5>
                                <p class="card-text text-muted"><small><i class="bi bi-calendar"></i> <?php echo date('d F Y', strtotime($berita['tanggal_publish'])); ?></small></p>
                                <p class="card-text">
                                    <?php echo buat_ringkasan($berita['isi']); ?>
                                </p>
                                <!-- Arahkan ke halaman detail, misalnya detail.php -->
                                <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>" class="btn btn-outline-primary mt-auto">Baca Selengkapnya</a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <p class="text-center">Belum ada berita untuk ditampilkan.</p>
                </div>
            <?php endif; ?>
        </div>
        <div class="text-center mt-5">
            <a href="semua_berita.php" class="btn btn-outline-primary btn-lg">Lihat Semua Berita</a>
        </div>
    </div>
</section>

    </main>

    <!-- Footer Section -->
<footer class="main-footer">
    <div class="container">
        <div class="row">
            <!-- Column 1: Info Kontak -->
            <div class="col-lg-3 col-md-6 mb-4">
                <div class="footer-info-brand mb-3">
                    <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                    <div class="logo-text">
                        <span>Pemerintah Kabupaten Kendal</span>
                        <h6>Desa Tampingan</h6>
                    </div>
                </div>
                <ul class="list-unstyled info-list">
                    <li class="d-flex"><i class="bi bi-geo-alt-fill me-3"></i><span>JL.Raya Boja. Susukan KM.1, Kode Pos. 51381</span></li>
                    <li class="d-flex"><i class="bi bi-telephone-fill me-3"></i><span>(0294) 571073</span></li>
                    <li class="d-flex"><i class="bi bi-envelope-fill me-3"></i><span>tampinganboja@gmail.com</span></li>
                </ul>
                <h6 class="mt-4">FOLLOW US</h6>
                <div class="social-links">
                    <a href="#" class="me-2"><i class="bi bi-twitter-x"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-facebook"></i></a>
                    <a href="#" class="me-2"><i class="bi bi-instagram"></i></a>
                </div>
            </div>

            <!-- Column 2: Peta Lokasi -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>PETA LOKASI</h5>
                <div class="map-container">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.149959794197!2d110.28603260000001!3d-7.108613999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7062fe7f9f2091%3A0x10df70e8b27e31f0!2sKantor%20Kelurahan%20Tampingan!5e0!3m2!1sid!2sid!4v1758382263096!5m2!1sid!2sid" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <!--<iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.149959794197!2d110.28603260000001!3d-7.108613999999999!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e7062fe7f9f2091%3A0x10df70e8b27e31f0!2sKantor%20Kelurahan%20Tampingan!5e0!3m2!1sid!2sid!4v1758382263096!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>-->
                </div>
            </div>

            <!-- Column 3: Statistik Pengunjung -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>STATISTIK PENGUNJUNG</h5>
                <ul class="list-unstyled visitor-stats">
                    <li><span>Online Visitors:</span> <strong>3</strong></li>
                    <li><span>Today's Views:</span> <strong>152</strong></li>
                    <li><span>Last 7 Days Views:</span> <strong>2,189</strong></li>
                    <li><span>Total Views:</span> <strong>48,731</strong></li>
                </ul>
            </div>

            <!-- Column 4: Survey -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5>BAGAIMANA PENILAIAN ANDA?</h5>
                <p>Terhadap Kinerja Pelayanan Publik Desa Tampingan</p>
                <form class="survey-form">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="survey" id="sangatBaik">
                        <label class="form-check-label" for="sangatBaik">Sangat Baik</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="survey" id="baik">
                        <label class="form-check-label" for="baik">Baik</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="survey" id="cukup">
                        <label class="form-check-label" for="cukup">Cukup</label>
                    </div>
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="radio" name="survey" id="kurang">
                        <label class="form-check-label" for="kurang">Kurang</label>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Pilih</button>
                </form>
            </div>
        </div>
        <div class="footer-bottom mb-4">
            <p>&copy; 2025 Hak Cipta Dilindungi | Desa Tampingan, Kabupaten Kendal<br>Develop by: <a href="#">Mahasiswa KKN UAA 2025</a></p>
        </div>
    </div>
</footer>
    <div class="announcement-ticker fixed-bottom">
        <a href="#" class="btn btn-sm">PENGUMUMAN</a>
        <marquee><p>Pengumuman Seleksi Penerimaan Pegawai Pemerintah Non Pegawai Negeri (PPNPN) Desa Tampingan Tahun 2025</p></marquee>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>


    <script>
        // Letakkan semua kode custom Anda di sini agar rapi
        console.log("Website Desa Tampingan berhasil dimuat.");
        
        // (Jika Anda punya kode lain seperti navbar scroll atau galeri AJAX, letakkan di sini juga)
    </script>


    <?php if (isset($maintenanceMode) && $maintenanceMode === true): ?>
    <script>
    document.addEventListener('DOMContentLoaded', function() {

        // Tentukan waktu target: Besok, jam 8:00:00 pagi
        const targetDate = new Date();
        targetDate.setDate(targetDate.getDate() + 1); // Set tanggal menjadi besok
        targetDate.setHours(8, 0, 0, 0); // Set jam ke 8:00:00 pagi

        // Fungsi untuk memformat angka (misal: 7 -> 07)
        const formatNumber = (num) => num < 10 ? '0' + num : num;

        // Tampilkan popup SweetAlert
        Swal.fire({
            title: '<br>Website Dalam Perbaikan',
            html: `
                <p style="margin-bottom: 1rem; font-size: 1.1rem;">
                    Mohon maaf atas ketidaknyamanannya. Kami sedang melakukan beberapa pemeliharaan.
                </p>
                <p style="margin-bottom: 1.5rem;">
                    Situs akan kembali normal dalam:
                </p>
                <div id="countdown-timer" style="font-size: 2.5rem; font-weight: bold; color: #007bff; letter-spacing: 2px;">
                    Memuat...
                </div>
            `,
            // Opsi agar popup tidak bisa ditutup
            allowOutsideClick: false,
            allowEscapeKey: false,
            showConfirmButton: false,
            
            // Mulai timer setelah popup ditampilkan
            didOpen: () => {
                const timerElement = document.getElementById('countdown-timer');

                const countdownInterval = setInterval(() => {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    // Jika waktu sudah habis
                    if (distance < 0) {
                        clearInterval(countdownInterval);
                        Swal.close(); // Tutup popup
                        return;
                    }

                    // Kalkulasi waktu
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    // Tampilkan di dalam popup
                    timerElement.innerHTML = `${formatNumber(hours)}:${formatNumber(minutes)}:${formatNumber(seconds)}`;

                }, 1000); // Update setiap 1 detik
            }
        });

    });
    </script>
    <?php endif; ?>

</body>
</html>


