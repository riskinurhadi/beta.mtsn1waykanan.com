<?php
include 'koneksi.php';

// Ambil ID dari URL dan pastikan itu adalah angka
$prestasi_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($prestasi_id <= 0) {
    // Jika ID tidak valid, arahkan ke halaman 404
    header("Location: 404.php");
    exit();
}

// Siapkan query untuk mengambil data prestasi berdasarkan ID
$sql = "SELECT * FROM prestasi WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $prestasi_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika data tidak ditemukan, arahkan ke halaman 404
    header("Location: 404.php");
    exit();
}

// Ambil data prestasi
$prestasi = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($prestasi['nama_prestasi']); ?> - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
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
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="guru.php">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="berita.php">Berita</a>
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
                        <a class="btn btn-ppdb" href="ppdb-coming-soon.php">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header (Konten Asli Anda) -->
    <header class="page-header">
        <div class="container mt-5">
            <h1 class="page-title">Detail Prestasi</h1>
            <p class="page-subtitle"><?php echo htmlspecialchars($prestasi['nama_prestasi']); ?></p>
        </div>
    </header>

    <!-- Konten Detail Prestasi (Konten Asli Anda) -->
    <main class="detail-page-section">
        <div class="container mt-5">
            <div class="row justify-content-center">
                <div class="col-lg-9">
                    <div class="card shadow-sm detail-card">
                        <?php 
                            $foto_detail = (!empty($prestasi['foto_url'])) ? 'admin/uploads/prestasi/' . htmlspecialchars($prestasi['foto_url']) : 'https://placehold.co/1200x600/E0F2F1/198754?text=Gambar+Prestasi';
                        ?>
                        <img src="<?php echo $foto_detail; ?>" class="card-img-top detail-img" alt="Foto <?php echo htmlspecialchars($prestasi['nama_prestasi']); ?>">
                        <div class="card-body p-4 p-md-5">
                            <div class="detail-meta mb-3">
                                <span class="badge bg-success me-2"><?php echo htmlspecialchars($prestasi['tingkat']); ?></span>
                                <span class="text-muted">Oleh: <?php echo htmlspecialchars($prestasi['nama_siswa']); ?> - <?php echo htmlspecialchars($prestasi['tahun']); ?></span>
                            </div>
                            <h2 class="card-title detail-title"><?php echo htmlspecialchars($prestasi['nama_prestasi']); ?></h2>
                            <hr>
                            <div class="detail-content">
                                <?php echo nl2br(htmlspecialchars($prestasi['deskripsi'])); ?>
                            </div>
                        </div>
                    </div>
                    <div class="text-center mt-4 mb-5">
                        <a href="index.php#prestasi" class="btn btn-outline-secondary">← Kembali ke Beranda</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer (Konten Asli Anda) -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $stmt->close();
    $koneksi->close();
    ?>
    
    <!-- ============================================= -->
    <!-- SCRIPT JS BARU (SESUAI DENGAN index.php) -->
    <!-- ============================================= -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Ambil elemen navbar
        const navbar = document.querySelector('.navbar');

        // Fungsi untuk memperbarui tampilan navbar
        function updateNavbar() {
            // Di halaman selain Beranda, navbar selalu 'scrolled'
            navbar.classList.add('scrolled');
        }

        // --- Event Listeners untuk Navbar ---
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
