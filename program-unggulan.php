<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Kelas Unggulan - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- CSS Internal -->
    <style>
        /* Font Utama & Basic Style */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f8f9fa;
        }

        /* Style untuk Navbar */
        .navbar {
            transition: background-color 0.4s ease-in-out, padding 0.4s ease-in-out;
        }
        .navbar.scrolled {
            background: linear-gradient(135deg, #198754, #28a745);
            padding: 0.5rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar-nav .nav-link { color: #ffffff; font-weight: 500; }
        .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover { background-color: rgba(255, 255, 255, 0.15); }
        .navbar .dropdown-menu { border-radius: 0.75rem; border: none; box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.1); }
        .navbar .dropdown-item:hover, .navbar .dropdown-item:focus, .navbar .dropdown-item.active { background-color: #198754; color: #ffffff; }
        .btn-ppdb { background-color: #ffffff; color: #198754; font-weight: 600; border-radius: 50px; padding: 0.4rem 1.2rem; transition: all 0.3s ease; }
        .btn-ppdb:hover { background-color: transparent; color: #ffffff; border: 2px solid #ffffff; }

        /* Header Halaman */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1541339907198-e08756dedf3f?q=80&w=2070&auto=format&fit=crop') no-repeat center center;
            background-size: cover;
            padding: 120px 0 60px 0;
            color: #ffffff;
            text-align: center;
        }
        .page-header .page-title { font-size: 3.5rem; font-weight: 700; }
        .page-header .page-subtitle { font-size: 1.2rem; font-weight: 400; }

        /* Konten Halaman Profil Unggulan */
        .content-section { padding: 80px 0; }
        .content-card { background-color: #ffffff; border-radius: 15px; padding: 30px 40px; box-shadow: 0 5px 25px rgba(0,0,0,0.07); }
        .content-card h2.main-title { font-weight: 700; color: #198754; text-align: center; margin-bottom: 8px; }
        .content-card p.main-subtitle { text-align: center; font-weight: 500; color: #6c757d; margin-bottom: 30px; }
        .content-card .intro-text { font-size: 1.05rem; line-height: 1.8; text-align: justify; margin-bottom: 20px; }
        .content-card .section-heading { font-size: 1.8rem; font-weight: 700; color: #198754; text-align: center; }
        
        .section-heading { 
        font-size: 1.8rem;
        font-weight: 700; 
        color: #198754; 
        text-align: center; 
        /*margin-top: 20px; */
        /*margin-bottom: 30px; */
        }
        
        .content-card .sub-heading { font-size: 1.2rem; font-weight: 600; color: #333; margin-bottom: 15px; }
        .content-card ul { padding-left: 20px; margin-bottom: 20px; }
        .content-card ul li { margin-bottom: 10px; color: #555; }

        /* =============================================
           STYLE UNTUK SEKSI KEUNGGULAN & KEGIATAN
           ============================================= */
        .excellence-activities-section {
            padding: 80px 0;
            background-color: #ffffff;
        }
        .excellence-activities-section .section-title {
            font-weight: 700;
            text-transform: uppercase;
        }
        .info-box {
            background-color: #f8f9fa;
            border-radius: 15px;
            padding: 30px;
            /* PERBAIKAN: Menghapus height: 100% agar tinggi menyesuaikan konten */
            border-left: 5px solid #198754;
        }
        .info-box-title {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
        }
        .info-box-subtitle {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 15px;
        }
        .info-list {
            list-style: none;
            padding-left: 0;
            color: #555;
            line-height: 1.8;
        }
        .info-list li {
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
        }
        .info-list li i {
            color: #198754;
            margin-right: 15px;
            margin-top: 6px;
            width: 20px;
            text-align: center;
        }
        .jam-tambahan-box {
            background-color: #e0f2f1;
            border: 1px solid #b2dfdb;
            border-radius: 10px;
            padding: 20px;
            margin: 25px 0;
            text-align: center;
        }
        .jam-tambahan-box h4 {
            font-weight: 600;
            color: #00695C;
            margin-bottom: 10px;
        }
        .jam-tambahan-box p {
            margin-bottom: 0;
            color: #00796b;
            font-weight: 500;
            line-height: 1.7;
        }
        
        /* =============================================
           STYLE UNTUK SEKSI INFO TAMBAHAN
           ============================================= */
        .additional-info-section {
            padding: 0 0 80px 0; /* Padding atas 0 karena menyambung dari section sebelumnya */
            background-color: #ffffff;
        }



    </style>
</head>
<body>

    <!-- Navbar -->
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
                        <a class="nav-link active" aria-current="page" href="https://mtsn1waykanan.com/">Beranda</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/profil.php">Profil Madrasah</a></li>
                            <!--<li><a class="dropdown-item" href="visi-misi-tujuan.html">Visi, Misi & Tujuan</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">Sejarah</a></li>-->
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="#">Profil Guru</a></li>
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com#galeri">Galeri</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com#berita">Berita</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/layanan">Layanan</a>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/unggulan">Program Unggulan</a>
                    </li>
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tata-tertib.php">Tata Tertib Madrasah</a></li>
                            
                            <!--<li><a class="dropdown-item" href="https://asndigital.bkn.go.id/">ASN</a></li>-->
                            <!--<li><a class="dropdown-item" href="">PTSP</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">PTSP</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">Legalisir Online</a></li>-->
                        </ul>
                    </li>
                    
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <!-- Ganti tanda '#' dengan link halaman PPDB Anda -->
                        <a class="btn btn-ppdb" href="https://mtsn1waykanan.com/ppdb">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Program Unggulan</h1>
            <p class="page-subtitle">Informasi Mengenai Kelas Unggul (Excellent Class)</p>
        </div>
    </header>

    <!-- Konten Utama Halaman -->
    <main class="content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="content-card">
                        <h2 class="main-title">PROFIL PROGRAM KELAS UNGGULAN</h2>
                        <p class="main-subtitle">MTS NEGERI 1 WAY KANAN</p>

                        <p class="intro-text">
                            Kelas Unggul (Excellent Class) MTs Negeri 1 Way Kanan merupakan suatu program yang menitik beratkan keunggulan siswa pada aspek pemahaman, penguasaan, sekaligus keterampilan.
                        </p>
                        <p class="intro-text">
                            Pembelajaran unggul adalah proses pembelajaran yang membuat siswa senang, betah dan nikmat belajar.
                        </p>
                        <p class="intro-text">
                            Proses pembelajaran unggul adalah proses yang dapat memunculkan kegiatan belajar mengajar yang menggairahkan, dengan pembelajaran unggul prestasi puncak dapat dicapai para peserta didik dengan cara mengembangkan kemampuan peserta didik dan merubah kondisi-kondisi pembelajaran dengan kurikulum yang sesuai, guru yang kompeten, adanya ciri khusus dari kelas reguler, dukungan masyarakat dan keterlibatan orang tua, disiplin yang ketat, keterikatan pada nilai-nilai budaya madrasah, akhlak dan kepribadian (karakter) yang unggul, pembiayaan yang memadai.
                        </p>

                        <h2 class="section-heading mt-5 mb-3">FAKTOR PENDUKUNG</h2>

                        <div>
                            <h4 class="sub-heading">a. Seleksi Peserta Didik</h4>
                            <ul>
                                <li>Untuk dapat menjadi calon peserta didik kelas unggulan wajib menduduki peringkat 10 (sepuluh) besar kelas V dan VI pada jenjang sekolah sebelumnya.</li>
                                <li>Memiliki nilai raport mata pelajaran Agama Islam, Matematika dan IPA kelas V-VI tidak kurang dari 75.</li>
                                <li>Dinyatakan lulus tes tertulis dan wawancara.</li>
                            </ul>
                        </div>

                        <div>
                            <h4 class="sub-heading">b. Kualifikasi Pendidik</h4>
                            <p>Guru kelas unggulan ialah guru yang memenuhi persyaratan sesuai undang-undang, yang memandu bidang studi khusus atau mata pelajaran tertentu di kelas unggulan.</p>
                        </div>

                        <div>
                            <h4 class="sub-heading">c. Sumber Dana</h4>
                            <p>Dana yang diperlukan bagi kelancaran penyelenggaraan kelas unggulan ini berasal dari dana madrasah, iuran orang tua/wali dari peserta didik kelas unggulan (SPP), dan dana lainnya yang bersifat tidak mengikat.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        
        
        <section class="excellence-activities-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <!-- Judul Section Keunggulan -->
                    <div class="text-center mb-3">
                        <h2 class="section-heading">KEUNGGULAN</h2>
                    </div>

                    <!-- Bagian Akademik & Moral -->
                    <div class="info-box mb-5">
                        <h3 class="info-box-title">I. Akademik</h3>
                        <ul class="info-list">
                            <li><i class="fas fa-book-open"></i> Empat mata pelajaran yang diunggulkan: Bahasa Inggris, Bahasa Arab, Matematika, dan IPA.</li>
                            <li><i class="fas fa-laptop-code"></i> Dua mata pelajaran tambahan: TIK dan Tahfidzul Qur’an.</li>
                            <li><i class="fas fa-quran"></i> Target minimal hafal 2 Juz al-Qur’an.</li>
                            <li><i class="fas fa-language"></i> Komunikasi intensif harian dalam Bahasa Inggris dan Bahasa Arab.</li>
                            <li><i class="fas fa-school"></i> Pola belajar “full day school” dari pagi hingga sore.</li>
                        </ul>
                        <div class="jam-tambahan-box">
                            <h4>Jam Tambahan</h4>
                            <p><strong>Senin-Kamis:</strong> 14.30 – 16.15<br><strong>Jum’at-Sabtu:</strong> 13.00 – 14.10</p>
                        </div>
                        <h4 class="info-box-subtitle">Akademik (Lanjutan)</h4>
                        <p>Jam pelajaran tambahan sebagai berikut:</p>
                        <ul class="info-list">
                            <li><i class="fas fa-check-circle"></i> Bahasa Inggris: 3 JP/Minggu</li>
                            <li><i class="fas fa-check-circle"></i> Bahasa Arab: 3 JP/Minggu</li>
                            <li><i class="fas fa-check-circle"></i> Matematika: 3 JP/Minggu</li>
                            <li><i class="fas fa-check-circle"></i> TIK: 3 JP/Minggu</li>
                            <li><i class="fas fa-check-circle"></i> IPA: 2 JP/Minggu</li>
                            <li><i class="fas fa-check-circle"></i> Tahfidzul Qur’an: 2 JP/Minggu</li>
                        </ul>
                    </div>
                    
                    <div class="info-box mb-5">
                        <h3 class="info-box-title">Moral</h3>
                        <ul class="info-list">
                            <li><i class="fas fa-heart"></i> Pemahaman agama komprehensif: Salīmul ‘Aqīdah (Akidah Lurus), Shahīhul ‘Ibādah (Ibadahnya Baik), dan Matīnul Khuluq (Akhlaknya Terpuji).</li>
                        </ul>
                    </div>

                    <!-- Judul Section Kegiatan -->
                    <div class="text-center mb-3">
                        <h2 class="section-heading">KEGIATAN</h2>
                    </div>
                    
                    <!-- Bagian Kegiatan -->
                    <div class="info-box">
                         <h3 class="info-box-title">a. Harian/Mingguan</h3>
                         <ul class="info-list">
                             <li><i class="fas fa-mosque"></i> Shalat Dhuha dan Ashar berjama’ah.</li>
                             <li><i class="fas fa-hands-praying"></i> Zikir, Wirid dan doa.</li>
                             <li><i class="fas fa-book"></i> Tadarus Al-Qur’an sebelum belajar.</li>
                             <li><i class="fas fa-users"></i> Pembiasaan menebarkan salam dan belajar kelompok.</li>
                         </ul>
                         <h3 class="info-box-title mt-4">b. Bulanan</h3>
                         <ul class="info-list">
                             <li><i class="fas fa-comments"></i> Uji kemampuan berbahasa Inggris.</li>
                             <li><i class="fas fa-comments"></i> Uji kemampuan berbahasa Arab.</li>
                             <li><i class="fas fa-star"></i> Setoran hafalan al-Qur’an.</li>
                         </ul>
                         <h3 class="info-box-title mt-4">c. Tahunan</h3>
                         <ul class="info-list">
                             <!--<li><i class="fas fa-comments"></i> Uji kemampuan berbahasa Inggris.</li>-->
                             <!--<li><i class="fas fa-comments"></i> Uji kemampuan berbahasa Arab.</li>-->
                             <li><i class="fas fa-star"></i> Perform/ penampilan siswa kelas unggulan.</li>
                         </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- SECTION BARU: TOLAK UKUR, DESAIN KELAS, DLL -->
    <!-- ============================================= -->
    <section class="additional-info-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">

                    <!-- Tolak Ukur Keberhasilan -->
                    <div class="info-box mb-5">
                        <h2 class="section-heading text-center mb-4">TOLAK UKUR KEBERHASILAN</h2>
                        <ul class="info-list">
                            <li><i class="fas fa-bullseye"></i> Keterampilan siswa dalam berbahasa Inggris dan Bahasa Arab menyimak, berbicara, membaca, dan menulis.</li>
                            <li><i class="fas fa-bullseye"></i> Peserta didik siap menjadi duta/utusan madrasah dalam kegiatan kompetisi/olimpiade di tingkat kabupaten, provinsi dan nasional.</li>
                            <li><i class="fas fa-bullseye"></i> Nilai Akhlak dan Kepribadian (Karakter) adalah Sangat Baik.</li>
                            <li><i class="fas fa-bullseye"></i> KKM untuk setiap mata pelajaran adalah 80.</li>
                        </ul>
                    </div>

                    <!-- Desain Kelas -->
                    <div class="info-box mb-5">
                        <h2 class="section-heading text-center mb-4">DESAIN KELAS</h2>
                        <ul class="info-list">
                            <li><i class="fas fa-check"></i> Setiap siswa disediakan tempat duduk satu meja/kursi satu siswa.</li>
                            <li><i class="fas fa-check"></i> Kelas didesain menjadi ruangan yang memiliki pendingin ruangan/AC.</li>
                            <li><i class="fas fa-check"></i> Tersedia LCD Proyektor sebagai media pembelajaran.</li>
                            <li><i class="fas fa-check"></i> Tersedia Despenzer air minum, gelas/botol minum untuk setiap siswa dan minuman sehat seperti teh manis, Energen dan lain-lain.</li>
                            <li><i class="fas fa-check"></i> Tersedia loker/lemari penyimpanan untuk setiap siswa, rak sepatu dan tempat sampah.</li>
                            <li><i class="fas fa-check"></i> Tersedia lemari buku, serta pojok buku referensi, kamus bahasa Inggris, Kamus Bahasa Arab dan tempat istirahat.</li>
                            <li><i class="fas fa-check"></i> Jumlah peserta didik per kelas Maximal 25 peserta didik.</li>
                        </ul>
                    </div>

                    <!-- Lain-lain -->
                    <div class="info-box">
                        <h2 class="section-heading text-center mb-4">LAIN-LAIN</h2>
                        <ul class="info-list">
                            <li><i class="fas fa-star"></i> Siswa kelas unggulan diberikan waktu untuk mengikuti kegiatan ekstrakurikuler sesuai minat dan bakatnya.</li>
                        </ul>
                    </div>

                </div>
            </div>
        </div>
    </section>


        
        
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <!-- Script JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk Navbar
        const navbar = document.querySelector('.navbar');
        function updateNavbar() {
            navbar.classList.add('scrolled');
        }
        document.addEventListener('DOMContentLoaded', updateNavbar);
        window.addEventListener('scroll', updateNavbar);
        window.addEventListener('resize', updateNavbar);
        document.addEventListener('click', function (event) {
            const navbarMenu = document.querySelector('#navbarNav');
            const navbarToggler = document.querySelector('.navbar-toggler');
            if (navbarMenu && navbarMenu.classList.contains('show')) {
                const isClickInsideNavbar = navbarMenu.contains(event.target) || (navbarToggler && navbarToggler.contains(event.target));
                if (!isClickInsideNavbar) {
                    let bsCollapse = new bootstrap.Collapse(navbarMenu, { toggle: false });
                    bsCollapse.hide();
                }
            }
        });
    </script>
</body>
</html>
