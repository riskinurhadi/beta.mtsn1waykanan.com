<?php
// Asumsi file config.php ada di dalam folder 'admin'
require_once 'admin/config.php';
require_once 'koneksi.php';

$errors = [];
$success_message = '';

// Variabel untuk menampung nilai form agar tidak hilang jika terjadi error
$nama = '';
$email = '';
$phone = '';
$subjek = '';
$pesan = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data dari form
    $nama = trim($_POST['nama_pengirim']);
    $email = trim($_POST['email_pengirim']);
    $phone = trim($_POST['telepon_pengirim']);
    $subjek = trim($_POST['subjek']);
    $pesan = trim($_POST['isi_pesan']);

    // 1. Validasi Sederhana
    if (empty($nama)) { $errors[] = "Nama Anda tidak boleh kosong."; }
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Alamat email tidak valid."; }
    if (empty($phone)) { $errors[] = "Nomor telepon tidak boleh kosong."; }
    if (empty($subjek)) { $errors[] = "Anda harus memilih subjek pesan."; }
    if (empty($pesan)) { $errors[] = "Isi pesan tidak boleh kosong."; }

    // 2. Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $sql = "INSERT INTO pesan_kontak (nama_pengirim, email_pengirim, telepon_pengirim, subjek, isi_pesan) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssss", $nama, $email, $phone, $subjek, $pesan);
            
            if ($stmt->execute()) {
                // Jika berhasil, siapkan pesan sukses dan kosongkan variabel form
                $success_message = "Pesan Anda telah berhasil terkirim. Terima kasih!";
                $nama = $email = $phone = $subjek = $pesan = '';
            } else {
                $errors[] = "Terjadi kesalahan. Gagal mengirim pesan, silakan coba lagi nanti.";
            }
            $stmt->close();
        }
    }
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
   <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!--<link rel="preconnect" href="https://fonts.googleapis.com">-->
    <!--<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>-->
    <!--<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">-->

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        /* =============================================
   STYLE UNTUK KARTU PRESTASI YANG BISA DIKLIK
   ============================================= */

.prestasi-card-link {
    text-decoration: none;
    color: inherit; 
    display: block;
    height: 100%;
}

.prestasi-card-link:hover {
    color: inherit; 
}

/* =============================================
   PERBAIKAN: Mengembalikan gaya teks di dalam kartu
   ============================================= */

/* Mengembalikan warna asli untuk judul dan paragraf */
.prestasi-card-link .prestasi-title {
    color: #333;
    transition: color 0.3s ease;
}

.prestasi-card-link .prestasi-description,
.prestasi-card-link .prestasi-info {
    color: #555; /* Sesuaikan dengan warna teks asli Anda */
}

/* Memberi efek hover hanya pada judul untuk menandakan bisa diklik */
.prestasi-card-link:hover .prestasi-title {
    color: #198754; /* Warna hijau saat hover */
}


/* =============================================
   STYLE UNTUK HALAMAN DETAIL PRESTASI
   ============================================= */

.detail-page-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

.detail-card {
    border: none;
    border-radius: 15px;
}

.detail-img {
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
    max-height: 500px;
    object-fit: cover;
}

.detail-title {
    font-weight: 700;
    color: #333;
}

