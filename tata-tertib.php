<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// Query untuk mengambil data tata tertib yang sedang aktif.
$sql = "SELECT judul, nama_file FROM tata_tertib WHERE is_active = TRUE LIMIT 1";
$result = $koneksi->query($sql);

$tata_tertib = null;
if ($result && $result->num_rows > 0) {
    // Jika ditemukan, simpan datanya ke dalam variabel.
    $tata_tertib = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tata Tertib - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolled">
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
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item active" href="tata-tertib.php">Tata Tertib Madrasah</a></li>
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

    <!-- Page Header -->
    <header class="page-header">
        <div class="container mt-5">
            <h1 class="page-title">Tata Tertib Madrasah</h1>
            <p class="page-subtitle">Pedoman Peraturan dan Kedisiplinan Siswa</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Tata Tertib -->
    <main class="rules-display-section">
        <div class="container">
            <?php if ($tata_tertib): ?>
                <h2 class="rules-display-title"><?php echo htmlspecialchars($tata_tertib['judul']); ?></h2>
                <?php
                $file_path = 'admin/uploads/tata_tertib/' . htmlspecialchars($tata_tertib['nama_file']);
                $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])):
                ?>
                    <div class="rules-image-wrapper">
                        <img src="<?php echo $file_path; ?>" class="img-fluid shadow-sm" alt="<?php echo htmlspecialchars($tata_tertib['judul']); ?>">
                    </div>
                <?php 
                elseif ($file_ext == 'pdf'): 
                ?>
                    <!-- PERBAIKAN: Menggunakan <embed> untuk menampilkan PDF secara langsung -->
                    <div class="rules-pdf-wrapper">
                        <embed src="<?php echo $file_path; ?>" type="application/pdf" width="100%" height="800px" />
                        <div class="pdf-fallback text-center mt-4">
                            <p class="mb-2">Tidak dapat menampilkan PDF? Coba buka di tab baru atau unduh.</p>
                            <a href="<?php echo $file_path; ?>" class="btn btn-outline-secondary" target="_blank">Buka di Tab Baru</a>
                            <a href="<?php echo $file_path; ?>" class="btn btn-success" download>
                                <i class="fas fa-download me-2"></i>Download Tata Tertib
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <p class="text-center">Format file tidak didukung untuk ditampilkan.</p>
                <?php endif; ?>
            <?php else: ?>
                <div class="text-center">
                    <p>Dokumen tata tertib belum tersedia saat ini.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $koneksi->close();
    ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
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
