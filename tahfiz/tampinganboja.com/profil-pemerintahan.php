<?php
// Panggil file koneksi untuk variabel $maintenanceMode dan koneksi database jika diperlukan
require_once 'koneksi.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Pemerintahan - Desa Tampingan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Mengambil variabel warna dari CSS utama Anda */
        :root {
            --primary-color: #0d47a1; /* Contoh warna primer, sesuaikan jika perlu */
            --dark-text: #333;
            --light-gray: #f8f9fa;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #ffffff;
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
            padding: 10rem 0;
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* ----- Content Styling ----- */
        .content-section {
            padding: 4rem 0;
            opacity: 0;
            transform: translateY(30px);
            transition: opacity 0.8s ease, transform 0.8s ease;
        }
        .content-section.is-visible {
            opacity: 1;
            transform: translateY(0);
        }
        .content-section:nth-child(odd) {
            background-color: var(--light-gray);
        }
        .section-heading {
            margin-bottom: 3rem;
            font-weight: 700;
            color: var(--dark-text);
        }
        .sejarah-img {
            width: 100%;
            height: auto;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .text-lead {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        /* ----- Visi Misi Card (reuse from homepage) ----- */
        .vm-card {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px;
            height: 100%;
            display: flex;
            align-items: flex-start;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border-left: 5px solid var(--primary-color);
        }
        .vm-icon { font-size: 2.5rem; color: var(--primary-color); margin-right: 20px; padding-top: 5px; }
        .vm-content h3 { font-weight: 700; color: var(--dark-text); margin-bottom: 15px; }
        .vm-content p, .vm-content ul { color: #555; line-height: 1.8; margin-bottom: 0; }
        .vm-content ul { padding-left: 20px; }
        .vm-content ul li { margin-bottom: 10px; }
        
        /* ----- Kelembagaan ----- */
        .struktur-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            padding: 10px;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        }
        
   .geografis-info-list {
        list-style: none;
        padding-left: 0;
    }
    .geografis-info-list li {
        display: flex;
        align-items: flex-start;
        margin-bottom: 1rem;
        font-size: 1rem;
    }
    .geografis-info-list .icon {
        font-size: 1.2rem;
        color: var(--primary-color);
        margin-right: 15px;
        margin-top: 4px;
    }
    .geografis-info-list .label {
        font-weight: 600;
        color: var(--dark-text);
        display: block;
    }
    .geografis-info-list .value {
        color: #555;
    }
    .table-geografis {
        box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden; /* Agar border-radius bekerja dengan thead */
    }
    .table-geografis thead {
        background-color: var(--primary-color);
        color: white;
        font-weight: 600;
    }
        
        section {
  scroll-margin-top: 80px; /* Sesuaikan nilai ini dengan tinggi navbar Anda */
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

    <main style="padding-top: 80px;">
     <div class="overflow-hidden">

        <section class="page-header">
            <div class="container">
                <h1 class="display-4">Profil Pemerintahan Desa</h1>
                <p class="lead">Mengenal lebih dekat landasan, tujuan, dan struktur pemerintahan Desa Tampingan.</p>
            </div>
        </section>

        <section id="sejarah" class="content-section">
            <div class="container">
                <h2 class="text-center section-heading">Sejarah Desa</h2>
                <div class="row align-items-center g-5">
                    <div class="col-lg-5">
                        <img src="https://discoveryindochina.com/wp-content/uploads/2016/10/Tra-Que-Village5.jpg" alt="Sejarah Desa Tampingan" class="sejarah-img">
                    </div>
                    <div class="col-lg-7">
                        <p class="text-lead">
                            Nama Desa Tampingan diambil dari tokoh yang pertama kali membuka 
lahan pemukiman di Tampingan, yaitu Ki Ageng Tampingan yang hidup pada 
masa Ki Ageng Pandan Arang yang menjadi tokoh keagamaan pertama di 
Semarang. 
                        </p>
                        <p>
                            Kemudian pada dekade berikutnya, pada pertengahan jaman penjajahan 
Belanda, baru muncul kelompok pemukiman di Tampingan menjadi bentuk 
pemerintahan.
                        </p>
                    </div>
                </div>
            </div>
        </section>

<section id="visimisi" class="content-section">
    <div class="container">
        <h2 class="text-center section-heading">Visi & Misi</h2>
        <div class="row g-4">
            <!-- Kolom Visi -->
            <div class="col-lg-12">
                <div class="vm-card">
                    <div class="vm-icon"><i class="bi bi-eye-fill"></i></div>
                    <div class="vm-content">
                        <h3>VISI</h3>
                        <p class="fw-bold fst-italic">“MEMBANGUN TAMPINGAN BERWIBAWA DAN BERMARTABAT”</p>
                        <p>
                            Visi tersebut mengandung pengertian bahwa cita-cita yang akan dituju di masa mendatang yaitu menjadikan Desa Tampingan menjadi desa yang berwibawa dan bermartabat masyarakatnya. Adapun apabila visi tersebut dijabarkan secara rinci sebagai berikut:
                        </p>
                        <ul style="list-style-type: none; padding-left: 0;">
                            <li>
                                <strong>MEMBANGUN TAMPINGAN</strong> adalah bangkit berdiri, berperan aktif memajukan Desa Tampingan dengan melaksanakan kegiatan pembangunan di Desa Tampingan yang meliputi seluruh aspek kehidupan masyarakat Desa Tampingan secara terpadu dengan mengembangkan swadaya gotong-royong.
                            </li>
                            <li class="mt-3">
                                <strong>BERWIBAWA</strong> mengandung arti mempunyai wibawa, sehingga disegani dan dipatuhi. Dalam hal ini dengan visi tersebut, diharapkan Desa Tampingan akan menjadi desa yang disegani oleh desa-desa lain, atau dalam artian kata lain menjadi desa percontohan dalam hal pembangunan.
                            </li>
                            <li class="mt-3">
                                <strong>BERMARTABAT</strong> yaitu mempunyai kedudukan yang tinggi, memiliki harga diri. Martabat berkaitan dengan nilai manusia. Manusia adalah makhluk Tuhan yang paling sempurna dari makhluk lain dan mendapatkan tugas dan wewenang untuk mengurus dunia untuk kesejahteraannya. Sebagai makhluk yang lebih sempurna, maka manusia dituntut untuk selalu menjaga martabatnya dalam setiap posisi dan kegiatan apa saja. Oleh karena itu, semua kebijakan pembangunan dan kiprahnya harus selalu berorientasi kepada penjagaan martabat manusia.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Kolom Misi -->
            <div class="col-lg-12 mt-4">
                 <div class="vm-card">
                    <div class="vm-icon"><i class="bi bi-rocket-takeoff-fill"></i></div>
                    <div class="vm-content">
                        <h3>MISI</h3>
                        <p>
                            Misi Desa Tampingan merupakan turunan dari Visi Desa Tampingan. Misi merupakan tujuan jangka yang lebih pendek dari visi yang menunjang keberhasilan tercapainya sebuah visi. Misi Desa Tampingan adalah:
                        </p>
                        <ol>
                            <li>Membangun ekonomi masyarakat kecil.</li>
                            <li>Membangun BUMDesa dengan membuka berbagai unit usaha.</li>
                            <li>Membuat yayasan sosial yang berbadan hukum.</li>
                            <li>Membangun rumah free Wi-Fi (free dalam pengadaan perangkat).</li>
                            <li>Membangun terminal kecil untuk kendaraan truk warga Desa Tampingan.</li>
                            <li>Membangun pasar rakyat untuk ekonomi menengah ke bawah.</li>
                            <li>Pelayanan administrasi desa yang ramah, cepat dan gratis.</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
        
        <section id="kelembagaan" class="content-section">
            <div class="container">
                <h2 class="text-center section-heading">Struktur Kelembagaan</h2>
                <div class="row justify-content-center">
                    <div class="col-lg-10 text-center">
                        <p class="text-lead mb-5">
                            Berikut adalah bagan struktur organisasi dan kelembagaan resmi Pemerintah Desa Tampingan yang bertugas untuk melayani dan mensejahterakan masyarakat.
                        </p>
                        <img src="/img/struktur.png" alt="Struktur Kelembagaan Desa Tampingan" class="struktur-img">
                    </div>
                </div>
            </div>
        </section>
        
    <section id="geografis" class="content-section">
    <div class="container">
        <h2 class="text-center section-heading">Kondisi Geografis & Kependudukan</h2>
        <div class="row g-5 align-items-center">
            
            <!-- Kolom Kiri: Deskripsi & Batas Wilayah -->
            <div class="col-lg-7">
                <p class="text-lead">
                    Desa Tampingan adalah salah satu desa yang terletak di Kecamatan Boja yang berada di bagian selatan Kabupaten Kendal. Jarak tempuh wilayah Desa Tampingan dari Ibukota Kabupaten Kendal adalah 27 km.
                </p>
                <p>
                    Desa ini memiliki luas wilayah 193,64 Ha, dengan potensi lahan yang produktif di antaranya, persawahan. Pusat pemerintahan Desa Tampingan terletak di Dusun Rejosari RT 01 RW 01 dan untuk menuju Kantor Desa dapat dijangkau dengan kendaraan umum atau jalan kaki.
                </p>
                
                <!-- Konten Tambahan dari Gambar -->
                <!--<h5 class="mt-5 mb-3">Kependudukan & Potensi Strategis</h5>-->
                <p>
                    Pusat pemerintahan Desa Tampingan terletak di Dusun Rejosari RT 01 RW 01 dan untuk menuju Kantor Desa dapat dijangkau dengan kendaraan umum atau jalan kaki karena berada di jalan kabupaten, berhubungan langsung dengan pusat Kecamatan
Boja.
                </p>
                <p>
                    Berdasarkan Profil Desa Tahun 2014-2019, data kependudukan Desa Tampingan per akhir Desember 2019 adalah sejumlah 4.569 jiwa, yang terdiri dari 2.310 jiwa penduduk laki-laki dan 2.259 jiwa penduduk perempuan dengan jumlah KK sebanyak 1.450 KK.
                </p>
                <p>
                    Letak Desa Tampingan yang strategis sebagai jalur antar desa dan antar kecamatan menjadi potensi utama dalam pelaksanaan Agenda Pembangunan. Upaya menjadikan masyarakat yang beriman, bertakwa, berbudaya, dan sentosa adalah prioritas utama pemerintah desa, yang diwujudkan melalui perencanaan pembangunan partisipatif dan musyawarah untuk memenuhi kebutuhan masyarakat.
                </p>
                <!-- Akhir Konten Tambahan -->

                <div class="row mt-5">
                    <div class="col-md-6">
                        <h5 class="mb-3">Jarak & Waktu Tempuh</h5>
                        <ul class="geografis-info-list">
                            <li>
                                <i class="bi bi-geo-alt-fill icon"></i>
                                <div>
                                    <span class="label">Jarak ke Kecamatan</span>
                                    <span class="value">1,5 km (5 menit)</span>
                                </div>
                            </li>
                             <li>
                                <i class="bi bi-signpost-split-fill icon"></i>
                                <div>
                                    <span class="label">Jarak ke Kabupaten</span>
                                    <span class="value">27 km (45 menit)</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                         <h5 class="mb-3">Batas Wilayah</h5>
                         <ul class="geografis-info-list">
                            <li><i class="bi bi-arrow-up-circle-fill icon"></i> <div><span class="label">Utara:</span> <span class="value">Desa Campurejo</span></div></li>
                            <li><i class="bi bi-arrow-right-circle-fill icon"></i> <div><span class="label">Timur:</span> <span class="value">Desa Karangmanggis</span></div></li>
                            <li><i class="bi bi-arrow-down-circle-fill icon"></i> <div><span class="label">Selatan:</span> <span class="value">Desa Salamsari</span></div></li>
                            <li><i class="bi bi-arrow-left-circle-fill icon"></i> <div><span class="label">Barat:</span> <span class="value">Desa Boja</span></div></li>
                         </ul>
                    </div>
                </div>
            </div>

            <!-- Kolom Kanan: Tabel Luas Wilayah -->
            <div class="col-lg-5">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped table-geografis mb-0">
                        <caption class="text-center mb-2">Tabel 1. Luas Wilayah Desa Tampingan</caption>
                        <thead>
                            <tr>
                                <th scope="col">No</th>
                                <th scope="col">Uraian</th>
                                <th scope="col" class="text-end">Luas (Ha)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Permukiman</td>
                                <td class="text-end">45,00</td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Pekarangan</td>
                                <td class="text-end">15,69</td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Tanah Kas Desa</td>
                                <td class="text-end">22,73</td>
                            </tr>
                             <tr>
                                <td>4</td>
                                <td>Persawahan</td>
                                <td class="text-end">93,67</td>
                            </tr>
                            <tr>
                                <td>5</td>
                                <td>Perkebunan</td>
                                <td class="text-end">3,60</td>
                            </tr>
                            <tr>
                                <td>6</td>
                                <td>Fasilitas Umum</td>
                                <td class="text-end">13,40</td>
                            </tr>
                            <tr class="table-primary">
                                <td colspan="2" class="fw-bold">TOTAL LUAS</td>
                                <td class="text-end fw-bold">193,64</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
        
        
        
     </div>
    </main>
    
    <footer class="bg-dark text-white text-center p-4 mt-5">
        <div class="container">
            <p>&copy; 2025 Pemerintah Desa Tampingan. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sections = document.querySelectorAll('.content-section');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('is-visible');
                    }
                });
            }, { threshold: 0.1 });

            sections.forEach(section => {
                observer.observe(section);
            });
        });
    </script>
    
    
    
</body>
</html>