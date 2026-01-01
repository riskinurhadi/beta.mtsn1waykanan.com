
<?php
// Sertakan file koneksi database.
include '../koneksi.php';

// Query untuk mengambil data target hafalan yang sedang aktif.
$sql_hafalan = "SELECT judul, nama_file FROM target_hafalan WHERE is_active = TRUE LIMIT 1";
$result_hafalan = $koneksi->query($sql_hafalan);

$target_hafalan = null;
if ($result_hafalan && $result_hafalan->num_rows > 0) {
    $target_hafalan = $result_hafalan->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Program Tahfidz - MTs Negeri 1 Way Kanan</title>
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
        @media (max-width: 991px) { .align-items-lg-center { align-items: flex-start !important; } }

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
        .content-card h2.main-title { font-weight: 700; color: #198754; text-align: center; margin-bottom: 8px; }

        /* Konten Halaman */
        .content-section {
            padding: 80px 0;
        }
        .content-card {
            background-color: #ffffff;
            border-radius: 15px;
            padding: 30px 40px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.07);
        }
        .content-card .main-title {
            font-weight: 700;
            color: #198754; 
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        
        .main-title {
            font-weight: 700;
            color: #198754; 
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 8px;
        }
        .content-card .main-subtitle {
            text-align: center;
            font-weight: 500;
            color: #6c757d;
            margin-bottom: 40px;
        }
        .info-block {
            margin-bottom: 40px;
        }
        .info-block h3 {
            font-size: 1.5rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }
        .info-block ul {
            list-style: none;
            padding-left: 0;
        }
        .info-block ul li {
            color: #555;
            margin-bottom: 12px;
            display: flex;
            align-items: flex-start;
            line-height: 1.7;
        }
        .info-block ul li i {
            margin-right: 15px;
            margin-top: 7px;
            width: 20px;
            text-align: center;
        }
        .info-block.kekuatan li i { color: #198754; } /* Hijau untuk kekuatan */
        .info-block.kendala li i { color: #dc3545; } /* Merah untuk kendala */
        .info-block.solusi li i { color: #0d6efd; } /* Biru untuk solusi */
        
        /* =============================================
           STYLE UNTUK SEKSI TARGET HAFALAN
           ============================================= */
        .table-subtitle {
            text-align: center;
            font-weight: 500;
            color: #E63946;
            line-height: 1.4;
        }

        .hafalan-table {
            vertical-align: middle;
            font-size: 0.8rem;
            font-weight: 500;
            border-color: #999; /* Menambahkan warna border agar lebih jelas */
        }

        .hafalan-table thead th {
            background-color: #00695C;
            color: white;
            font-weight: 600;
            padding: 8px;
            border-color: #999;
        }

        .hafalan-table .surah-names th {
            background-color: #e0f2f1;
            color: #004d40;
            font-family: 'Times New Roman', Times, serif; /* Font arabik */
            font-size: 1.1rem;
            /* PERUBAHAN: Membuat teks nama surah menjadi vertikal */
            writing-mode: vertical-rl;
            text-orientation: mixed;
            padding: 10px 2px;
            height: 150px; 
        }

        .hafalan-table tbody td {
            padding: 6px;
            border-color: #999;
        }

        /* --- PERBAIKAN DETAIL WARNA SESUAI GAMBAR --- */

        /* Default Colors (jika selector :nth-child tidak didukung) */
        .hafalan-table .bg-ganjil { background-color: #c8e6c9; }
        .hafalan-table .bg-genap { background-color: #a5d6a7; }
        .hafalan-table .bg-smt-ganjil { background-color: #ffcdd2; }
        .hafalan-table .bg-smt-genap { background-color: #bbdefb; }

        /* Kelas VII (Baris Pertama) */
        .hafalan-table tbody tr:nth-child(1) .bg-ganjil {
            background-color: #c8e6c9; /* Hijau Muda */
        }
        .hafalan-table tbody tr:nth-child(1) .bg-genap {
            background-color: #a5d6a7; /* Hijau Lebih Terang */
        }

        /* Kelas VIII (Baris Kedua) */
        .hafalan-table tbody tr:nth-child(2) .bg-smt-ganjil {
            background-color: #fff9c4; /* Kuning Muda */
        }
        .hafalan-table tbody tr:nth-child(2) .bg-smt-genap {
            background-color: #c8e6c9; /* Hijau Muda */
        }

        /* Kelas IX (Baris Ketiga) */
        .hafalan-table tbody tr:nth-child(3) .bg-smt-ganjil {
            background-color: #ffcdd2; /* Merah Muda */
        }
        .hafalan-table tbody tr:nth-child(3) .bg-smt-genap {
            background-color: #bbdefb; /* Biru Muda */
        }
        
        /* Style untuk Tampilan Gambar Target Hafalan */
        .hafalan-target-section {
            padding-bottom: 80px;
        }
        .hafalan-image-wrapper {
            text-align: center;
            padding: 20px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.1);
        }
        .hafalan-image-wrapper img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
        }


    </style>
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.html">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="https://mtsn1waykanan.com/img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
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
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/guru.php">Profil Guru</a></li>
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
                    
                    <!--<li class="nav-item">-->
                    <!--    <a class="nav-link" href="https://mtsn1waykanan.com/unggulan">Program Unggulan</a>-->
                    <!--</li>-->
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Program
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/unggulan">Program Unggulan</a></li>
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tahfiz">Program Tahfiz</a></li>
                            <!--<li><a class="dropdown-item" href="https://asndigital.bkn.go.id/">ASN</a></li>-->
                            <!--<li><a class="dropdown-item" href="">PTSP</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">PTSP</a></li>-->
                            <!--<li><a class="dropdown-item" href="sejarah.html">Legalisir Online</a></li>-->
                        </ul>
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
            <h1 class="page-title">Program Tahfidz</h1>
            <p class="page-subtitle">Mencetak Generasi Qur'ani</p>
        </div>
    </header>

    <!-- Konten Utama Halaman -->
    <main class="content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="content-card">
                        <h2 class="main-title">PROGRAM TAHFIDZ</h2>
                        <p class="main-subtitle">MTSN 1 WAY KANAN</p>

                        <!-- Blok Kekuatan -->
                        <div class="info-block kekuatan">
                            <h3>Kekuatan</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Banyak siswa/i yang sudah lancar membaca alqur'an.</li>
                                <li><i class="fas fa-check-circle"></i> Sebagian besar siswa/i kelas 7 yang sudah mempunyai hafalan sesuai target.</li>
                                <li><i class="fas fa-check-circle"></i> Kekompakan para guru terutama wali kelas.</li>
                                <li><i class="fas fa-check-circle"></i> Sound system yang memadai.</li>
                            </ul>
                        </div>

                        <!-- Blok Kendala -->
                        <div class="info-block kendala">
                            <h3>Kendala-kendala</h3>
                            <ul>
                                <li><i class="fas fa-times-circle"></i> Masih ada siswa/i yang belum lancar membaca alqur'an.</li>
                                <li><i class="fas fa-times-circle"></i> Sebagian besar siswa/i yang sudah hafal banyak surat namun belum lancar dan belum adanya sarana bagi anak (juz amma) sehingga kurang maksimal dalam menghafal.</li>
                                <li><i class="fas fa-times-circle"></i> Masih adanya siswa/i yang bermain di waktu pembacaan surat surat pendek.</li>
                                <li><i class="fas fa-times-circle"></i> Belum adanya formulasi yang tepat untuk mengevaluasi target hafalan siswa/i.</li>
                                <li><i class="fas fa-times-circle"></i> Banyak siswa/i yang sudah hafal surat surat pendek namun belum disetorkan.</li>
                            </ul>
                        </div>

                        <!-- Blok Solusi -->
                        <div class="info-block solusi">
                            <h3>Solusi</h3>
                            <ul>
                                <li><i class="fas fa-lightbulb"></i> Wali Kelas perlu mengadakan bimbingan anak yang belum mampu membaca al quran.</li>
                                <li><i class="fas fa-lightbulb"></i> Madrasah/Wali kelas menyediakan juz amma bagi siswa/i.</li>
                                <li><i class="fas fa-lightbulb"></i> Wali kelas, tenaga pendidik dan kependidikan terlibat dalam kegiatan pagi.</li>
                            </ul>
                        </div>
                        
                        <div class="info-block kekuatan">
                            <h3>Yang Di Persiapkan Madrasah</h3>
                            <ul>
                                <li><i class="fas fa-check-circle"></i> Mutabaah pegangan anak.</li>
                                <li><i class="fas fa-check-circle"></i> Mutabaah pegangan guru.</li>
                                <li><i class="fas fa-check-circle"></i> Banner mutabaah dikelas.</li>
                                <!--<li><i class="fas fa-check-circle"></i> Sound system yang memadai.</li>-->
                            </ul>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </main>
    
    <!-- Bagian Target Hafalan (Dinamis) -->
    <section class="hafalan-target-section">
        <div class="container">
            <?php if ($target_hafalan): ?>
                <div class="row justify-content-center">
                    <div class="col-lg-11">
                        <h2 class="main-title mb-4 text-center"><?php echo htmlspecialchars($target_hafalan['judul']); ?></h2>
                        <div class="hafalan-image-wrapper">
                            <img src="../admin/uploads/hafalan/<?php echo htmlspecialchars($target_hafalan['nama_file']); ?>" class="img-fluid" alt="<?php echo htmlspecialchars($target_hafalan['judul']); ?>">
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="text-center">
                    <p>Tabel target hafalan belum tersedia.</p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php $koneksi->close(); ?>

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
