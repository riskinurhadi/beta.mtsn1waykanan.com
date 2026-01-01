<?php
// Panggil file koneksi untuk konsistensi
require_once 'koneksi.php';

// Array data layanan untuk mempermudah pengelolaan
$daftar_layanan = [
    [
        'id' => 'bpjs',
        'judul' => 'Syarat Pendaftaran BPJS Kesehatan (PBI)',
        'icon' => 'bi-shield-check',
        'syarat' => [
            "Fotokopi Kartu Keluarga (KK) terbaru.",
            "Fotokopi Kartu Tanda Penduduk (KTP) seluruh anggota keluarga.",
            "Surat Keterangan Tidak Mampu (SKTM) dari Desa/Kelurahan.",
            "Pas foto berwarna ukuran 3x4 sebanyak 1 lembar.",
            "Mengisi formulir pendaftaran yang disediakan di kantor desa."
        ]
    ],
    [
        'id' => 'ktp',
        'judul' => 'Syarat Pembuatan KTP Elektronik (Baru)',
        'icon' => 'bi-person-badge',
        'syarat' => [
            "Telah berusia 17 tahun atau sudah/pernah menikah.",
            "Fotokopi Kartu Keluarga (KK).",
            "Tidak perlu surat pengantar dari RT/RW, bisa langsung datang ke kantor kecamatan atau Dukcapil.",
            "Untuk WNA, membawa paspor dan Kartu Izin Tinggal Tetap (KITAP)."
        ]
    ],
    [
        'id' => 'kk',
        'judul' => 'Syarat Pembuatan Kartu Keluarga (KK) Baru',
        'icon' => 'bi-people-fill',
        'syarat' => [
            "Surat pengantar dari RT/RW setempat.",
            "Fotokopi buku nikah atau akta perkawinan bagi yang sudah menikah.",
            "Surat Keterangan Pindah (SKP) jika merupakan pendatang.",
            "Mengisi formulir permohonan Kartu Keluarga di kantor desa."
        ]
    ],
     [
        'id' => 'surat_pindah',
        'judul' => 'Syarat Mengurus Surat Pindah Datang/Pergi',
        'icon' => 'bi-sign-turn-right-fill',
        'syarat' => [
            "Surat pengantar dari RT/RW.",
            "Kartu Tanda Penduduk (KTP) asli dan fotokopi.",
            "Kartu Keluarga (KK) asli dan fotokopi.",
            "Pas foto berwarna ukuran 4x6 sebanyak 4 lembar.",
            "Mengisi formulir permohonan pindah yang tersedia."
        ]
    ],
    [
        'id' => 'kip',
        'judul' => 'Syarat Pengajuan Kartu Indonesia Pintar (KIP)',
        'icon' => 'bi-mortarboard-fill',
        'syarat' => [
            "Terdaftar sebagai peserta didik di lembaga pendidikan formal atau non-formal.",
            "Berasal dari keluarga miskin/rentan miskin yang terdaftar di Data Terpadu Kesejahteraan Sosial (DTKS).",
            "Memiliki Kartu Keluarga Sejahtera (KKS) atau Surat Keterangan Tidak Mampu (SKTM).",
            "Menyertakan fotokopi Kartu Keluarga (KK) dan Akta Kelahiran."
        ]
    ],
    [
        'id' => 'kis',
        'judul' => 'Syarat Pengajuan Kartu Indonesia Sehat (KIS)',
        'icon' => 'bi-heart-pulse-fill',
        'syarat' => [
            "Termasuk dalam kategori keluarga miskin dan tidak mampu.",
            "Terdaftar dalam sistem Data Terpadu Kesejahteraan Sosial (DTKS).",
            "Fotokopi Kartu Keluarga (KK) dan KTP seluruh anggota keluarga.",
            "Surat Keterangan Tidak Mampu (SKTM) dari kantor desa/kelurahan.",
            "Biasanya diajukan secara kolektif melalui pendataan oleh pihak desa."
        ]
    ]
];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Layanan Sosial - Desa Tampingan</title>

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
            background-color: #fff;
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
        
        /* Layanan Section */
        .layanan-section {
            padding: 4rem 0;
        }

        /* Custom Accordion Styling */
        .accordion-item {
            border: 1px solid #dee2e6;
            border-radius: 8px !important;
            margin-bottom: 1rem;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: box-shadow 0.3s ease;
        }
        .accordion-item:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .accordion-header .accordion-button {
            font-weight: 600;
            color: var(--dark-text);
            background-color: #fff;
            border-radius: 8px !important;
            box-shadow: none;
            font-size: 1.1rem;
        }
        .accordion-header .accordion-button:not(.collapsed) {
            color: var(--primary-color);
            background-color: #e7f1ff;
        }
        .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
        }
        .accordion-button::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%23212529'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
        }
        .accordion-button:not(.collapsed)::after {
            background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' fill='%230a58ca'%3e%3cpath fill-rule='evenodd' d='M1.646 4.646a.5.5 0 0 1 .708 0L8 10.293l5.646-5.647a.5.5 0 0 1 .708.708l-6 6a.5.5 0 0 1-.708 0l-6-6a.5.5 0 0 1 0-.708z'/%3e%3c/svg%3e");
            transform: rotate(-180deg);
        }
        .accordion-body ul {
            padding-left: 1.2rem;
        }
        .accordion-body li {
            margin-bottom: 0.75rem;
        }

        /* Info Card */
        .info-card {
            background-color: var(--light-gray);
            border-left: 5px solid var(--primary-color);
            border-radius: 8px;
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
    <section class="page-header">
        <div class="container">
            <h1 class="display-4">Layanan Administrasi & Sosial</h1>
            <p class="lead">Informasi persyaratan untuk berbagai layanan penting bagi masyarakat Desa Tampingan.</p>
        </div>
    </section>

    <section class="layanan-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="accordion" id="layananAccordion">
                        <?php foreach ($daftar_layanan as $layanan): ?>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="heading-<?php echo $layanan['id']; ?>">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $layanan['id']; ?>" aria-expanded="false" aria-controls="collapse-<?php echo $layanan['id']; ?>">
                                    <i class="bi <?php echo $layanan['icon']; ?> me-3"></i>
                                    <?php echo $layanan['judul']; ?>
                                </button>
                            </h2>
                            <div id="collapse-<?php echo $layanan['id']; ?>" class="accordion-collapse collapse" aria-labelledby="heading-<?php echo $layanan['id']; ?>" data-bs-parent="#layananAccordion">
                                <div class="accordion-body">
                                    <strong>Syarat yang harus dipenuhi:</strong>
                                    <ul>
                                        <?php foreach ($layanan['syarat'] as $syarat): ?>
                                            <li><?php echo $syarat; ?></li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Info Card -->
                    <div class="info-card p-4 mt-5">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-info-circle-fill fs-2 text-primary me-4"></i>
                            <div>
                                <h5 class="mb-1 fw-bold">Sudah Melengkapi Persyaratan?</h5>
                                <p class="mb-0">Setelah semua dokumen persyaratan lengkap, silakan datang langsung ke <strong>Kantor Desa Tampingan</strong> pada jam kerja untuk proses lebih lanjut. Petugas kami siap membantu Anda.</p>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center p-4">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
