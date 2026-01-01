<?php
require 'koneksi.php';

// Ambil slug dari URL, pastikan tidak kosong
$slug = $_GET['slug'] ?? null;
if (!$slug) {
    // Jika tidak ada slug, kembalikan ke halaman utama atau tampilkan error
    header("Location: index.php");
    exit();
}

// Ambil data berita spesifik berdasarkan slug
$stmt = $koneksi->prepare("SELECT b.*, a.nama_lengkap 
                          FROM tb_berita b 
                          JOIN tb_admin a ON b.id_admin = a.id_admin 
                          WHERE b.slug = ?");
$stmt->bind_param("s", $slug);
$stmt->execute();
$result_berita = $stmt->get_result();
$berita = $result_berita->fetch_assoc();
$stmt->close();

// Jika berita tidak ditemukan, tampilkan halaman 404 sederhana
if (!$berita) {
    http_response_code(404);
    // Anda bisa membuat halaman 404.php yang lebih bagus
    echo "<h1>404 Not Found</h1><p>Halaman berita yang Anda cari tidak ditemukan.</p><a href='index.php'>Kembali ke Beranda</a>";
    exit();
}

// Ambil 5 berita terbaru lainnya untuk sidebar (kecuali berita yang sedang dibaca)
$query_lainnya = "SELECT judul, slug, gambar, tanggal_publish FROM tb_berita WHERE slug != ? ORDER BY tanggal_publish DESC LIMIT 5";
$stmt_lainnya = $koneksi->prepare($query_lainnya);
$stmt_lainnya->bind_param("s", $slug);
$stmt_lainnya->execute();
$hasil_lainnya = $stmt_lainnya->get_result();
$stmt_lainnya->close();

// PERBAIKAN: Ganti path gambar di dalam isi berita
$isi_berita_diperbaiki = str_replace('src="uploads/', 'src="admin/uploads/', $berita['isi']);

// URL lengkap dari halaman saat ini untuk fitur share
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]";
$current_url = $base_url . $_SERVER['REQUEST_URI'];

