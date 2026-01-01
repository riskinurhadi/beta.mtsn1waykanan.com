<?php
require 'koneksi.php';

// Path ke folder upload foto pejabat di dalam folder admin
$upload_dir = 'admin/uploads/pejabat/';

// Query untuk mengambil semua data pejabat, diurutkan berdasarkan kolom 'urutan' lalu 'nama'
$query_pejabat = "SELECT nama_pejabat, jabatan, foto FROM tb_pejabat ORDER BY urutan ASC, nama_pejabat ASC";
$result_pejabat = $koneksi->query($query_pejabat);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - Desa Tampingan</title>
    
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

        .pejabat-card {
            position: relative;
            border-radius: 15px;
            overflow: hidden;
            background-color: white;
            box-shadow: 0 8px 25px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .pejabat-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }
        .pejabat-card img {
            width: 100%;
            display: block;
            aspect-ratio: 4/5;
            object-fit: cover;
        }
        .pejabat-info {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(to top, rgba(0,0,0,0.85) 0%, rgba(0,0,0,0) 100%);
            color: white;
            padding: 2.5rem 1.5rem 1.5rem 1.5rem;
            text-align: center;
        }
        .pejabat-info h5 {
            font-weight: 600;
            margin-bottom: 0.25rem;
            font-size: 1.1rem;
        }
        .pejabat-info p {
            margin: 0;
            font-size: 0.9rem;
            opacity: 0.8;
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
                <h1>Struktur Organisasi</h1>
                <p class="text-muted">Mengenal lebih dekat jajaran pimpinan di lingkungan Desa Tampingan.</p>
            </div>
        </section>

        <section class="py-5">
            <div class="container">
                <div class="row justify-content-center g-4">
                    <?php if ($result_pejabat && $result_pejabat->num_rows > 0): ?>
                        <?php while ($pejabat = $result_pejabat->fetch_assoc()): ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="pejabat-card">
                                    <img src="<?php echo $upload_dir . htmlspecialchars($pejabat['foto']); ?>" alt="Foto <?php echo htmlspecialchars($pejabat['nama_pejabat']); ?>">
                                    <div class="pejabat-info">
                                        <h5><?php echo htmlspecialchars($pejabat['nama_pejabat']); ?></h5>
                                        <p><?php echo htmlspecialchars($pejabat['jabatan']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <p class="text-center fs-5 text-muted">Data pimpinan belum tersedia saat ini.</p>
                        </div>
                    <?php endif; ?>
                </div>
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
