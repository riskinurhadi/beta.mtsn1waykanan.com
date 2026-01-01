<?php
// Memanggil file konfigurasi dari direktori utama
require_once '../admin/config.php';

// --- SECURITY CHECK ---
// Pastikan hanya kontributor yang sudah login yang bisa mengakses
if (!isset($_SESSION['kontributor_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data kontributor dari session
$kontributor_id = $_SESSION['kontributor_id'];
$kontributor_nama = $_SESSION['kontributor_nama'];
$kontributor_api_key = $_SESSION['kontributor_api_key'];

// --- Ambil riwayat berita yang ditulis oleh kontributor ini ---
$berita_list = [];
// PERBAIKAN: Menambahkan kolom id_kontributor pada query
$sql = "SELECT judul, kategori, tanggal_publikasi, slug FROM berita WHERE id_kontributor = ? ORDER BY tanggal_publikasi DESC";

if ($stmt = $conn->prepare($sql)) {
    $stmt->bind_param("i", $kontributor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $berita_list[] = $row;
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Kontributor</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --sidebar-active: #34495e;
            --main-bg: #f4f7f6;
            --text-color: #333;
            --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        }
        body { 
            font-family: 'Poppins', sans-serif; 
            background-color: var(--main-bg); 
        }
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            z-index: 1000;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid var(--sidebar-active);
        }
        .sidebar-header h5 {
            margin: 0;
            font-weight: 600;
        }
        .sidebar-nav {
            list-style: none;
            padding: 20px 0;
        }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 15px;
            transition: background-color 0.2s ease;
        }
        .sidebar-nav li a i {
            width: 30px;
            font-size: 18px;
            margin-right: 10px;
        }
        .sidebar-nav li a:hover, .sidebar-nav li.active a {
            background-color: var(--sidebar-active);
        }
        .main-content {
            margin-left: 260px;
            padding: 20px;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
            margin-bottom: 30px;
        }
        .header h1 {
            font-size: 24px;
            font-weight: 600;
            margin: 0;
        }
        .card-custom { 
            border: none; 
            border-radius: 12px; 
            box-shadow: var(--card-shadow); 
        }
        .api-key-display {
            font-family: monospace;
            background-color: #e9ecef;
            padding: 0.75rem 1.25rem;
            border-radius: 8px;
            word-break: break-all;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h5>Portal Kontributor</h5>
        </div>
        <ul class="sidebar-nav">
            <li class="active"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="tulis_berita.php"><i class="fas fa-plus-circle"></i><span>Tulis Berita</span></a></li>
            <li><a href="akun_kontributor.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Dashboard</h1>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3">
                    Halo, <?php echo htmlspecialchars($kontributor_nama); ?>
                </span>
            </div>
        </header>

        <div class="container-fluid p-0">
            <div class="row g-4">
                <!-- Kolom Kiri: Info & Aksi -->
                <div class="col-lg-4">
                    <div class="card card-custom mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold">Kode Izin (API Key) Anda</h5>
                            <p class="card-text text-muted small">Gunakan kode ini di aplikasi Anda untuk mengirim berita. Jangan bagikan kode ini kepada siapapun.</p>
                            <div class="api-key-display mb-3" id="apiKeyText">
                                <?php echo htmlspecialchars($kontributor_api_key); ?>
                            </div>
                            <button class="btn btn-sm btn-outline-success w-100" onclick="copyApiKey()">
                                <i class="fas fa-copy me-2"></i>Salin Kode Izin
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Kolom Kanan: Riwayat Berita -->
                <div class="col-lg-8">
                    <div class="card card-custom">
                        <div class="card-body p-4">
                            <h5 class="card-title fw-bold mb-3">Riwayat Berita Anda</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Tanggal Publikasi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($berita_list)): ?>
                                            <tr>
                                                <td colspan="3" class="text-center text-muted">Anda belum pernah mengirim berita.</td>
                                            </tr>
                                        <?php else: ?>
                                            <?php foreach ($berita_list as $berita): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($berita['judul']); ?></td>
                                                <td><?php echo htmlspecialchars($berita['kategori']); ?></td>
                                                <td><?php echo date('d M Y', strtotime($berita['tanggal_publikasi'])); ?></td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function copyApiKey() {
            const apiKey = document.getElementById('apiKeyText').innerText;
            navigator.clipboard.writeText(apiKey).then(() => {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'Kode Izin disalin!',
                    showConfirmButton: false,
                    timer: 2000
                });
            });
        }
    </script>
</body>
</html>