// Data untuk Open Graph Meta Tags
function buat_ringkasan_og($konten, $panjang = 155) {
    $teks_polos = strip_tags($konten);
    if (strlen($teks_polos) > $panjang) {
        $potong_teks = substr($teks_polos, 0, $panjang);
        $akhir_spasi = strrpos($potong_teks, ' ');
        $ringkasan = substr($potong_teks, 0, $akhir_spasi);
        return $ringkasan . '...';
    }
    return $teks_polos;
}
$og_description = buat_ringkasan_og($berita['isi']);
$og_image_url = $base_url . '/admin/uploads/' . htmlspecialchars($berita['gambar']);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <!-- Judul halaman dinamis sesuai judul berita -->
    <title><?php echo htmlspecialchars($berita['judul']); ?> - Kecamatan Boja</title>
    
    <!-- Open Graph Meta Tags untuk Social Media Sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($berita['judul']); ?>" />
    <meta property="og:description" content="<?php echo htmlspecialchars($og_description); ?>" />
    <meta property="og:image" content="<?php echo $og_image_url; ?>" />
    <meta property="og:url" content="<?php echo $current_url; ?>" />
    <meta property="og:type" content="article" />
    <meta property="og:site_name" content="Website Kecamatan Boja" />

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Internal CSS (Bisa disalin dari index.php dan disesuaikan) -->
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #ff9800;
            --light-gray: #f8f9fa;
            --dark-text: #333;
        }
        body { font-family: 'Poppins', sans-serif; background-color: white; color: var(--dark-text); }
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        /* CSS PERBAIKAN NAVBAR */
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; }

        .article-header { padding: 3rem 0; background-color: var(--light-gray); text-align: center; border-bottom: 1px solid #ddd; }
        .article-header h1 { font-weight: 700; color: var(--primary-color); }
        .article-meta { color: #6c757d; }

        .article-content img { max-width: 100%; height: auto; border-radius: 8px; margin: 1rem 0; }
        
        /* CSS Fitur Share */
        .share-widget { padding: 20px; border-radius: 12px; }
        .share-widget h6 { font-weight: 600; margin-bottom: 15px; }
        #qrcode { padding: 10px; background: white; display: inline-block; border-radius: 8px; }

        .sidebar-widget { background-color: var(--light-gray); padding: 20px; border-radius: 12px; margin-bottom: 2rem; }
        .sidebar-widget h5 { font-weight: 600; border-bottom: 2px solid var(--secondary-color); padding-bottom: 10px; margin-bottom: 20px; }
        .recent-post-item { display: flex; align-items: center; margin-bottom: 15px; text-decoration: none; color: var(--dark-text); }
        .recent-post-item:hover .recent-post-title { color: var(--primary-color); }
        .recent-post-img { width: 80px; height: 60px; object-fit: cover; border-radius: 5px; margin-right: 15px; }
        .recent-post-title { font-size: 0.9rem; font-weight: 500; }

        .footer { background-color: #2c3e50; color: white; padding: 3rem 0; }
        .footer a { color: #bdc3c7; }
    </style>
</head>
<body>

    <!-- Header / Navbar (Sederhana, bisa diganti dengan include file header jika ada) -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="#">
                    <!-- Ganti logo.png dengan link logo Anda -->
                    <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                    <div class="logo-text">
                        <span class="title">Pemerintah Kabupaten Kendal</span>
                        <span class="subtitle">Desa Tampingan</span>
                    </div>
                </a>
                <a href="index.php" class="btn btn-outline-light">Kembali ke Beranda</a>
            </div>
        </nav>
    </header>

    <main>
        <!-- Judul dan Meta Berita -->
        <section class="article-header">
            <div class="container">
                <h1><?php echo htmlspecialchars($berita['judul']); ?></h1>
                <p class="article-meta">
                    <i class="bi bi-person-fill"></i> <?php echo htmlspecialchars($berita['nama_lengkap']); ?>
                    &nbsp;&nbsp;|&nbsp;&nbsp;
                    <i class="bi bi-calendar-event"></i> <?php echo date('d F Y', strtotime($berita['tanggal_publish'])); ?>
                </p>
            </div>
        </section>

        <!-- Konten Utama dan Sidebar -->
        <section class="py-5">
            <div class="container">
                <div class="row">
                    <!-- Kolom Konten Berita -->
                    <div class="col-lg-8">
                        <article>
                            <img src="admin/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" class="img-fluid rounded mb-4" alt="Gambar Utama Berita">
                        
                        <!-- Share Widget -->
                        <!--<div class="share-widget">-->
                        <!--    <h5 class="text-center">Bagikan Melalui</h5>-->
                            <!--<h6><i class="bi bi-share-fill me-2 align-items-center"></i>Bagikan Berita Ini</h6>-->
                        <!--    <div class="text-center flex-wrap">-->
                        <!--        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($berita['judul'] . ' - ' . $current_url); ?>" target="_blank" class="btn btn-success me-2 mb-2"><i class="bi bi-whatsapp"></i> WhatsApp</a>-->
                        <!--        <button id="copyLinkBtn" class="btn btn-primary me-2 mb-2"><i class="bi bi-clipboard"></i> Salin Tautan</button>-->
                        <!--        <button class="btn btn-dark mb-2" data-bs-toggle="modal" data-bs-target="#qrModal"><i class="bi bi-qr-code"></i> Kode QR</button>-->
                        <!--        <span id="copyFeedback" class="text-success ms-2 d-none">Tautan disalin!</span>-->
                        <!--    </div>-->
                        <!--</div>-->
                        
                            <div class="article-content">
                                <!-- Tampilkan isi berita dari editor yang pathnya sudah diperbaiki -->
                                <?php echo $isi_berita_diperbaiki; ?>
                            </div>
                        </article>
                        <!-- Share Widget -->
                        <div class="share-widget">
                            <h5 class="text-center">Bagikan Melalui</h5>
                            <!--<h6><i class="bi bi-share-fill me-2 align-items-center"></i>Bagikan Berita Ini</h6>-->
                            <div class="text-center flex-wrap">
                                <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($berita['judul'] . ' - ' . $current_url); ?>" target="_blank" class="btn btn-success me-2 mb-2"><i class="bi bi-whatsapp"></i> WhatsApp</a>
                                <button id="copyLinkBtn" class="btn btn-primary me-2 mb-2"><i class="bi bi-clipboard"></i> Salin Tautan</button>
                                <button class="btn btn-dark mb-2" data-bs-toggle="modal" data-bs-target="#qrModal"><i class="bi bi-qr-code"></i> Kode QR</button>
                                <span id="copyFeedback" class="text-success ms-2 d-none">Tautan disalin!</span>
                            </div>
                        </div>
                        
                        <!-- Share Widget -->
                        <!--<div class="share-widget mt-5">-->
                        <!--    <h6><i class="bi bi-share-fill me-2"></i>Bagikan Berita Ini</h6>-->
                        <!--    <div class="d-flex align-items-center flex-wrap">-->
                        <!--        <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($berita['judul'] . ' - ' . $current_url); ?>" target="_blank" class="btn btn-success me-2 mb-2"><i class="bi bi-whatsapp"></i> WhatsApp</a>-->
                        <!--        <button id="copyLinkBtn" class="btn btn-primary me-2 mb-2"><i class="bi bi-clipboard"></i> Salin Tautan</button>-->
                        <!--        <button class="btn btn-dark mb-2" data-bs-toggle="modal" data-bs-target="#qrModal"><i class="bi bi-qr-code"></i> Kode QR</button>-->
                        <!--        <span id="copyFeedback" class="text-success ms-2 d-none">Tautan disalin!</span>-->
                        <!--    </div>-->
                        <!--</div>-->
                    </div>

                    <!-- Kolom Sidebar -->
                    <div class="col-lg-4">
                        <div class="sidebar-widget">
                            <h5><i class="bi bi-newspaper me-2"></i>Berita Lainnya</h5>
                            <?php if ($hasil_lainnya->num_rows > 0): ?>
                                <?php while($lainnya = $hasil_lainnya->fetch_assoc()): ?>
                                <a href="detail_berita.php?slug=<?php echo htmlspecialchars($lainnya['slug']); ?>" class="recent-post-item">
                                    <img src="admin/uploads/<?php echo htmlspecialchars($lainnya['gambar']); ?>" class="recent-post-img" alt="Thumbnail">
                                    <div>
                                        <div class="recent-post-title"><?php echo htmlspecialchars($lainnya['judul']); ?></div>
                                        <small class="text-muted"><?php echo date('d M Y', strtotime($lainnya['tanggal_publish'])); ?></small>
                                    </div>
                                </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>Tidak ada berita lain untuk ditampilkan.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>
    
    <!-- Modal untuk QR Code -->
    <div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="qrModalLabel">Pindai Kode QR</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body text-center">
            <div id="qrcode"></div>
            <p class="mt-3">Pindai kode ini untuk membuka berita di perangkat lain.</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Footer (Sederhana, bisa diganti dengan include file footer jika ada) -->
    <footer class="footer">
        <div class="container text-center">
            <p>&copy; 2025 Hak Cipta Dilindungi | Kecamatan Boja, Kabupaten Kendal</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Library untuk generate QR Code -->
    <script src="https://cdn.jsdelivr.net/npm/qrcodejs@1.0.0/qrcode.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Fungsi Salin Tautan
            const copyLinkBtn = document.getElementById('copyLinkBtn');
            const copyFeedback = document.getElementById('copyFeedback');
            if (copyLinkBtn) {
                copyLinkBtn.addEventListener('click', function() {
                    const urlToCopy = window.location.href;
                    
                    const textArea = document.createElement('textarea');
                    textArea.value = urlToCopy;
                    document.body.appendChild(textArea);
                    textArea.select();
                    try {
                        document.execCommand('copy');
                        copyFeedback.classList.remove('d-none');
                        setTimeout(() => {
                            copyFeedback.classList.add('d-none');
                        }, 2500);
                    } catch (err) {
                        console.error('Gagal menyalin tautan: ', err);
                    }
                    document.body.removeChild(textArea);
                });
            }

            // Fungsi Generate QR Code
            const qrModal = document.getElementById('qrModal');
            if (qrModal) {
                qrModal.addEventListener('show.bs.modal', function (event) {
                    const qrcodeContainer = document.getElementById('qrcode');
                    qrcodeContainer.innerHTML = ''; 
                    new QRCode(qrcodeContainer, {
                        text: window.location.href,
                        width: 220,
                        height: 220,
                        colorDark : "#000000",
                        colorLight : "#ffffff",
                        correctLevel : QRCode.CorrectLevel.H
                    });
                });
            }
        });
    </script>
</body>
</html>

