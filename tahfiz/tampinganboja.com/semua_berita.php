<?php
require 'koneksi.php';

// --- FUNGSI UNTUK MEMBUAT RINGKASAN BERITA ---
function buat_ringkasan($konten, $panjang = 150) {
    $teks_polos = strip_tags($konten); // Hapus tag HTML
    if (strlen($teks_polos) > $panjang) {
        $potong_teks = substr($teks_polos, 0, $panjang);
        // Pastikan tidak memotong di tengah kata
        $akhir_spasi = strrpos($potong_teks, ' ');
        $ringkasan = $akhir_spasi ? substr($potong_teks, 0, $akhir_spasi) : $potong_teks;
        return $ringkasan . '...';
    }
    return $teks_polos;
}

// --- LOGIKA PAGINATION ---
$berita_per_halaman = 6; // Jumlah berita yang ditampilkan per halaman
$halaman_saat_ini = isset($_GET['halaman']) ? (int)$_GET['halaman'] : 1;
if ($halaman_saat_ini < 1) {
    $halaman_saat_ini = 1;
}

// Hitung total berita untuk menentukan jumlah halaman
$total_berita_result = $koneksi->query("SELECT COUNT(id_berita) as total FROM tb_berita");
$total_berita = $total_berita_result->fetch_assoc()['total'];
$total_halaman = ceil($total_berita / $berita_per_halaman);

// Hitung offset untuk query database
$offset = ($halaman_saat_ini - 1) * $berita_per_halaman;

// Ambil data berita untuk halaman saat ini
$query_berita = "SELECT b.judul, b.slug, b.gambar, b.isi, b.tanggal_publish, a.nama_lengkap 
                 FROM tb_berita b 
                 JOIN tb_admin a ON b.id_admin = a.id_admin 
                 ORDER BY b.tanggal_publish DESC 
                 LIMIT ? OFFSET ?";
$stmt = $koneksi->prepare($query_berita);
$stmt->bind_param("ii", $berita_per_halaman, $offset);
$stmt->execute();
$result_berita = $stmt->get_result();
$stmt->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Arsip Berita - Desa Tampingan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #ff9800;
            --light-gray: #f8f9fa;
            --dark-text: #333;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); color: var(--dark-text); }
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; }
        
        .page-header {
            padding: 3rem 0;
            background-color: white;
            text-align: center;
            border-bottom: 1px solid #dee2e6;
        }
        .page-header h1 {
            font-weight: 700;
            color: var(--primary-color);
        }

        .berita-card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            height: 100%;
        }
        .berita-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        }
        .berita-card-img {
            width: 100%;
            aspect-ratio: 16 / 10;
            object-fit: cover;
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
        }
        .berita-card-body {
            padding: 20px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        .berita-card-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--dark-text);
            text-decoration: none;
        }
        .berita-card-title:hover {
            color: var(--primary-color);
        }
        .berita-card-meta {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 10px;
        }
        .berita-card-text {
            font-size: 0.9rem;
            flex-grow: 1;
        }
        .berita-card .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            font-weight: 500;
        }
        
        .pagination .page-item.active .page-link {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        .pagination .page-link {
            color: var(--primary-color);
        }

        .footer { background-color: #2c3e50; color: white; padding: 3rem 0; }
        .footer a { color: #bdc3c7; }
    </style>
</head>
<body>

    <header>
        <nav class="navbar navbar-expand-lg navbar-dark">
            <div class="container">
                <a class="navbar-brand" href="index.php">
                    <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                    <div class="logo-text">
                        <span class="title">Pemerintah Kabupaten Kendal</span>
                        <span class="subtitle">Desa Tampingan</span>
                    </div>
                </a>
                <a href="index.php" class="btn btn-outline-light d-none d-lg-inline-block">Kembali ke Beranda</a>
            </div>
        </nav>
    </header>

    <main>
        <section class="page-header">
            <div class="container">
                <h1>Arsip Berita</h1>
                <p class="text-muted">Jelajahi semua berita dan informasi terkini dari Desa Tampingan.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="row g-4">
                    <?php if ($result_berita->num_rows > 0): ?>
                        <?php while ($berita = $result_berita->fetch_assoc()): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="berita-card">
                                    <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>">
                                        <img src="admin/uploads/<?php echo htmlspecialchars($berita['gambar']); ?>" class="berita-card-img" alt="Gambar Berita">
                                    </a>
                                    <div class="berita-card-body">
                                        <div class="berita-card-meta">
                                            <i class="bi bi-calendar-event"></i> <?php echo date('d M Y', strtotime($berita['tanggal_publish'])); ?>
                                        </div>
                                        <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>" class="berita-card-title">
                                            <?php echo htmlspecialchars($berita['judul']); ?>
                                        </a>
                                        <p class="berita-card-text mt-2">
                                            <?php echo htmlspecialchars(buat_ringkasan($berita['isi'])); ?>
                                        </p>
                                        <div class="mt-auto">
                                            <a href="detail_berita.php?slug=<?php echo htmlspecialchars($berita['slug']); ?>" class="btn btn-primary btn-sm">Selengkapnya <i class="bi bi-arrow-right-short"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center fs-5 text-muted">Belum ada berita untuk ditampilkan.</p>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Navigasi Pagination -->
                <?php if ($total_halaman > 1): ?>
                <nav aria-label="Navigasi Halaman Berita" class="mt-5">
                    <ul class="pagination justify-content-center">
                        <!-- Tombol Previous -->
                        <li class="page-item <?php echo ($halaman_saat_ini <= 1) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?halaman=<?php echo $halaman_saat_ini - 1; ?>">Sebelumnya</a>
                        </li>
                        
                        <!-- Tombol Halaman -->
                        <?php for ($i = 1; $i <= $total_halaman; $i++): ?>
                            <li class="page-item <?php echo ($i == $halaman_saat_ini) ? 'active' : ''; ?>">
                                <a class="page-link" href="?halaman=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>

                        <!-- Tombol Next -->
                        <li class="page-item <?php echo ($halaman_saat_ini >= $total_halaman) ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?halaman=<?php echo $halaman_saat_ini + 1; ?>">Selanjutnya</a>
                        </li>
                    </ul>
                </nav>
                <?php endif; ?>

            </div>
        </section>
    </main>

    <footer class="footer">
        <div class="container text-center">
            <p>&copy; <?php echo date("Y"); ?> Hak Cipta Dilindungi | Desa Tampingan, Kecamatan Boja, Kabupaten Kendal</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
