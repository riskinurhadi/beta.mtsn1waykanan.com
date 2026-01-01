<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// Query untuk mengambil data struktur yang sedang aktif.
$sql = "SELECT judul, nama_file FROM gambar_struktur WHERE is_active = TRUE LIMIT 1";
$result = $koneksi->query($sql);

$struktur = null;
if ($result && $result->num_rows > 0) {
    // Jika ditemukan, simpan datanya ke dalam variabel.
    $struktur = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">
    
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
                <!-- PERBAIKAN: Mengubah 'align-items-center' menjadi 'align-items-lg-center' -->
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item active" href="struktur.php">Struktur Madrasah</a></li>
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

    <!-- Page Header (Konten Asli Anda) -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Struktur Organisasi</h1>
            <p class="page-subtitle">Hierarki Pimpinan dan Staf MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Struktur (Konten Asli Anda) -->
    <main class="structure-display-section">
        <div class="container">
            <?php if ($struktur): ?>
                <?php
                $file_path = 'admin/uploads/struktur/' . htmlspecialchars($struktur['nama_file']);
                $file_ext = strtolower(pathinfo($file_path, PATHINFO_EXTENSION));

                if (in_array($file_ext, ['jpg', 'jpeg', 'png', 'gif'])):
                ?>
                    <div class="structure-image-wrapper">
                        <img src="<?php echo $file_path; ?>" class="img-fluid shadow-sm" alt="<?php echo htmlspecialchars($struktur['judul']); ?>">
                    </div>
                <?php 
                elseif ($file_ext == 'pdf'): 
                ?>
                    <div class="structure-pdf-wrapper">
                        <iframe src="<?php echo $file_path; ?>" width="100%" height="800px" style="border:none;"></iframe>
                    </div>
                <?php else: ?>
                    <p class="text-center">Format file tidak didukung untuk ditampilkan.</p>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center">
                    <p>Struktur organisasi belum tersedia saat ini.</p>
                    <p>Admin dapat meng-upload dan mengaktifkan gambar struktur melalui halaman kelola.</p>
                </div>
            <?php endif; ?>
        </div>
    </main>

    <!-- Footer (Konten Asli Anda) -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    // Tutup koneksi database.
    $koneksi->close();
    ?>

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
