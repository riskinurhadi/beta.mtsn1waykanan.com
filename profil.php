<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Madrasah - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <link rel="stylesheet" href="style.css">
</head>
<body>
    
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
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <!-- PERUBAHAN: Menambahkan class 'active' pada menu Profil -->
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page">
                            Profil
                        </a>
                        <!-- PERUBAHAN: Isi dropdown disesuaikan -->
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="guru.php">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="index.php#berita">Berita</a>
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
    
    <!-- Header Halaman Profil (Konten Asli Anda) -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title mt-5">Profil Madrasah</h1>
            <p class="page-subtitle">Informasi Lengkap Mengenai MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Identitas (Konten Asli Anda) -->
    <main id="identitas" class="identity-page-section">
        <div class="container mt-2">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card shadow-sm border-0">
                        <div class="card-body p-4 p-md-5">
                            <table class="table table-striped table-hover identity-table">
                                <tbody>
                                    <tr><th scope="row" style="width: 30%;">Nama Madrasah</th><td>Madrasah Tsanawiyah Negeri 1 Way Kanan</td></tr>
                                    <tr><th scope="row">NSM</th><td>121118080001</td></tr>
                                    <tr><th scope="row">NPSN</th><td>10816839</td></tr>
                                    <tr><th scope="row">Akreditasi</th><td>A</td></tr>
                                    <tr><th scope="row">Alamat</th><td>Jl. H. Ibrahim No. 59 Kelurahan Kasui Pasar</td></tr>
                                    <tr><th scope="row">Kecamatan</th><td>Kasui</td></tr>
                                    <tr><th scope="row">Kabupaten</th><td>Way Kanan</td></tr>
                                    <tr><th scope="row">Provinsi</th><td>Lampung</td></tr>
                                    <tr><th scope="row">Kode Pos</th><td>34765</td></tr>
                                    
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Konten Visi Misi (Konten Asli Anda) -->
    <section id="visimisi" class="visi-misi-section">
        <main class="vmt-content-section">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-9">
                        <h2 class="section-title" style="color: #00695C;">Visi, Misi, & Tujuan</h2>
                        <p class="section-subtitle mb-5">Arah dan Cita-cita MTs Negeri 1 Way Kanan.</p>
                    </div>
                </div>
                <div class="row justify-content-center mt-2">
                    <div class="col-lg-10">
                        <div class="vmt-block">
                            <h2>Visi Madrasah</h2>
                            <blockquote class="blockquote">"Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup"</blockquote>
                            <p class="mt-4">Indikator Visi dirumuskan sebagai berikut:</p>
                            <ol>
                                <li>Unggul dalam berbudi dan beribadah</li>
                                <li>Unggul dalam proses pembelajaran akademis dan non-akademis</li>
                                <li>Unggul dalam sistem penilaian</li>
                                <li>Unggul dalam pembelajaran berbasis e-learning</li>
                                <li>Unggul dalam informasi dan komunikasi berbasis IT</li>
                                <li>Unggul dalam pengelolaan lingkungan hidup</li>
                            </ol>
                        </div>
                        <div class="vmt-block">
                            <h2>Misi Madrasah</h2>
                            <blockquote class="blockquote">"Membangun Citra Madrasah yang Inspiratif, Berkarakter, Berprestasi, dan Ramah Lingkungan"</blockquote>
                            <p class="mt-4">Sebagaimana yang tertuang dalam rumusan indikator misi sebagai berikut, yaitu dengan:</p>
                            <ol>
                                <li>Membentuk karakter siswa yang taat beribadah dan bertaqwa kepada Allah SWT</li>
                                <li>Mewujudkan proses pendidikan yang menghasilkan lulusan berakhlaqul karimah</li>
                                <li>Mewujudkan proses pendidikan yang menghasilkan lulusan berprestasi di bidang akademik dan non akademik</li>
                                <li>Mewujudkan proses pembelajaran yang inovatif dan inspiratif dengan berbagaimacam pendekatan</li>
                                <li>Mewujudkan mekanisme penilaian dalam bentuk Akuntabel, Transparan, dan dapat di akses oleh masyarakat luas</li>
                                <li>Terwujudnya lingkungan yang bersih dan nyaman serta menyenangkan</li>
                            </ol>
                        </div>
                        <div class="vmt-block">
                            <h2>Tujuan Madrasah</h2>
                            <p>Berdasarkan Tujuan Pendidikan Nasional, Tujuan Pendidikan Dasar, Visi dan Misi, maka Tujuan Madrasah dapat diuraikan sebagai berikut:</p>
                            <ol>
                                <li>Menyiapkan peserta didik menjadi insan yang beriman dan bertaqwa kepada Tuhan Yang Maha Esa, berakhlak mulia dan berbudi pekerti luhur;</li>
                                <li>Menghasilkan lulusan yang mampu bersaing dalam imtaq dan iptek serta dapat diterima di Sekolah/Madrasah yang di inginkan;</li>
                                <li>Terbentuknya kegiatan Ekstrakurikuler berbasis Islam;</li>
                                <li>Terlaksananya PBM tepat waktu;</li>
                                <li>Terlaksananya pembelajaran teknologi informatika;</li>
                                <li>Terlaksananya penilaian sikap, pengetahuan dan keterampilan pada PBM</li>
                                <li>Terwujudnya informasi berbasis Web (Internet);</li>
                                <li>Terwujudnya kehidupan sekolah yang bersih, indah dan berbudaya islami;</li>
                                <li>Terwujudnya lingkungan yang aman, nyaman dan menyenangkan.</li>
                            </ol>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>
    
    <!-- Konten Sejarah (Konten Asli Anda) -->
    <section id="sejarah">
        <main class="vmt-content-section">
            <div class="container">
                <div class="row justify-content-center text-center">
                    <div class="col-lg-9">
                        <h2 class="section-title" style="color: #00695C;">Sejarah Madrasah</h2>
                        <p class="section-subtitle mb-5">Jejak Langkah dan Perkembangan MTs Negeri 1 Way Kanan.</p>
                    </div>
                </div>
                <div class="row justify-content-center mt-2">
                    <div class="col-lg-9">
                        <div class="history-content-block">
                            <p>Berdasarkan hasil musyawarah tokoh masyarakat, tokoh Agama dan Pemerintah Kecamatan Kasui (Bapak Hi. Mardik, Bapak Mardjan, Bapak Polanudin, Bapak Sukiyan, Drs. Hi. Bakrudin Arta selaku Kepala Madrasah saat itu dan lain-lain).</p>
                            <p>MTs Negeri 1 Way Kanan berdiri sejak tahun 1994. Dibawahnaugan Yayasan Al Fajar dengan nama MTs Al fajar Kasui. Madrasah Tsanawiyah Al Fajar Kasui adalah sebuah Yayasan Pendidikan dimana mempunyai program pendidikan dari tingkat MTs/SLTP dan SMK/SLTA yang bergerak dibidang Agama Islam dan bertujuan untuk menampung anak-anak putus sekolah serta anak-anak yatim/yatimpiatu. Akhirnya dengan kerja keras saling bahu membahu antara tokoh masyarakat, tokoh agama dan jajaran pemerintah kecamatan Kasui setempat ingin menegerikan salah satu tempat pendidikan madrasah tsanawiyah di kecamatan kasui dibawah naungan Kementerian Agama. Pada saat itu ada dua tempat pendidikan yaitu Pondok Pesantren Raudlatul Muta'allimin dan MTs Al Fajar hingga akhirnya berkat perjuangan tokoh masyarakat dan tokoh agama melalui Bapak Akrima Romli dan kawan-kawan tepatnya Pada tahun 1997 MTs Al Fajar menjadi MTs Negeri Kasui dan kini MTsN Kasui berdasarkan KMA tahun 2014 nama MTsN Kasui diubah menjadi MTsN 1 Way Kanan dengan status Akreditasi A. Semanjak dinegerikan MTs Negeri 1 Way Kanan telah mengalami beberapa kali pergantian kepemimpinan yaitu :</p>
                            <h3 class="list-title">Kepemimpinan Madrasah</h3>
                            <ol class="history-list">
                                <li>DIDI ROSYADI, S.Ag (1997 s.d 2004)</li>
                                <li>Drs. SAARI SANUSI (2004 s.d 2006)</li>
                                <li>MUSRIANI, S.Pd.MM (2006 s.d 2014)</li>
                                <li>HAYAMUDIN, S.Pd. (2015 s.d 2019)</li>
                                <li>H. LUKMANHAKIM., S.Pd.,M.M (2019)</li>
                                <li>Dr. ISMAIL FAHMI, S.Pd.,MM (2020 s.d 2022)</li>
                                <li>M. NASIHIN HAQ, S.Pd.I.,MM. (2023 s.d Sekarang).</li>
                            </ol>
                            <address class="address-block">
                                MTs Negeri 1 Way Kanan berlokasi di JL.Hi.Ibrahim No.59 Kelurahan Kasui Pasar Kecamatan Kasui Kabupaten Way Kanan Provinsi Lampung Kode Pos. 34765.
                            </address>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>

    <!-- Footer -->
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
            const isMobile = window.innerWidth < 992;
            
            // Di halaman selain Beranda, navbar selalu 'scrolled'
            navbar.classList.add('scrolled');
        }

        // --- Event Listeners ---
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
