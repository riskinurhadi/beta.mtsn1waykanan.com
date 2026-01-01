<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// Ambil ID dari URL dan pastikan itu adalah angka.
$guru_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($guru_id <= 0) {
    // Jika ID tidak valid atau tidak ada, arahkan ke halaman 404.
    header("Location: 404.php");
    exit();
}

// Siapkan query untuk mengambil data lengkap guru berdasarkan ID.
$sql = "SELECT * FROM data_guru WHERE id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $guru_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika data guru dengan ID tersebut tidak ditemukan, arahkan ke halaman 404.
    header("Location: 404.php");
    exit();
}

// Ambil data guru untuk ditampilkan.
$guru = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Profil: <?php echo htmlspecialchars($guru['nama_lengkap']); ?> - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        /* =============================================
   STYLE UNTUK HALAMAN DETAIL GURU
   ============================================= */

.detail-guru-card {
    border: none;
    border-radius: 15px;
    overflow: hidden; /* Penting untuk sudut gambar */
}

.detail-guru-img-container {
    background-color: #e9ecef;
    display: flex;
    align-items: center;
    justify-content: center;
}

.detail-guru-img-container img {
    width: 100%;
    height: 100%;
    max-height: 450px;
    object-fit: cover;
}

.detail-guru-name {
    font-weight: 700;
    color: #333;
    margin-bottom: 0.25rem;
}

.detail-guru-position {
    font-size: 1.1rem;
    color: #198754;
    font-weight: 500;
}

.detail-guru-table {
    font-size: 0.95rem;
}

.detail-guru-table th {
    color: #6c757d;
    font-weight: 500;
    padding-left: 0;
}

.detail-guru-table td {
    color: #212529;
    font-weight: 500;
}

    </style>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolled">
        <!-- ... (Salin kode navbar lengkap dari index.php) ... -->
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <p class="page-subtitle">Profil Tenaga Pendidik</p>
            <h1 class="page-title"><?php echo htmlspecialchars($guru['nama_lengkap']); ?></h1>
        </div>
    </header>

    <!-- Konten Detail Guru -->
    <main class="detail-page-section mt-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm detail-guru-card">
                        <div class="row g-0">
                            <div class="col-md-4 detail-guru-img-container">
                                <?php 
                                    $foto_detail = (!empty($guru['foto_guru'])) ? 'admin/uploads/guru/' . htmlspecialchars($guru['foto_guru']) : 'https://placehold.co/500x500/E0F2F1/198754?text=Foto';
                                ?>
                                <img src="<?php echo $foto_detail; ?>" alt="Foto <?php echo htmlspecialchars($guru['nama_lengkap']); ?>">
                            </div>
                            <div class="col-md-8">
                                <div class="card-body p-4">
                                    <h3 class="detail-guru-name"><?php echo htmlspecialchars($guru['nama_lengkap']); ?></h3>
                                    <p class="detail-guru-position"><?php echo htmlspecialchars($guru['jabatan']); ?></p>
                                    <hr>
                                    <table class="table table-borderless detail-guru-table">
                                        <tbody>
                                            <tr>
                                                <th style="width: 35%;">NIK</th>
                                                <td>: <?php echo htmlspecialchars($guru['nik']); ?></td>
                                            </tr>
                                            <tr>
                                                <th>NUPTK</th>
                                                <td>: <?php echo htmlspecialchars($guru['nuptk'] ?: '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>Tempat, Tanggal Lahir</th>
                                                <td>: <?php echo htmlspecialchars($guru['tempat_lahir'] ?: '-'); ?>, <?php echo !empty($guru['tanggal_lahir']) ? date('d F Y', strtotime($guru['tanggal_lahir'])) : '-'; ?></td>
                                            </tr>
                                            <tr>
                                                <th>Agama</th>
                                                <td>: <?php echo htmlspecialchars($guru['agama'] ?: '-'); ?></td>
                                            </tr>
                                            <tr>
                                                <th>No. Handphone</th>
                                                <td>: <?php echo htmlspecialchars($guru['no_hp'] ?: '-'); ?></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                     <div class="text-center mt-4 mb-5">
                        <a href="guru.php" class="btn btn-outline-secondary">← Kembali ke Daftar Guru</a>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    $stmt->close();
    $koneksi->close();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
