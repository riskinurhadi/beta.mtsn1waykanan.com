<?php
// Letak config disesuaikan karena file ini ada di root
require_once 'admin/config.php'; 

// Ambil slug dari URL
if (!isset($_GET['slug'])) {
    header("Location: index.php"); 
    exit();
}
$slug = $_GET['slug'];

// 1. Ambil data berita utama berdasarkan slug
$sql = "SELECT id, judul, isi, penulis, kategori, gambar_utama, tanggal_publikasi FROM berita WHERE slug = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika berita tidak ditemukan
    echo "<h1>404 - Berita Tidak Ditemukan</h1>";
    echo "<p>Maaf, berita yang Anda cari tidak ada atau telah dihapus.</p>";
    exit();
}
$berita = $result->fetch_assoc();
$current_id = $berita['id']; // Simpan ID berita saat ini

// 2. Ambil 4 berita lainnya (selain berita yang sedang dibuka)
$berita_lainnya = [];
$sql_lainnya = "SELECT judul, slug, gambar_utama, tanggal_publikasi FROM berita WHERE id != ? ORDER BY tanggal_publikasi DESC LIMIT 4";
$stmt_lainnya = $conn->prepare($sql_lainnya);
$stmt_lainnya->bind_param("i", $current_id);
$stmt_lainnya->execute();
$result_lainnya = $stmt_lainnya->get_result();
while ($row = $result_lainnya->fetch_assoc()) {
    $berita_lainnya[] = $row;
}

$stmt->close();
$stmt_lainnya->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($berita['judul']); ?> - MTsN 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{ --primary-color: #28a745; --primary-hover: #218838; }
        body { font-family: 'Poppins', sans-serif; line-height: 1.8; color: #333; margin: 0; background-color: #f4f7f6; }
        .container { max-width: 800px; margin: 40px auto; padding: 20px 30px; background-color: #fff; box-shadow: 0 0 20px rgba(0,0,0,0.07); border-radius: 8px; }
        
        /* Style Artikel */
        .article-header .main-image { width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; }
        .article-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; line-height: 1.3; }
        .article-meta { font-size: 14px; color: #777; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; }
        .article-meta span { display: flex; align-items: center; }
        .article-meta i { margin-right: 8px; color: var(--primary-color); }
        .article-content { font-size: 16px; border-top: 1px solid #eee; padding-top: 20px; }
        .article-content p { margin-bottom: 1.5em; }
        .article-content ul, .article-content ol { padding-left: 25px; margin-bottom: 1.5em; }
        .article-content a { color: var(--primary-color); text-decoration: none; }
        .article-content a:hover { text-decoration: underline; }

        /* Style untuk Tombol Aksi */
        .article-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px; }
        .btn-action {
            display: inline-flex; align-items: center; gap: 8px;
            padding: 8px 16px; border-radius: 6px; text-decoration: none;
            font-weight: 500; font-size: 14px; border: 1px solid #ddd;
            background-color: #fff; color: #333; cursor: pointer; transition: all 0.2s ease;
        }
        .btn-action:hover { background-color: #f5f5f5; border-color: #ccc; }
        .share-buttons { margin-left: auto; display: flex; gap: 10px; align-items: center; }
        .share-buttons .btn-action { border-radius: 50%; width: 40px; height: 40px; padding: 0; justify-content: center; font-size: 18px; }
        .share-buttons .btn-whatsapp { color: #25D366; }
        .share-buttons .btn-copy { color: #3498db; }
        .share-buttons span { font-size: 14px; font-weight: 500; color: #555; }

        /* Style untuk Berita Lainnya */
        .other-news { border-top: 1px solid #eee; padding-top: 20px; margin-top: 40px; }
        .other-news h2 { font-size: 22px; margin-bottom: 20px; }
        .news-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .news-card { background-color: #fff; border-radius: 8px; overflow: hidden; text-decoration: none; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.12); }
        .news-card img { width: 100%; height: 150px; object-fit: cover; }
        .news-card-body { padding: 15px; }
        .news-card-body h3 { font-size: 16px; margin: 0; line-height: 1.4; }

        /* --- Style untuk Cetak/Print --- */
        @media print {
            body { background-color: #fff; font-size: 12pt; }
            .container { margin: 0; padding: 0; max-width: 100%; box-shadow: none; border-radius: 0; }
            .article-actions, .other-news { display: none !important; }
            .article-header .main-image { max-height: 350px; }
        }
        
        @media (max-width: 768px) {
            .news-grid { grid-template-columns: 1fr; }
            .article-header h1 { font-size: 26px; }
            .article-actions { flex-direction: column; align-items: flex-start; }
            .share-buttons { margin-left: 0; margin-top: 10px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <article>
            <header class="article-header">
                <img src="admin/uploads/berita/<?php echo htmlspecialchars($berita['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>" class="main-image">
                <h1><?php echo htmlspecialchars($berita['judul']); ?></h1>
                <div class="article-meta">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($berita['penulis']); ?></span>
                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($berita['tanggal_publikasi'])); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($berita['kategori']); ?></span>
                </div>
            </header>

            <div class="article-actions">
                <a href="https://mtsn1waykanan.com/#berita" class="btn-action"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita</a>
                <button onclick="window.print()" class="btn-action"><i class="fas fa-print"></i> Cetak/Simpan PDF</button>
                <div class="share-buttons">
                    <span>Bagikan:</span>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($berita['judul'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn-action btn-whatsapp" title="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <button onclick="copyLink()" class="btn-action btn-copy" title="Salin Link"><i class="fas fa-link"></i></button>
                </div>
            </div>

            <section class="article-content">
                <?php echo $berita['isi']; ?>
            </section>
        </article>

        <?php if (!empty($berita_lainnya)): ?>
        <section class="other-news">
            <h2>Berita Lainnya</h2>
            <div class="news-grid">
                <?php foreach ($berita_lainnya as $item): ?>
                <a href="detail_berita.php?slug=<?php echo $item['slug']; ?>" class="news-card">
                    <img src="admin/uploads/berita/<?php echo htmlspecialchars($item['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($item['judul']); ?>">
                    <div class="news-card-body">
                        <h3><?php echo htmlspecialchars($item['judul']); ?></h3>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>
        </section>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyLink() {
            // Mengambil URL saat ini
            const currentUrl = window.location.href;
            
            // Menyalin URL ke clipboard
            navigator.clipboard.writeText(currentUrl).then(() => {
                // Tampilkan notifikasi sukses dengan SweetAlert2
                Swal.fire({
                    title: 'Berhasil!',
                    text: 'Link berita telah disalin ke clipboard.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }, () => {
                // Tampilkan notifikasi error jika gagal
                Swal.fire({
                    title: 'Gagal!',
                    text: 'Tidak dapat menyalin link.',
                    icon: 'error'
                });
            });
        }
    </script>
</body>
</html>