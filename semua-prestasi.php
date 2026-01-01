<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// --- Logika Pagination ---
$limit = 9; // Jumlah prestasi yang ditampilkan per halaman.
$halaman = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
$offset = ($halaman - 1) * $limit;

// Query untuk menghitung total prestasi.
$total_result = $koneksi->query("SELECT COUNT(*) AS total FROM prestasi");
$total_records = $total_result->fetch_assoc()['total'];
$total_halaman = ceil($total_records / $limit);

// Query untuk mengambil data prestasi sesuai halaman.
$query_prestasi = "SELECT * FROM prestasi ORDER BY tahun DESC, id DESC LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($query_prestasi);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$result_prestasi = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Semua Prestasi - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        /* =============================================
   STYLE UNTUK HALAMAN SEMUA PRESTASI
   ============================================= */

.all-prestasi-section {
    padding: 80px 0;
    background-color: #f8f9fa;
}

/* =============================================
   STYLE UNTUK KARTU PRESTASI
   ============================================= */

/* PERBAIKAN: Mengatur link pembungkus kartu */
.prestasi-card-link {
    text-decoration: none; /* Menghilangkan garis bawah */
    color: inherit; /* Mewarisi warna teks normal */
    display: block;
    height: 100%;
}
.prestasi-card-link:hover {
    color: inherit; /* Pastikan warna tidak berubah saat hover */
}

/* PERBAIKAN: Mengembalikan warna asli teks di dalam link */
.prestasi-card-link .prestasi-title {
    color: #333;
    transition: color 0.3s ease;
}
.prestasi-card-link .prestasi-description,
.prestasi-card-link .prestasi-info {
    color: #555;
}

/* PERBAIKAN: Memberi efek hover hanya pada judul */
.prestasi-card-link:hover .prestasi-title {
    color: #198754; /* Warna hijau saat hover */
}


/* =============================================
   PAGINATION
   ============================================= */
.pagination .page-item .page-link {
    color: #198754;
    border: 1px solid #dee2e6;
    margin: 0 5px;
    border-radius: 0.375rem;
    font-weight: 500;
}

.pagination .page-item.active .page-link {
    background-color: #198754;
    border-color: #198754;
    color: #ffffff;
    z-index: 3;
}

.pagination .page-item.disabled .page-link {
    color: #6c757d;
}

.pagination .page-item .page-link:hover {
    background-color: #e0f2f1;
    color: #198754;
}

    </style>

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
                            Program Unggulan
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/unggulan">Kelas Unggulan</a></li>
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tahfiz">Tahfiz Al Qur'an</a></li>
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
                        <a class="btn btn-ppdb" href="https://mtsn1waykanan.com/ppdb">Info PPDB</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container mt-5">
            <h1 class="page-title">Semua Prestasi</h1>
            <p class="page-subtitle">Seluruh Pencapaian Gemilang Siswa-siswi MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Prestasi -->
    <main class="all-prestasi-section">
        <div class="container">
            <div class="row g-4">
                <?php
                if ($result_prestasi && $result_prestasi->num_rows > 0):
                    while ($data = $result_prestasi->fetch_assoc()):
                        $foto = (!empty($data['foto_url'])) ? 'admin/uploads/prestasi/' . htmlspecialchars($data['foto_url']) : 'https://placehold.co/600x400/E0F2F1/198754?text=Prestasi';
                        $deskripsi_lengkap = $data['deskripsi'];
                        $panjang_maksimal = 100;
                        if (mb_strlen($deskripsi_lengkap, 'UTF-8') > $panjang_maksimal) {
                            $deskripsi_tampil = mb_substr($deskripsi_lengkap, 0, $panjang_maksimal, 'UTF-8') . '...';
                        } else {
                            $deskripsi_tampil = $deskripsi_lengkap;
                        }
                ?>
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
                    endwhile;
                else:
                    echo '<div class="col-12"><p class="text-center">Belum ada data prestasi untuk ditampilkan.</p></div>';
                endif;
                ?>
            </div>

            <!-- Navigasi Halaman (Pagination) -->
            <?php if($total_halaman > 1): ?>
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                        <li class="page-item <?php if($halaman == $i) echo 'active'; ?>">
                            <a class="page-link" href="semua-prestasi.php?halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $stmt->close();
    $koneksi->close();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk Navbar
        const navbar = document.querySelector('.navbar');
        function updateNavbar() { navbar.classList.add('scrolled'); }
        document.addEventListener('DOMContentLoaded', updateNavbar);
        window.addEventListener('scroll', updateNavbar);
        window.addEventListener('resize', updateNavbar);
        document.addEventListener('click', function (event) { /* ... (kode tutup menu mobile) ... */ });
    </script>
</body>
</html>
