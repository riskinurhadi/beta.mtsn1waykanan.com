<?php
require 'koneksi.php';

// Mendapatkan query pencarian dari URL dan membersihkannya
$search_query = isset($_GET['q']) ? trim($_GET['q']) : '';

$hasil = [
    'berita' => [],
    'pejabat' => [],
    'galeri' => [],
    'halaman' => []
];
$total_hasil = 0;

if (!empty($search_query)) {
    $search_param = "%" . $search_query . "%";

    // 1. Mencari di tb_berita (judul atau isi)
    $stmt_berita = $koneksi->prepare("SELECT judul, slug, isi, gambar, tanggal_publish FROM tb_berita WHERE judul LIKE ? OR isi LIKE ?");
    $stmt_berita->bind_param("ss", $search_param, $search_param);
    $stmt_berita->execute();
    $result_berita = $stmt_berita->get_result();
    while ($row = $result_berita->fetch_assoc()) {
        $hasil['berita'][] = $row;
    }
    $total_hasil += count($hasil['berita']);
    $stmt_berita->close();

    // 2. Mencari di tb_pejabat (nama atau jabatan)
    $stmt_pejabat = $koneksi->prepare("SELECT nama_pejabat, jabatan, foto FROM tb_pejabat WHERE nama_pejabat LIKE ? OR jabatan LIKE ?");
    $stmt_pejabat->bind_param("ss", $search_param, $search_param);
    $stmt_pejabat->execute();
    $result_pejabat = $stmt_pejabat->get_result();
    while ($row = $result_pejabat->fetch_assoc()) {
        $hasil['pejabat'][] = $row;
    }
    $total_hasil += count($hasil['pejabat']);
    $stmt_pejabat->close();

    // 3. Mencari di tb_galeri (judul atau keterangan)
    $stmt_galeri = $koneksi->prepare("SELECT judul_media, file_media, jenis_media FROM tb_galeri WHERE judul_media LIKE ? OR keterangan LIKE ?");
    $stmt_galeri->bind_param("ss", $search_param, $search_param);
    $stmt_galeri->execute();
    $result_galeri = $stmt_galeri->get_result();
    while ($row = $result_galeri->fetch_assoc()) {
        $hasil['galeri'][] = $row;
    }
    $total_hasil += count($hasil['galeri']);
    $stmt_galeri->close();

    // 4. Mencari Halaman Statis (berdasarkan kata kunci)
    $keyword_map = [
        'lokasi' => ['nama' => 'Peta Lokasi Penting', 'url' => 'lokasi_penting.php', 'icon' => 'bi-map-fill'],
        'layanan' => ['nama' => 'Layanan Online', 'url' => 'index.php#layanan', 'icon' => 'bi-grid-1x2-fill'],
        'profil' => ['nama' => 'Profil Pemerintahan', 'url' => 'profil_pemerintahan.php', 'icon' => 'bi-bank'],
        'visi' => ['nama' => 'Visi & Misi', 'url' => 'profil_pemerintahan.php#visimisi', 'icon' => 'bi-eye-fill'],
        'misi' => ['nama' => 'Visi & Misi', 'url' => 'profil_pemerintahan.php#visimisi', 'icon' => 'bi-rocket-takeoff-fill'],
        'kontak' => ['nama' => 'Kontak Desa', 'url' => 'index.php#kontak', 'icon' => 'bi-telephone-fill'],
        'aduan' => ['nama' => 'Lapor / Aduan Masyarakat', 'url' => 'lapor.php', 'icon' => 'bi-megaphone-fill'],
        'lapor' => ['nama' => 'Lapor / Aduan Masyarakat', 'url' => 'lapor.php', 'icon' => 'bi-megaphone-fill']
    ];
    foreach ($keyword_map as $keyword => $page) {
        if (strpos(strtolower($search_query), $keyword) !== false) {
            $hasil['halaman'][$page['url']] = $page; // Gunakan URL sebagai key untuk hindari duplikat
        }
    }
    $total_hasil += count($hasil['halaman']);
}