.detail-content {
    font-size: 1.05rem;
    line-height: 1.8;
    color: #555;
}
    </style>
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
                    
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tata-tertib.php">Tata Tertib Madrasah</a></li>
                            <!--<li><a class="dropdown-item" href="https://emisgtk.kemenag.go.id/">GTK</a></li>-->
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
    

    <header class="hero-section">
        <div class="container h-100">
            <div class="row h-100 align-items-center">
                <div class="col-md-7">
                    <h1 class="hero-title">Selamat Datang di Website Resmi MTs Negeri 1 Way Kanan</h1>
                    <p class="hero-subtitle">Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup.</p>
                    <a href="#" class="btn btn-success btn-lg hero-button">Penerimaan Siswa Baru</a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- ALERT PENGUMUMAN PROGRES DEVELOPMENT -->
    <!-- ============================================= -->
    <div class="alert alert-dev-progress alert-dismissible fade show" role="alert">
        <div class="container text-center">
            <strong>Update Progres Development (14 Juli 2025):</strong> Pemutakhiran semua fitur dan halaman sementara yang sudah ada, menghapus efek transparasi Navbar saat di buka di mode Handphone, dan beberapa pembenahan Bug lain nya.
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
    
        <section class="features-section">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Gerbang Informasi Digital Madrasah</h2>
                    <p class="section-subtitle">Temukan semua yang Anda butuhkan tentang MTs Negeri 1 Way Kanan melalui platform digital kami.</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-bank2" viewBox="0 0 16 16">
                                <path d="M8.277.084a.5.5 0 0 0-.554 0l-7.5 5A.5.5 0 0 0 .5 6h1.875v7H1.5a.5.5 0 0 0 0 1h13a.5.5 0 0 0 0-1h-.875V6H15.5a.5.5 0 0 0 .277-.916zM12.375 6v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zm-2.5 0v7h-1.25V6zM8 4a1 1 0 1 1 0-2 1 1 0 0 1 0 2M.5 15a.5.5 0 0 0 0 1h15a.5.5 0 0 0 0-1z"/>
                            </svg>
                        </div>
                        <h4 class="card-title">Profil Madrasah</h4>
                        <p class="card-text">Kenali lebih dalam sejarah, visi, misi, serta fasilitas yang kami sediakan untuk menunjang pendidikan.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="icon-container">
                           <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-newspaper" viewBox="0 0 16 16">
                                <path d="M0 2.5A1.5 1.5 0 0 1 1.5 1h11A1.5 1.5 0 0 1 14 2.5v10.528c0 .3-.05.654-.238.972h.738a.5.5 0 0 0 .5-.5v-9a.5.5 0 0 1 1 0v9a1.5 1.5 0 0 1-1.5 1.5H1.497A1.497 1.497 0 0 1 0 13.5zM12 14c.37 0 .654-.211.853-.441.092-.106.147-.279.147-.531V2.5a.5.5 0 0 0-.5-.5h-11a.5.5 0 0 0-.5.5v11c0 .278.223.5.497.5z"/>
                                <path d="M2 3h10v2H2zm0 3h4v3H2zm0 4h4v1H2zm0 2h4v1H2zm5-6h2v1H7zm3 0h2v1h-2zM7 8h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2zm-3 2h2v1H7zm3 0h2v1h-2z"/>
                            </svg>
                        </div>
                        <h4 class="card-title">Pusat Informasi</h4>
                        <p class="card-text">Dapatkan berita terbaru, pengumuman penting, dan agenda kegiatan madrasah secara cepat dan akurat.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="icon-container">
                            <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-trophy-fill" viewBox="0 0 16 16">
                                <path d="M2.5.5A.5.5 0 0 1 3 .5h10a.5.5 0 0 1 .5.5c0 .538-.012 1.05-.034 1.536a3 3 0 1 1-1.133 5.89c-.79 1.865-1.878 2.777-2.833 3.011v2.173l1.425.356a.5.5 0 0 1 .175.787l-1.5 1.5a.5.5 0 0 1-.707 0l-1.5-1.5a.5.5 0 0 1 .175-.787l1.425-.356v-2.173c-.955-.234-2.043-1.146-2.833-3.012a3 3 0 1 1-1.132-5.89A33 33 0 0 1 2.5 1.036a.5.5 0 0 1 0-.536z"/>
                            </svg>
                        </div>
                        <h4 class="card-title">Prestasi & Galeri</h4>
                        <p class="card-text">Lihat berbagai pencapaian siswa dan guru, serta dokumentasi kegiatan dalam galeri foto dan video kami.</p>
                    </div>
                </div>

                <div class="col-lg-3 col-md-6">
                    <div class="feature-card">
                        <div class="icon-container">
                           <svg xmlns="http://www.w3.org/2000/svg" width="36" height="36" fill="currentColor" class="bi bi-globe" viewBox="0 0 16 16">
                                <path d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m7.5-6.923c-.67.204-1.335.82-1.887 1.855A8 8 0 0 0 5.145 4H7.5zM4.09 4a9.3 9.3 0 0 1 .64-1.539 7 7 0 0 1 .597-.933A7.03 7.03 0 0 0 2.255 4zm-.582 3.5c.03-.877.138-1.718.312-2.5H1.674a7 7 0 0 0-.656 2.5zM4.847 5a12.5 12.5 0 0 0-.202 2.5H7.5V5zM8.5 5v2.5h2.853a12.5 12.5 0 0 0-.202-2.5zm4.507 1.5a9.3 9.3 0 0 1-.64 1.539 7 7 0 0 1-.597.933A7.03 7.03 0 0 0 13.745 4h-1.89zM10.855 4a8 8 0 0 0-1.21-2.066c.652-.458 1.356-.826 2.087-1.075V4z"/>
                            </svg>
                        </div>
                        <h4 class="card-title">Layanan Digital</h4>
                        <p class="card-text">Akses mudah untuk pendaftaran siswa baru (PPDB), informasi kontak, dan layanan digital lainnya.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <section class="sambutan-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 text-center">
                    <div class="sambutan-img-wrapper">
                        <img src="img/kamad1.jpg" alt="Kepala MTs Negeri 1 Way Kanan" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="sambutan-text">
                        <h2 class="sambutan-title">Sambutan Kepala Madrasah</h2>
                        <p class="sambutan-greeting">Assalamu'alaikum Warahmatullahi Wabarakatuh.</p>
                        <p>Puji syukur kehadirat Allah SWT atas rahmat dan karunia-Nya, kami dapat menyajikan website resmi MTs Negeri 1 Way Kanan. Website ini kami hadirkan sebagai jembatan informasi antara madrasah dengan siswa, orang tua, dan masyarakat luas.</p>
                        <p>Kami berharap platform digital ini dapat menjadi sarana untuk berbagi informasi, prestasi, dan berbagai kegiatan positif di lingkungan madrasah. Mari bersama-sama kita majukan pendidikan untuk mencetak generasi yang cerdas dan berakhlak mulia.</p>
                        <div class="sambutan-signature">
                            <p class="nama-kepsek">M. NASIHIN HAQ, S.Pd.I, M.M.</p>
                            <p class="jabatan">Kepala MTs Negeri 1 Way Kanan</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="visi-misi-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9 text-center">
                    
                    <h2 class="section-title">Visi & Misi</h2>
                    <p class="section-subtitle mb-5">Landasan dan arah tujuan pendidikan di MTs Negeri 1 Way Kanan.</p>

                    <div class="visi-container">
                        <h3 class="visi-title">Visi</h3>
                        <blockquote class="blockquote">
                            <p>"Bertaqwa, Berakhlakul Karimah, Berprestasi Dan Berwawasan Lingkungan Hidup."</p>
                        </blockquote>
                    </div>

                    <div class="misi-container mt-5">
                        <h3 class="misi-title">Misi</h3>
                        <ul class="misi-list">
                            <li>
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                                </svg>
                                <span>Membangun Citra Madrasah yang Inspiratif, Berkarakter, Berprestasi, dan Ramah Lingkungan.</span>
                            </li>
                            <!--<li>-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">-->
                            <!--        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>-->
                            <!--    </svg>-->
                            <!--    <span>2.Unggul dalam proses pembelajaran akademis dan non-akademis.</span>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">-->
                            <!--        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>-->
                            <!--    </svg>-->
                            <!--    <span>Membina dan mengembangkan akhlakul karimah serta budi pekerti luhur melalui kegiatan pembiasaan.</span>-->
                            <!--</li>-->
                            <!--<li>-->
                            <!--    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16">-->
                            <!--        <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>-->
                            <!--    </svg>-->
                            <!--    <span>Menciptakan lingkungan madrasah yang bersih, sehat, dan nyaman sebagai wujud peduli lingkungan.</span>-->
                            <!--</li>-->
                        </ul>
                    </div>
                </div>
            </div>
            <div class="text-center mt-5">
                <a href="https://mtsn1waykanan.com/profil.php#visimisi" class="btn btn-outline-success btn-lg">Baca Selengkapnya</a>
            </div>
        </div>
    </section>
    
    </section>

    </section>

    <section class="sambutan-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-5 text-center">
                    <div class="sambutan-img-wrapper">
                        <img src="img/kepalatu.jpg" alt="Kepala MTs Negeri 1 Way Kanan" class="img-fluid">
                    </div>
                </div>
                <div class="col-lg-7">
                    <div class="sambutan-text">
                        <h2 class="sambutan-title">Sambutan Kepala Tata Usaha</h2>
                        <p class="sambutan-greeting">Assalamu'alaikum Warahmatullahi Wabarakatuh.</p>
                        <p>Alhamdulillah, segala puji bagi Allah SWT yang telah memberikan kita kesempatan dan kemudahan dalam menjalankan amanah di lingkungan MTs Negeri 1 Way Kanan. Kami menyambut baik kehadiran website resmi madrasah ini sebagai bentuk inovasi dalam pelayanan informasi dan transparansi administrasi.</p>
                        <p>Sebagai Kepala Tata Usaha, kami berkomitmen untuk terus meningkatkan kualitas layanan administrasi, baik untuk peserta didik, orang tua, maupun masyarakat. Website ini diharapkan menjadi salah satu sarana efektif untuk mengakses informasi penting terkait pelayanan akademik dan non-akademik, surat menyurat, serta kegiatan-kegiatan madrasah.</p>
                        <div class="sambutan-signature">
                            <p class="nama-kepsek">AFRIANSYAH, S.Pd.I.,M.M</p>
                            <p class="jabatan">Kepala Tata Usaha</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    </section>

    <!-- Bagian Prestasi Siswa -->
    <section id="prestasi" class="prestasi-section">
        <div class="container">
            <!-- Judul Section -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Prestasi Siswa</h2>
                    <p class="section-subtitle">Berbagai pencapaian gemilang oleh siswa-siswi MTs Negeri 1 Way Kanan di berbagai bidang.</p>
                </div>
            </div>

