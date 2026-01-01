<?php
// Panggil file koneksi database
require_once 'koneksi.php';

// Definisikan path ke folder upload galeri
$upload_dir_galeri = 'admin/uploads/galeri/';

// Mengambil SEMUA media dari database, diurutkan dari yang terbaru
$query_galeri = "SELECT * FROM tb_galeri ORDER BY tanggal_upload DESC";
$result_galeri_page = $koneksi->query($query_galeri);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri Desa - Desa Tampingan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- Lightbox2 CSS (PENTING untuk tampilan galeri) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/css/lightbox.min.css">

    <style>
        /* Variabel warna dan font dasar */
        :root {
            --primary-color: #0d47a1;
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
            padding: 8rem 0;
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* ----- Gallery Styling (Sama seperti di homepage) ----- */
        .gallery-content-area {
            padding: 4rem 0;
        }
        .gallery-item {
            position: relative; display: block; overflow: hidden;
            border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            transition: all 0.3s ease; aspect-ratio: 4 / 3;
        }
        .gallery-item:hover {
            transform: translateY(-8px); box-shadow: 0 12px 35px rgba(0,0,0,0.12);
        }
        .gallery-item img, .gallery-item video {
            width: 100%; height: 100%; object-fit: cover; transition: transform 0.4s ease;
        }
        .gallery-item:hover img, .gallery-item:hover video { transform: scale(1.1); }
        .gallery-overlay {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);
            display: flex; flex-direction: column; justify-content: flex-end;
            align-items: center; text-align: center; padding: 20px; color: white;
            opacity: 0; transition: opacity 0.3s ease;
        }
        .gallery-item:hover .gallery-overlay { opacity: 1; }
        .gallery-overlay .icon {
            font-size: 2.5rem; position: absolute; top: 50%; left: 50%;
            transform: translate(-50%, -50%) scale(0.8); opacity: 0;
            transition: all 0.3s ease;
        }
        .gallery-item:hover .gallery-overlay .icon {
            transform: translate(-50%, -50%) scale(1); opacity: 1;
        }
        .gallery-overlay .title {
            font-weight: 600; font-size: 1rem; transform: translateY(20px);
            transition: transform 0.3s ease;
        }
        .gallery-item:hover .gallery-overlay .title { transform: translateY(0); }
        
        #videoModal .modal-dialog { max-width: 800px; }
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
            <h1 class="display-4">Galeri Desa</h1>
            <p class="lead">Dokumentasi visual kegiatan, acara, dan potensi Desa Tampingan.</p>
        </div>
    </section>

    <section class="gallery-content-area">
        <div class="container">
            <div class="row g-4">
                <?php if ($result_galeri_page && $result_galeri_page->num_rows > 0): ?>
                    <?php while ($media = $result_galeri_page->fetch_assoc()): ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <?php
                                $file_path = $upload_dir_galeri . htmlspecialchars($media['file_media']);
                                $media_title = htmlspecialchars($media['judul_media']);
                            ?>
                            <?php if ($media['jenis_media'] == 'foto'): ?>
                                <a href="<?php echo $file_path; ?>" data-lightbox="gallery" data-title="<?php echo $media_title; ?>" class="gallery-item">
                                    <img src="<?php echo $file_path; ?>" alt="<?php echo $media_title; ?>">
                                    <div class="gallery-overlay">
                                        <div class="icon"><i class="bi bi-arrows-fullscreen"></i></div>
                                        <div class="title"><?php echo $media_title; ?></div>
                                    </div>
                                </a>
                            <?php else: // Jika video ?>
                                <a href="#" class="gallery-item video-trigger" data-bs-toggle="modal" data-bs-target="#videoModal" data-src="<?php echo $file_path; ?>" data-title="<?php echo $media_title; ?>">
                                    <video muted preload="metadata">
                                        <source src="<?php echo $file_path; ?>#t=0.5" type="video/mp4">
                                    </video>
                                    <div class="gallery-overlay">
                                        <div class="icon"><i class="bi bi-play-circle-fill"></i></div>
                                        <div class="title"><?php echo $media_title; ?></div>
                                    </div>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <p class="text-center text-muted fs-5 mt-5">Galeri masih kosong. Belum ada media untuk ditampilkan.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
</main>

<!-- Modal untuk Video Player -->
<div class="modal fade" id="videoModal" tabindex="-1" aria-labelledby="videoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="videoModalLabel">Video Player</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body p-0">
        <video id="modalVideoPlayer" src="" class="w-100" controls autoplay></video>
      </div>
    </div>
  </div>
</div>

<footer class="bg-dark text-white text-center p-4 mt-4">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
</footer>

<!-- JavaScript Libraries -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.4/js/lightbox.min.js"></script>

<!-- Custom JS untuk Video Modal -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoModal = document.getElementById('videoModal');
    if(videoModal) {
        const modalVideoPlayer = document.getElementById('modalVideoPlayer');
        const modalTitle = document.getElementById('videoModalLabel');

        videoModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            modalTitle.textContent = button.getAttribute('data-title');
            modalVideoPlayer.setAttribute('src', button.getAttribute('data-src'));
        });

        videoModal.addEventListener('hide.bs.modal', function () {
            modalVideoPlayer.pause();
            modalVideoPlayer.setAttribute('src', '');
        });
    }
});
</script>

</body>
</html>