// Fungsi untuk membuat ringkasan
function buat_ringkasan($konten, $panjang = 150) {
    $teks_polos = strip_tags($konten);
    if (mb_strlen($teks_polos) > $panjang) {
        $potong_teks = mb_substr($teks_polos, 0, $panjang);
        return $potong_teks . '...';
    }
    return $teks_polos;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian untuk "<?php echo htmlspecialchars($search_query); ?>"</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root { --primary-color: #0d47a1; --dark-text: #333; --light-gray: #f8f9fa; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); }
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
        .search-header { padding: 3rem 0; background-color: #fff; border-bottom: 1px solid #dee2e6; }
        .search-results-section { padding: 4rem 0; }
        .result-card {
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 1.5rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .result-card:hover {
             transform: translateY(-5px);
             box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .result-icon { flex-shrink: 0; width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 1.5rem; background-color: var(--light-gray); }
        .result-icon img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; }
        .result-icon i { font-size: 1.8rem; color: var(--primary-color); }
        .result-content h5 { font-weight: 600; margin-bottom: 0.25rem; }
        .result-content h5 a { text-decoration: none; color: var(--dark-text); }
        .result-content p { color: #555; margin-bottom: 0; font-size: 0.9rem; }
        .result-category { font-size: 0.8rem; font-weight: 600; color: var(--primary-color); text-transform: uppercase; letter-spacing: 0.5px; }
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

<main>
    <section class="search-header mt-5">
        <div class="container">
            <h1>Hasil Pencarian</h1>
            <p class="lead text-muted">
                <?php if (!empty($search_query)): ?>
                    Menampilkan <?php echo $total_hasil; ?> hasil untuk "<strong><?php echo htmlspecialchars($search_query); ?></strong>"
                <?php else: ?>
                    Silakan masukkan kata kunci pada form pencarian.
                <?php endif; ?>
            </p>
        </div>
    </section>

    <section class="search-results-section">
        <div class="container">
            <?php if (empty($search_query)): ?>
                <div class="text-center">
                    <a href="index.php" class="btn btn-primary">&laquo; Kembali ke Beranda</a>
                </div>
            <?php elseif ($total_hasil > 0): ?>
                
                <!-- Hasil Halaman Terkait -->
                <?php if (!empty($hasil['halaman'])): ?>
                    <h4 class="mb-4">Halaman Terkait</h4>
                    <?php foreach ($hasil['halaman'] as $h): ?>
                    <a href="<?php echo $h['url']; ?>" class="text-decoration-none">
                        <div class="result-card">
                            <div class="result-icon"><i class="bi <?php echo $h['icon']; ?>"></i></div>
                            <div class="result-content">
                                <h5><?php echo $h['nama']; ?></h5>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Hasil Berita -->
                <?php if (!empty($hasil['berita'])): ?>
                    <h4 class="mb-4 mt-5">Berita</h4>
                    <?php foreach ($hasil['berita'] as $b): ?>
                    <a href="detail_berita.php?slug=<?php echo $b['slug']; ?>" class="text-decoration-none">
                        <div class="result-card">
                            <div class="result-icon"><img src="admin/uploads/<?php echo $b['gambar']; ?>" alt="<?php echo htmlspecialchars($b['judul']); ?>"></div>
                            <div class="result-content">
                                <span class="result-category">Berita</span>
                                <h5><?php echo htmlspecialchars($b['judul']); ?></h5>
                                <p><?php echo htmlspecialchars(buat_ringkasan($b['isi'])); ?></p>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Hasil Pejabat -->
                <?php if (!empty($hasil['pejabat'])): ?>
                    <h4 class="mb-4 mt-5">Pejabat</h4>
                    <?php foreach ($hasil['pejabat'] as $p): ?>
                        <div class="result-card">
                            <div class="result-icon"><img src="admin/uploads/pejabat/<?php echo $p['foto']; ?>" alt="<?php echo htmlspecialchars($p['nama_pejabat']); ?>"></div>
                            <div class="result-content">
                                <span class="result-category">Aparatur Desa</span>
                                <h5><?php echo htmlspecialchars($p['nama_pejabat']); ?></h5>
                                <p><?php echo htmlspecialchars($p['jabatan']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>

                <!-- Hasil Galeri -->
                <?php if (!empty($hasil['galeri'])): ?>
                     <h4 class="mb-4 mt-5">Galeri</h4>
                    <?php foreach ($hasil['galeri'] as $g): ?>
                     <a href="semua_galeri.php" class="text-decoration-none">
                        <div class="result-card">
                           <div class="result-icon">
                                <?php if($g['jenis_media'] == 'foto'): ?>
                                    <img src="admin/uploads/galeri/<?php echo $g['file_media']; ?>" alt="<?php echo htmlspecialchars($g['judul_media']); ?>">
                                <?php else: ?>
                                    <i class="bi bi-camera-reels-fill"></i>
                                <?php endif; ?>
                           </div>
                           <div class="result-content">
                               <span class="result-category">Galeri</span>
                               <h5><?php echo htmlspecialchars($g['judul_media']); ?></h5>
                           </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>

            <?php else: ?>
                <div class="text-center">
                    <img src="https://via.placeholder.com/150/f8f9fa/6c757d?text=:(" alt="Tidak ditemukan" class="rounded-circle mb-4">
                    <h3>Oops! Hasil Tidak Ditemukan</h3>
                    <p class="text-muted">Kami tidak dapat menemukan apa yang Anda cari. Coba gunakan kata kunci lain.</p>
                    <a href="index.php" class="btn btn-primary mt-3">&laquo; Kembali ke Beranda</a>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center p-4">
    <p class="mb-0">&copy; <?php echo date('Y'); ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>