<div class="row g-4">
    <?php
    // PERBAIKAN FINAL: Atur header HTTP untuk memaksa UTF-8
    // Baris ini harus ada di paling atas file PHP Anda, sebelum ada output HTML apapun.
    header('Content-Type: text/html; charset=utf-8');

    // 1. Sertakan file koneksi
    include 'koneksi.php';

    // 2. Atur character set koneksi ke utf8mb4 (SANGAT PENTING)
    $koneksi->set_charset("utf8mb4");

    // 3. Buat query untuk mengambil 3 prestasi terbaru
    $query = "SELECT * FROM prestasi ORDER BY tahun DESC, id DESC LIMIT 3";
    
    // 4. Eksekusi query
    $result = $koneksi->query($query);

    // 5. Periksa apakah ada data yang ditemukan
    if ($result && $result->num_rows > 0) {
        // 6. Lakukan perulangan untuk setiap baris data
        while ($data = $result->fetch_assoc()) {
            // Tentukan path gambar, gunakan gambar placeholder jika kosong
            $foto = (!empty($data['foto_url'])) ? 'admin/uploads/prestasi/' . htmlspecialchars($data['foto_url']) : 'https://placehold.co/600x400/E0F2F1/198754?text=Prestasi';
            
            // Menggunakan mb_strlen dan mb_substr untuk memotong string multi-byte dengan aman
            $deskripsi_lengkap = $data['deskripsi'];
            $panjang_maksimal = 120; // Atur batas karakter di sini
            
            if (mb_strlen($deskripsi_lengkap, 'UTF-8') > $panjang_maksimal) {
                $deskripsi_tampil = mb_substr($deskripsi_lengkap, 0, $panjang_maksimal, 'UTF-8') . '...';
            } else {
                $deskripsi_tampil = $deskripsi_lengkap;
            }
    ?>
            <!-- PERUBAHAN: Kartu sekarang dibungkus dengan tag <a> -->
                        <div class="col-lg-4 col-md-6">
                            <a href="detail_prestasi.php?id=<?php echo $data['id']; ?>" class="prestasi-card-link">
                                <div class="prestasi-card">
                                    <div class="prestasi-img-container">
                                        <img src="<?php echo $foto; ?>" alt="Foto Prestasi: <?php echo htmlspecialchars($data['nama_prestasi']); ?>">
                                    </div>
                                    <div class="prestasi-content">
                                        <span class="prestasi-badge"><?php echo htmlspecialchars($data['tingkat']); ?></span>
                                        <h4 class="prestasi-title"><?php echo htmlspecialchars($data['nama_prestasi']); ?></h4>
                                        <p class="prestasi-description"><?php echo htmlspecialchars($deskripsi_tampil); ?></p>
                                        <p class="prestasi-info"><?php echo htmlspecialchars($data['nama_siswa']); ?> - <?php echo htmlspecialchars($data['tahun']); ?></p>
                                    </div>
                                </div>
                            </a>
                        </div>
    <?php
        } // Akhir dari perulangan while
    } else {
        // Jika tidak ada data, tampilkan pesan
        echo '<div class="col-12 text-center"><p>Belum ada data prestasi untuk ditampilkan.</p></div>';
    }

    // 7. Tutup koneksi database
    $koneksi->close();
    ?>
