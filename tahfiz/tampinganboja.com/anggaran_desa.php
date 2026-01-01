<?php
// Memanggil file koneksi untuk mengakses database
require_once 'koneksi.php';

// Mengambil data ringkasan anggaran dari tahun ke tahun
$query = "
    SELECT 
        a.id_anggaran, 
        a.tahun,
        COALESCE(pendapatan.total_realisasi_pendapatan, 0) as total_pendapatan,
        COALESCE(belanja.total_anggaran_belanja, 0) as total_anggaran,
        COALESCE(belanja.total_realisasi_belanja, 0) as total_realisasi
    FROM 
        tb_anggaran a
    LEFT JOIN (
        SELECT id_anggaran, SUM(jumlah_realisasi) as total_realisasi_pendapatan 
        FROM tb_anggaran_rincian WHERE tipe = 'Pendapatan' GROUP BY id_anggaran
    ) pendapatan ON a.id_anggaran = pendapatan.id_anggaran
    LEFT JOIN (
        SELECT id_anggaran, SUM(jumlah_anggaran) as total_anggaran_belanja, SUM(jumlah_realisasi) as total_realisasi_belanja 
        FROM tb_anggaran_rincian WHERE tipe = 'Belanja' GROUP BY id_anggaran
    ) belanja ON a.id_anggaran = belanja.id_anggaran
    ORDER BY a.tahun DESC
";
$result_anggaran = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transparansi Anggaran - Desa Tampingan</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #0d47a1;
            --light-gray: #f8f9fa;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); }

        /* Navbar Styling */
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; padding: 0.8rem 1rem; border-radius: 5px; transition: background-color 0.3s; }

        /* Page Header */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/img/fotobersama.png') no-repeat center center;
            background-size: cover;
            padding: 8rem 0;
            color: white;
            text-align: center;
        }
        .page-header h1 { font-weight: 700; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }

        /* Content Styling */
        .content-section { padding: 4rem 0; }
        .content-card {
            background-color: #fff;
            border-radius: 12px;
            padding: 2rem;
            box-shadow: 0 5px 25px rgba(0,0,0,0.07);
        }
        .table thead th {
            font-weight: 600;
            background-color: var(--light-gray);
        }
        .table tbody tr:hover {
            background-color: #f8f9fa;
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                <div class="logo-text">
                    <span class="title">Pemerintah Kabupaten Kendal</span>
                    <span class="subtitle">Desa Tampingan</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavProfil" aria-controls="navbarNavProfil" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavProfil">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="index.php">
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
            <h1 class="display-5">Transparansi Anggaran Desa</h1>
            <p class="lead">Ringkasan Anggaran Pendapatan dan Belanja Desa (APBDes) Tampingan dari tahun ke tahun.</p>
        </div>
    </section>

    <section class="content-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="content-card">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th scope="col">Tahun Anggaran</th>
                                        <th scope="col" class="text-end">Total Pendapatan</th>
                                        <th scope="col" class="text-end">Total Anggaran Belanja</th>
                                        <th scope="col" class="text-end">Total Realisasi Belanja</th>
                                        <th scope="col" class="text-center">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($result_anggaran && $result_anggaran->num_rows > 0): ?>
                                    <?php while($anggaran = $result_anggaran->fetch_assoc()): ?>
                                    <tr>
                                        <td class="fw-bold fs-5"><?php echo htmlspecialchars($anggaran['tahun']); ?></td>
                                        <td class="text-end text-success fw-bold"><?php echo "Rp " . number_format($anggaran['total_pendapatan'], 0, ',', '.'); ?></td>
                                        <td class="text-end"><?php echo "Rp " . number_format($anggaran['total_anggaran'], 0, ',', '.'); ?></td>
                                        <td class="text-end text-danger fw-bold"><?php echo "Rp " . number_format($anggaran['total_realisasi'], 0, ',', '.'); ?></td>
                                        <td class="text-center">
                                            <a href="anggaran_desa_rincian.php?id=<?php echo $anggaran['id_anggaran']; ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye-fill me-1"></i> Lihat Rincian
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                    <?php else: ?>
                                    <tr>
                                        <td colspan="5">
                                            <div class="alert alert-info text-center mb-0">
                                                Belum ada data anggaran yang dipublikasikan.
                                            </div>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center p-4 mt-4">
    <div class="container">
        <p class="mb-0">&copy; <?php echo date("Y"); ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>

