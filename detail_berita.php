<?php
// Letak config disesuaikan karena file ini ada di root
require_once 'admin/config.php';

// Ambil slug dari URL
if (!isset($_GET['slug'])) {
    header("Location: index.php");
    exit();
}
$slug = $_GET['slug'];

// --- TAMBAHAN: LOGIKA UNTUK MENAMBAH JUMLAH PEMBACA ---
// Jalankan UPDATE query untuk menambah counter pembaca setiap kali halaman diakses.
// Ini dilakukan sebelum mengambil data agar tidak menyebabkan race condition sederhana.
$update_sql = "UPDATE berita SET jumlah_pembaca = jumlah_pembaca + 1 WHERE slug = ?";
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param("s", $slug);
$update_stmt->execute();
$update_stmt->close();


// 1. Ambil data berita utama berdasarkan slug, termasuk semua gambar dan jumlah pembaca
// --- DIMODIFIKASI: Menambahkan 'jumlah_pembaca' ke query SELECT ---
$sql = "SELECT id, judul, slug, isi, penulis, kategori, gambar_utama, gambar_2, gambar_3, tanggal_publikasi, jumlah_pembaca FROM berita WHERE slug = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<h1>404 - Berita Tidak Ditemukan</h1>";
    echo "<p>Maaf, berita yang Anda cari tidak ada atau telah dihapus.</p>";
    exit();
}
$berita = $result->fetch_assoc();
$current_id = $berita['id'];

// 2. Ambil 4 berita lainnya (selain berita yang sedang dibuka)
// ... (sisa kode PHP Anda sampai $conn->close() tetap sama) ...
$berita_lainnya = [];
$sql_lainnya = "SELECT judul, slug, gambar_utama, tanggal_publikasi FROM berita WHERE id != ? ORDER BY tanggal_publikasi DESC LIMIT 4";
$stmt_lainnya = $conn->prepare($sql_lainnya);
$stmt_lainnya->bind_param("i", $current_id);
$stmt_lainnya->execute();
$result_lainnya = $stmt_lainnya->get_result();
while ($row = $result_lainnya->fetch_assoc()) {
    $berita_lainnya[] = $row;
}