</div>



            <div class="text-center mt-5">
                <a href="semua-prestasi.php" class="btn btn-outline-success btn-lg">Lihat Semua Prestasi</a>
            </div>
        </div>
    </section>
    
    
    <!-- ============================================= -->
    <!-- BAGIAN TESTIMONI ALUMNI -->
    <!-- ============================================= -->
    <section class="alumni-section">
        <div class="container">
            <!-- Judul Section -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Kata Mereka Para Alumni</h2>
                    <p class="section-subtitle">Kisah dan kesan dari para alumni yang telah sukses di berbagai bidang sebagai inspirasi bagi kita semua.</p>
                </div>
            </div>

            <div class="row g-4 justify-content-center">
                <?php
                // Asumsi koneksi ($koneksi) sudah ada dari atas file.
                include 'koneksi.php';
                // PERBAIKAN: Mengembalikan query ke kondisi normal.
                // Query ini akan mengambil testimoni yang kolom 'is_tampil' nya bernilai 1.
                $query_testimoni = "SELECT * FROM testimoni_alumni WHERE is_tampil = 1 ORDER BY id DESC";
                
                // Eksekusi query
                $result_testimoni = $koneksi->query($query_testimoni);

                // Cek apakah query berhasil dieksekusi dan ada datanya.
                if ($result_testimoni && $result_testimoni->num_rows > 0) {
                    while ($testimoni = $result_testimoni->fetch_assoc()) {
                        // Siapkan path gambar
                        $foto_alumni = (!empty($testimoni['foto_alumni'])) ? 'admin/uploads/testimoni/' . htmlspecialchars($testimoni['foto_alumni']) : 'https://placehold.co/400x400/E0F2F1/198754?text=Foto';
                ?>
                        <!-- Kartu Alumni Dinamis -->
                        <div class="col-lg-10">
                            <div class="alumni-card">
                                <div class="row g-0">
                                    <div class="col-md-4">
                                        <img src="<?php echo $foto_alumni; ?>" class="alumni-img" alt="Foto <?php echo htmlspecialchars($testimoni['nama_alumni']); ?>">
                                    </div>
                                    <div class="col-md-8">
                                        <div class="alumni-content">
                                            <h4 class="alumni-name"><?php echo htmlspecialchars($testimoni['nama_alumni']); ?></h4>
                                            <p class="alumni-year"><?php echo htmlspecialchars($testimoni['info_singkat']); ?></p>
                                            <p class="alumni-quote">"<?php echo htmlspecialchars($testimoni['testimoni']); ?>"</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                <?php
                    } // Akhir perulangan while
                } else {
                    // Pesan ini akan muncul jika tidak ada data ATAU jika query gagal.
                    echo '<div class="col-12 text-center"><p>Belum ada testimoni untuk ditampilkan.</p></div>';
                    // Untuk debugging, Anda bisa coba tambahkan ini untuk melihat error jika ada:
                    // echo '<div class="col-12 text-center text-danger"><p>Error: ' . $koneksi->error . '</p></div>';
                }
                ?>
            </div>
        </div>
    </section>
    
    <!-- BAGIAN PARALAKS (CALL TO ACTION) -->
    <!-- ============================================= -->
    <section class="cta-parallax-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-9 mx-auto text-center">
                    <h2 class="cta-title">Pendaftaran Siswa Baru Online</h2>
                    <p class="cta-subtitle">Jadilah bagian dari generasi unggul berikutnya. Bergabunglah dengan keluarga besar MTs Negeri 1 Way Kanan. Daftar sekarang juga, di sini.</p>
                    <!-- Ganti tanda '#' dengan link halaman pendaftaran Anda -->
                    <a href="https://mtsn1waykanan.com/ppdb" class="btn btn-light btn-lg cta-button">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </section>
    
    <section id="galeri" class="gallery-section">
        <div class="container">
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Dokumentasi Kegiatan</h2>
                    <p class="section-subtitle">Momen-momen berharga yang terekam dari berbagai aktivitas dan kegiatan di lingkungan madrasah.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // 1. Sertakan file koneksi
                // Pastikan koneksi belum ditutup di bagian sebelumnya jika dalam satu file
                include 'koneksi.php';

                // 2. Buat query untuk mengambil 6 foto terbaru
                $query_galeri = "SELECT * FROM galeri ORDER BY tanggal_upload DESC LIMIT 6";
                
                // 3. Eksekusi query
                $result_galeri = $koneksi->query($query_galeri);

                // 4. Periksa apakah ada data
                if ($result_galeri && $result_galeri->num_rows > 0) {
                    // 5. Lakukan perulangan untuk setiap foto
                    while ($item = $result_galeri->fetch_assoc()) {
                        // Tentukan path gambar, gunakan placeholder jika kosong
                        $foto_galeri = (!empty($item['foto_url'])) ? 'admin/uploads/galeri/' . htmlspecialchars($item['foto_url']) : 'https://placehold.co/600x600/E0F2F1/198754?text=Foto';
                ?>
                        <div class="col-lg-4 col-md-6">
                            <div class="gallery-item">
                                <img src="<?php echo $foto_galeri; ?>" class="img-fluid" alt="<?php echo htmlspecialchars($item['deskripsi']); ?>">
                                <div class="gallery-overlay">
                                    <p class="gallery-category"><?php echo htmlspecialchars($item['kategori']); ?></p>
                                    <h4 class="gallery-description"><?php echo htmlspecialchars($item['deskripsi']); ?></h4>
                                </div>
                            </div>
                        </div>
                <?php
                    } // Akhir perulangan while
                } else {
                    echo '<div class="col-12 text-center"><p>Belum ada foto di galeri.</p></div>';
                }
                
                // Tutup koneksi jika ini adalah query terakhir di halaman
                // $koneksi->close(); 
                ?>
            </div>

            <div class="text-center mt-5">
                <a href="galeri.php" class="btn btn-outline-success btn-lg">Lihat Galeri Lengkap</a>
            </div>
        </div>
    </section>
    
    
    <!-- BAGIAN BERITA TERBARU -->
    <!-- ============================================= -->
    <section id="berita" class="news-section">
        <div class="container">
            <!-- Judul Section -->
            <div class="row justify-content-center text-center mb-5">
                <div class="col-lg-8">
                    <h2 class="section-title">Berita & Informasi Terbaru</h2>
                    <p class="section-subtitle">Ikuti perkembangan dan informasi terkini dari kegiatan, prestasi, dan pengumuman penting di MTs Negeri 1 Way Kanan.</p>
                </div>
            </div>

            <div class="row g-4">
                <?php
                // Sertakan file koneksi jika belum ada.
                // Jika sudah ada koneksi terbuka, baris ini bisa di-comment.
                // include_once 'koneksi.php';

                // Query untuk mengambil 3 berita terbaru.
                $query_berita = "SELECT * FROM berita ORDER BY tanggal_publikasi DESC LIMIT 3";
                $result_berita = $koneksi->query($query_berita);

                if ($result_berita && $result_berita->num_rows > 0) {
                    while ($berita = $result_berita->fetch_assoc()) {
                        // Format tanggal menjadi lebih mudah dibaca (e.g., 03 Juli 2025)
                        $tanggal = date("d F Y", strtotime($berita['tanggal_publikasi']));

                        // Potong isi berita untuk cuplikan singkat (sekitar 100 karakter)
                        $cuplikan = substr(strip_tags($berita['isi']), 0, 100) . '...';
                        
                        // Path gambar berita
                        $gambar = (!empty($berita['gambar_utama'])) ? 'admin/uploads/berita/' . htmlspecialchars($berita['gambar_utama']) : 'https://placehold.co/600x400/E0F2F1/198754?text=Berita';
                ?>
                        <!-- Kartu Berita Dinamis -->
                        <div class="col-lg-4 col-md-6">
                            <div class="news-card">
                                <div class="news-img-container">
                                    <img src="<?php echo $gambar; ?>" alt="Gambar Berita: <?php echo htmlspecialchars($berita['judul']); ?>">
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
                    echo '<div class="col-12 text-center"><p>Belum ada berita untuk ditampilkan.</p></div>';
                }
                // Anda bisa menutup koneksi di akhir file jika semua query sudah selesai.
                // $koneksi->close();
                ?>
            </div>
             <div class="text-center mt-5">
                <a href="berita.php" class="btn btn-outline-success btn-lg">Lihat Semua Berita</a>
            </div>
        </div>
    </section>
    
    
    <section id="kontak" class="contact-section">
    <div class="container">
        <div class="section-title">
            <h2>Hubungi Kami</h2>
            <p>Punya pertanyaan atau butuh informasi lebih lanjut? Jangan ragu untuk menghubungi kami melalui form di bawah atau datang langsung ke madrasah.</p>
        </div>

        <div class="contact-info-grid">
            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="fas fa-map-marker-alt"></i>
                </div>
                <h4>Alamat Kami</h4>
                <p>Jalan Hi. Ibrahim No.59 Kelurahan Kasui Pasar Kec. Kasui Kab. Way Kanan Prov.Lampung, Kode Pos.34565.</p>
            </div>

            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="fas fa-phone-alt"></i>
                </div>
                <h4>Nomor Kontak</h4>
                <p>Telepon: (0723) 461-XXX<br>Email: info.mtsn1waykanan@gmail.com</p>
            </div>

            <div class="info-card">
                <div class="icon-wrapper">
                    <i class="fas fa-clock"></i>
                </div>
                <h4>Jam Operasional</h4>
                <p>Senin - Jumat: 07:30 - 15:00<br>Sabtu & Minggu: Tutup</p>
            </div>
        </div>

        <!--<div style="max-width: 1140px; margin: 40px auto; padding: 20px;">-->
        
        <?php if (!empty($errors)): ?>
            <div class="alert-box alert-danger">
                <strong>Gagal!</strong>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <!-- PERUBAHAN: Form sekarang memiliki method dan action -->
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="contact-form">
            <div class="form-grid">
                <!-- PERUBAHAN: Atribut 'name' disesuaikan dengan kolom database -->
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="nama_pengirim" placeholder="Nama Anda*" required value="<?php echo htmlspecialchars($nama); ?>">
                </div>
                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input type="email" name="email_pengirim" placeholder="Alamat Email*" required value="<?php echo htmlspecialchars($email); ?>">
                </div>
                <div class="input-group">
                    <i class="fas fa-mobile-alt"></i>
                    <input type="tel" name="telepon_pengirim" placeholder="Nomor Telepon*" required value="<?php echo htmlspecialchars($phone); ?>">
                </div>
                <div class="input-group">
                    <i class="fas fa-list-ul"></i>
                    <select name="subjek" required>
                        <option value="" disabled <?php echo empty($subjek) ? 'selected' : ''; ?>>Pilih Subjek Pesan*</option>
                        <option value="Informasi PPDB" <?php echo ($subjek == 'Informasi PPDB') ? 'selected' : ''; ?>>Informasi PPDB</option>
                        <option value="Terkait Akademik" <?php echo ($subjek == 'Terkait Akademik') ? 'selected' : ''; ?>>Terkait Akademik</option>
                        <option value="Pertanyaan Umum" <?php echo ($subjek == 'Pertanyaan Umum') ? 'selected' : ''; ?>>Pertanyaan Umum</option>
                        <option value="Lainnya" <?php echo ($subjek == 'Lainnya') ? 'selected' : ''; ?>>Lainnya</option>
                    </select>
                </div>
            </div>
            <div class="input-group full-width mt-4">
                <i class="fas fa-comment-dots"></i>
                <textarea name="isi_pesan" rows="6" placeholder="Tuliskan pesan Anda di sini...*" required><?php echo htmlspecialchars($pesan); ?></textarea>
            </div>
            <div class="submit-button-wrapper">
                <button type="submit" class="submit-btn">Kirim Pesan</button>
            </div>
        </form>
    <!--</div>-->

    </div>
</section>
    
    <section>
        <footer class="site-footer">
        <div class="footer-container">
            <div class="footer-column footer-about">
                <!--<img src="img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" class="footer-logo"  style="height: 40px;">-->
                <h4>MTs Negeri 1 Way Kanan</h4>
                <p>
                    <i class="fas fa-map-marker-alt"></i>
                    Jalan Hi. Ibrahim No.59 Kelurahan Kasui Pasar <br>Kec. Kasui Kab. Way Kanan Prov.Lampung KP. 34565.
                </p>
                <p>
                    <i class="fas fa-envelope"></i>
                    info.mtsn1waykanan@gmail.com
                </p>
                <p>
                    <i class="fas fa-phone-alt"></i>
                    (0723) 461-XXX
                </p>
            </div>

            <div class="footer-column footer-links">
                <h4>Menu Utama</h4>
                <ul>
                    <li><a href="/beranda">Beranda</a></li>
                    <li><a href="/profil">Profil</a></li>
                    <li><a href="/berita">Berita</a></li>
                    <li><a href="/galeri">Galeri</a></li>
                    <li><a href="/akademik">Akademik</a></li>
                    <li><a href="/kontak">Kontak</a></li>
                </ul>
            </div>

            <div class="footer-column footer-links">
                <h4>Penting</h4>
                <ul>
                    <li><a href="#">PPDB Online</a></li>
                    <li><a href="#">Sistem Informasi Akademik</a></li>
                    <li><a href="#">E-Learning Madrasah</a></li>
                    <li><a href="#">Kemenag Way Kanan</a></li>
                </ul>
            </div>

            <div class="footer-column footer-social">
                <h4>Temukan Kami</h4>
                <div class="social-icons">
                    <a href="https://web.facebook.com/mtsn1waykanan/" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.instagram.com/mtsn1waykanan/" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    <a href="https://www.youtube.com/@mtsnegeri1waykanan771" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="https://x.com/mtsn1waykanan" aria-label="Twitter"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.tiktok.com/@mtsn1waykanan" aria-label="Tiktok"><i class="fab fa-tiktok"></i></a>
                    <!--<a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>-->
                </div>
                <a href="#" class="map-button">Lihat Lokasi di Peta</a>
            </div>
        </div>

        <div class="footer-bottom">
            <p>© 2025 MTs Negeri 1 Way Kanan. Semua Hak Cipta Dilindungi.</p>
        </div>
    </footer>
    </section>
    
            <!--<div class="row justify-content-center text-center mt-2 mb-2">-->
            <!--    <div class="col-lg-8">-->
            <!--        <h2 class="section-title">Gerbang Informasi Digital Madrasah</h2>-->
            <!--        <p class="section-subtitle">Website ini sedang dalam pengembangan <br>Nantikan Update terbaru dari kami ðŸŒŸ</p>-->
            <!--    </div>-->
            <!--</div>-->



    <!--<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>-->
    <!--<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>-->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Efek scroll untuk Navbar
        window.addEventListener('scroll', function() {
            const navbar = document.querySelector('.navbar');
            if (window.scrollY > 50) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        });

        // // Menampilkan SweetAlert2 saat halaman dimuat
        // document.addEventListener('DOMContentLoaded', function() {
        //     Swal.fire({
        //         title: 'Pemberitahuan',
        //         text: 'Situs ini sedang dalam tahap pengembangan. Beberapa fitur mungkin belum berfungsi dengan sempurna.',
        //         icon: 'info',
        //         confirmButtonText: 'Baik, saya mengerti',
        //         confirmButtonColor: '#10B981',
        //         customClass: {
        //             title: 'swal-title-custom',
        //             popup: 'swal-popup-custom'
        //         }
        //     });
        // });

        // --- KODE  UNTUK  NAVBAR MOBILE ---
        document.addEventListener('click', function (event) {
            const navbarMenu = document.querySelector('#navbarNav');
            const navbarToggler = document.querySelector('.navbar-toggler');
            
            // Periksa apakah menu sedang terbuka
            let isMenuOpen = navbarMenu.classList.contains('show');

            // Cek apakah yang diklik BUKAN bagian dari navbar
            let isClickOutside = !event.target.closest('.navbar');

            // Jika menu terbuka dan klik terjadi di luar, tutup menu
            if (isMenuOpen && isClickOutside) {
                // Buat instance dari Collapse Bootstrap dan panggil method .hide()
                let bsCollapse = new bootstrap.Collapse(navbarMenu, {
                    toggle: false
                });
                bsCollapse.hide();
            }
        });
        
        // Ambil elemen navbar
    const navbar = document.querySelector('.navbar');

    // Fungsi untuk memperbarui tampilan navbar
    function updateNavbar() {
        // Cek apakah layar adalah mobile (lebar di bawah 992px)
        const isMobile = window.innerWidth < 992;
        
        // Cek apakah halaman sudah di-scroll
        const isScrolled = window.scrollY > 50;

        // Tambahkan class 'scrolled' jika di mobile ATAU jika sudah di-scroll
        if (isMobile || isScrolled) {
            navbar.classList.add('scrolled');
        } else {
            navbar.classList.remove('scrolled');
        }
    }

    // --- Event Listeners ---

    // Panggil fungsi saat halaman pertama kali dimuat
    document.addEventListener('DOMContentLoaded', updateNavbar);
    
    // Panggil fungsi saat halaman di-scroll
    window.addEventListener('scroll', updateNavbar);
    
    // Panggil fungsi saat ukuran jendela diubah (misal: rotasi HP)
    window.addEventListener('resize', updateNavbar);
        
    
    // // Menampilkan SweetAlert2 saat halaman dimuat
    //     document.addEventListener('DOMContentLoaded', function() {
    //         Swal.fire({
    //             title: 'Pemberitahuan',
    //             text: 'Situs ini sedang dalam tahap pengembangan. Beberapa fitur mungkin belum berfungsi atau belum tersedia.',
    //             icon: 'info', // 'success', 'error', 'warning', 'info', 'question'
    //             confirmButtonText: 'Baik, saya mengerti',
    //             confirmButtonColor: '#10B981', // Warna tombol hijau
    //             customClass: {
    //                 title: 'swal-title-custom',
    //                 popup: 'swal-popup-custom'
    //             }
    //         });
    //     });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php
    // Tampilkan notifikasi SweetAlert jika pesan sukses
    if (!empty($success_message)) {
        echo "Swal.fire({
            title: 'Berhasil!',
            text: '" . addslashes($success_message) . "',
            icon: 'success',
            confirmButtonText: 'OK'
        });";
    }
    ?>
    </script>
    
    
</body>
</html>