// --- PERBAIKAN FINAL: Menggunakan DOMDocument untuk menyisipkan gambar ---
$isi_artikel = $berita['isi'];
$dom = new DOMDocument();
@$dom->loadHTML('<?xml encoding="utf-8" ?>' . $isi_artikel, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

$paragraphs = $dom->getElementsByTagName('p');
$valid_paragraphs = [];

foreach ($paragraphs as $p) {
    if (strlen(trim($p->nodeValue)) > 0) {
        $valid_paragraphs[] = $p;
    }
}

if (!empty($berita['gambar_2']) && count($valid_paragraphs) >= 2) {
    $path_gambar_2 = 'admin/uploads/berita/' . htmlspecialchars($berita['gambar_2']);
    $img_node_2 = $dom->createElement('img');
    $img_node_2->setAttribute('src', $path_gambar_2);
    $img_node_2->setAttribute('alt', 'Gambar Tambahan 1');
    $img_node_2->setAttribute('class', 'in-article-image');
    $target_p_2 = $valid_paragraphs[1];
    if ($target_p_2->nextSibling) {
        $target_p_2->parentNode->insertBefore($img_node_2, $target_p_2->nextSibling);
    } else {
        $target_p_2->parentNode->appendChild($img_node_2);
    }
}

if (!empty($berita['gambar_3']) && count($valid_paragraphs) >= 3) {
    $path_gambar_3 = 'admin/uploads/berita/' . htmlspecialchars($berita['gambar_3']);
    $img_node_3 = $dom->createElement('img');
    $img_node_3->setAttribute('src', $path_gambar_3);
    $img_node_3->setAttribute('alt', 'Gambar Tambahan 2');
    $img_node_3->setAttribute('class', 'in-article-image');
    $target_p_3 = $valid_paragraphs[2];
    if ($target_p_3->nextSibling) {
        $target_p_3->parentNode->insertBefore($img_node_3, $target_p_3->nextSibling);
    } else {
        $target_p_3->parentNode->appendChild($img_node_3);
    }
}

$isi_dengan_gambar = $dom->saveHTML();


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

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
        }

        /* PENYESUAIAN: Padding atas untuk body agar konten tidak tertutup navbar fixed-top */
        body { 
            font-family: 'Poppins', sans-serif; 
            line-height: 1.8; 
            color: #333; 
            margin: 0; 
            background-color: #f4f7f6;
            padding-top: 80px; /* Jarak untuk fixed-top navbar */
        }

        /* --- CSS BARU UNTUK NAVBAR --- */
        .navbar {
            background-color: var(--primary-color) !important;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            font-size: 1rem;
        }
        .navbar .navbar-brand img {
            height: 45px;
        }
        .navbar .nav-link {
            color: rgba(255, 255, 255, 0.85) !important;
            font-weight: 500;
            padding: 0.5rem 1rem;
            border-radius: 8px;
            transition: all 0.2s ease;
        }
        .navbar .nav-link:hover, .navbar .nav-link.active {
            color: #fff !important;
            background-color: rgba(0, 0, 0, 0.15);
        }
        .navbar-toggler {
            border: none;
        }
        .navbar-toggler:focus {
            box-shadow: none;
        }
        .btn-ppdb {
            background-color: #fff;
            color: var(--primary-color) !important;
            border: 2px solid #fff;
            border-radius: 50px;
            font-weight: 600;
            padding: 8px 25px !important;
            transition: all 0.3s ease;
        }
        .btn-ppdb:hover {
            background-color: transparent;
            color: #fff !important;
        }
        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .dropdown-item {
            font-weight: 500;
        }
        .dropdown-item:hover {
            background-color: var(--primary-color);
            color: #fff;
        }

        /* --- CSS KONTEN BERITA (TETAP SAMA) box-shadow: 0 0 20px rgba(0,0,0,0.07);--- */
        .container-berita { max-width: 800px; margin: 40px auto; padding: 20px 30px; background-color: #fff;  border-radius: 8px; }
        .article-header .main-image { width: 100%; height: auto; max-height: 400px; object-fit: cover; border-radius: 8px; margin-bottom: 20px; }
        .article-header h1 { font-size: 32px; font-weight: 700; margin-bottom: 10px; line-height: 1.3; }
        .article-meta { font-size: 14px; color: #777; margin-bottom: 20px; display: flex; flex-wrap: wrap; gap: 15px; }
        .article-meta span { display: flex; align-items: center; }
        .article-meta i { margin-right: 8px; color: var(--primary-color); }
        .article-content { font-size: 16px; border-top: 1px solid #eee; padding-top: 20px; word-wrap: break-word; }
        .article-content p { margin-bottom: 1.5em; }
        .article-content ul, .article-content ol { padding-left: 25px; margin-bottom: 1.5em; }
        .article-content a { color: var(--primary-color); text-decoration: none; }
        .article-content a:hover { text-decoration: underline; }
        .in-article-image { width: 100%; height: auto; border-radius: 8px; margin: 30px 0; box-shadow: 0 4px 15px rgba(0,0,0,0.1); }
        .article-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; margin-bottom: 30px; }
        .btn-action { display: inline-flex; align-items: center; gap: 8px; padding: 8px 16px; border-radius: 6px; text-decoration: none; font-weight: 500; font-size: 14px; border: 1px solid #ddd; background-color: #fff; color: #333; cursor: pointer; transition: all 0.2s ease; }
        .btn-action:hover { background-color: #f5f5f5; border-color: #ccc; }
        .share-buttons { margin-left: auto; display: flex; gap: 10px; align-items: center; }
        .share-buttons .btn-action { border-radius: 50%; width: 40px; height: 40px; padding: 0; justify-content: center; font-size: 18px; }
        .share-buttons .btn-whatsapp { color: #25D366; }
        .share-buttons .btn-copy { color: #3498db; }
        .share-buttons span { font-size: 14px; font-weight: 500; color: #555; }
        .other-news { border-top: 1px solid #eee; padding-top: 20px; margin-top: 40px; }
        .other-news h2 { font-size: 22px; margin-bottom: 20px; }
        .news-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; }
        .news-card { background-color: #fff; border-radius: 8px; overflow: hidden; text-decoration: none; color: #333; box-shadow: 0 2px 8px rgba(0,0,0,0.08); transition: all 0.3s ease; }
        .news-card:hover { transform: translateY(-5px); box-shadow: 0 5px 15px rgba(0,0,0,0.12); }
        .news-card img { width: 100%; height: 150px; object-fit: cover; }
        .news-card-body { padding: 15px; }
        .news-card-body h3 { font-size: 16px; margin: 0; line-height: 1.4; }

        @media print {
            body { background-color: #fff; font-size: 12pt; padding-top: 0; }
            .navbar, .article-actions, .other-news, .main-image, .in-article-image { display: none !important; }
            .container-berita { margin: 0; padding: 0; max-width: 100%; box-shadow: none; border-radius: 0; }
        }
        
        @media (max-width: 768px) {
            .news-grid { grid-template-columns: 1fr; }
            .article-header h1 { font-size: 26px; }
            .article-actions { flex-direction: column; align-items: flex-start; }
            .share-buttons { margin-left: 0; margin-top: 10px; }
        }
        
        .main-footer {
            background-color: #343a40; /* Warna latar belakang gelap */
            color: #f8f9fa; /* Warna teks terang */
            padding: 25px 0;
            text-align: center;
            font-size: 14px;
            margin-top: 50px;
            margin-bottom: 0px;
            line-height: 1.6;
        }
        .main-footer a {
            color: #ffffff; /* Warna link putih */
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            margin-bottom: 0px;
        }
        .main-footer a:hover {
            text-decoration: underline;
        }
        
        /* --- CSS BARU: UNTUK QR CODE MODAL --- */
        .qr-modal {
            display: none; /* Sembunyikan secara default */
            position: fixed;
            z-index: 1050; /* Di atas elemen lain */
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            -webkit-backdrop-filter: blur(5px);
            backdrop-filter: blur(5px);
        }
        .qr-modal-content {
            background-color: #fff;
            margin: 15% auto;
            padding: 25px;
            border-radius: 12px;
            width: 90%;
            max-width: 320px;
            text-align: center;
            position: relative;
            box-shadow: 0 5px 25px rgba(0,0,0,0.2);
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        .qr-modal-close {
            color: #aaa;
            position: absolute;
            top: 10px;
            right: 15px;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }
        .qr-modal-close:hover { color: #333; }
        #qrCodeContainer img {
            width: 100%;
            max-width: 250px;
            height: auto;
            margin: 10px 0 15px 0;
            border: 1px solid #eee;
            border-radius: 8px;
        }
        .qr-modal-content h2 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .qr-modal-content p {
            font-size: 14px;
            color: #666;
            margin-bottom: 20px;
        }
        
        .article-content img {
    max-width: 100%; /* Membuat gambar responsif */
    height: auto;
    border-radius: 8px; /* Memberi sudut tumpul */
    margin: 30px 0; /* Memberi jarak atas dan bawah */
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    display: block; /* Memastikan gambar berada di barisnya sendiri */
    margin-left: auto;
    margin-right: auto;
}


/* --- CSS BARU UNTUK CTA TULIS BERITA --- */
.cta-tulis-berita .card {
    background-color: #f8f9fa; /* Warna latar sedikit abu-abu */
    border: 1px solid #e9ecef;
    transition: all 0.3s ease;
}

.cta-tulis-berita .card:hover {
    box-shadow: 0 0.5rem 1.5rem rgba(0,0,0,0.1);
    transform: translateY(-5px);
}

.cta-tulis-berita .btn-success {
    background-color: var(--primary-color); /* Menggunakan warna utama situs */
    border-color: var(--primary-color);
}

.cta-tulis-berita .btn-success:hover {
    background-color: var(--primary-hover); /* Menggunakan warna hover utama situs */
    border-color: var(--primary-hover);
}

.cta-tulis-berita .text-success {
    color: var(--primary-color) !important; /* Pastikan ikon juga menggunakan warna utama */
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

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="https://mtsn1waykanan.com/">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo Kemenag">
                <img src="https://mtsn1waykanan.com/img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/">Beranda</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Profil
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item" href="#">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com#galeri">Galeri</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="https://mtsn1waykanan.com/berita.php">Berita</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com/layanan.php">Layanan</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            Lainnya
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="https://mtsn1waykanan.com/tata-tertib.php">Tata Tertib Madrasah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://mtsn1waykanan.com#kontak">Kontak</a>
                    </li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0">
                        <a class="btn btn-ppdb" href="https://mtsn1waykanan.com/ppdb">PPDB Online</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    
    <div class="container-berita">
        <article>
            <header class="article-header">
                <img src="admin/uploads/berita/<?php echo htmlspecialchars($berita['gambar_utama']); ?>" alt="<?php echo htmlspecialchars($berita['judul']); ?>" class="main-image">
                <h1><?php echo htmlspecialchars($berita['judul']); ?></h1>
                <div class="article-meta">
                    <span><i class="fas fa-user"></i> <?php echo htmlspecialchars($berita['penulis']); ?></span>
                    <span><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y', strtotime($berita['tanggal_publikasi'])); ?></span>
                    <span><i class="fas fa-tag"></i> <?php echo htmlspecialchars($berita['kategori']); ?></span>
                    <span><i class="fas fa-eye"></i> Dibaca <?php echo htmlspecialchars($berita['jumlah_pembaca']); ?> kali</span>
            </div>
            </header>

            <div class="article-actions">
                <a href="berita.php" class="btn-action"><i class="fas fa-arrow-left"></i> Kembali ke Daftar Berita</a>
                <a href="cetak_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>" target="_blank" class="btn-action"><i class="fas fa-print"></i> Cetak/Simpan PDF</a>
                <div class="share-buttons">
                    <span>Bagikan:</span>
                    <a href="https://api.whatsapp.com/send?text=<?php echo urlencode($berita['judul'] . ' - ' . 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>" target="_blank" class="btn-action btn-whatsapp" title="Bagikan ke WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <button onclick="copyLink()" class="btn-action btn-copy" title="Salin Link"><i class="fas fa-link"></i></button>
                    <button onclick="showQrModal()" class="btn-action btn-qr" title="Bagikan via QR Code"><i class="fas fa-qrcode"></i></button>
                </div>
            </div>

            <section class="article-content">
                <?php echo $isi_dengan_gambar; ?>
            </section>
        </article>


        <section class="cta-tulis-berita my-5">
            <div class="card text-center p-4">
                <div class="card-body">
                    <!--<i class="fas fa-pen-fancy fa-3x mb-3 text-success"></i>-->
                    <h3 class="card-title fw-bold">Punya Informasi atau Berita Menarik?</h3>
                    <p class="card-text text-muted">Bagikan tulisan Anda dan jadilah bagian dari kami. Kontribusi Anda sangat berarti untuk kemajuan MTsN 1 Way Kanan.</p>
                    <a href="https://mtsn1waykanan.com/kontributor" class="btn btn-success btn-lg fw-bold mt-3">
                        <i class="fas fa-feather-alt me-2"></i>Tulis Berita Sekarang
                    </a>
                </div>
            </div>
        </section>

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
    
   

    <footer class="main-footer">
        <p class="mb-0">
            © 2025 MTs Negeri 1 Way Kanan. All Rights Reserved.<br>
            Versi 2.1.0 
            <!--| Develop by : <a href="https://risky.kemusukkidul.com/" target="_blank" rel="noopener noreferrer">Riskyy</a>-->
        </p>
    </footer>
    
     <!-- MODAL BARU UNTUK QR CODE -->
    <div id="qrModal" class="qr-modal">
        <div class="qr-modal-content">
            <span class="qr-modal-close" onclick="closeQrModal()">&times;</span>
            <h2>Bagikan dengan QR Code</h2>
            <div id="qrCodeContainer">
                <!-- Gambar QR akan dimuat di sini oleh JavaScript -->
            </div>
            <p>Silahkan Scan atau Download QR ini.</p>
            <a id="downloadQrLink" class="btn-action">
                <i class="fas fa-download"></i> Unduh QR Code
            </a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyLink() {
            const currentUrl = window.location.href;
            navigator.clipboard.writeText(currentUrl).then(() => {
                Swal.fire({ title: 'Berhasil!', text: 'Link berita telah disalin ke clipboard.', icon: 'success', timer: 2000, showConfirmButton: false });
            }, () => {
                Swal.fire({ title: 'Gagal!', text: 'Tidak dapat menyalin link.', icon: 'error' });
            });
        }

        // --- JAVASCRIPT BARU UNTUK FUNGSI QR CODE ---

        // Fungsi untuk menampilkan modal
        function showQrModal() {
            const modal = document.getElementById('qrModal');
            const qrContainer = document.getElementById('qrCodeContainer');
            const downloadLink = document.getElementById('downloadQrLink');
            
            // Ambil slug dari PHP dan buat URL untuk QR
            const slug = "<?php echo htmlspecialchars($berita['slug'], ENT_QUOTES); ?>";
            const qrUrl = `generate_qr.php?slug=${slug}`;

            // Kosongkan kontainer dan buat elemen gambar baru
            qrContainer.innerHTML = ''; 
            const img = document.createElement('img');
            img.src = qrUrl;
            img.alt = 'QR Code Berita';
            qrContainer.appendChild(img);

            // Atur link download
            downloadLink.href = qrUrl;
            downloadLink.download = `qr-code-${slug}.png`;

            // Tampilkan modal
            modal.style.display = "block";
        }

        // Fungsi untuk menutup modal
        function closeQrModal() {
            document.getElementById('qrModal').style.display = "none";
        }

        // Event listener untuk menutup modal jika pengguna mengklik di luar area konten modal
        window.addEventListener('click', function(event) {
            const modal = document.getElementById('qrModal');
            if (event.target == modal) {
                closeQrModal();
            }
        });
    </script>
</body>
</html>