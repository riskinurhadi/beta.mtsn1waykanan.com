<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- AMBIL DATA BERITA DARI DATABASE ---
$berita_list = [];
// Query disesuaikan dengan struktur tabel Anda, mengambil kolom yang relevan untuk ditampilkan
$sql = "SELECT id, judul, kategori, penulis, gambar_utama, tanggal_publikasi, slug FROM berita ORDER BY tanggal_publikasi DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $berita_list[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita - Admin MTsN 1 Way Kanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--danger-hover:#c0392b;--info-color:#3498db;--warning-color:#f39c12;--success-color:#2ecc71}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease}
        .sidebar-header{padding:20px;text-align:center;border-bottom:1px solid #34495e}
        .sidebar-header h3{font-weight:600}
        .sidebar-nav{flex-grow:1;list-style:none;padding-top:20px}
        .sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}
        .sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}
        .sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}
        .main-content{margin-left:260px;width:calc(100% - 260px);padding:20px;transition:all .3s ease}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;flex-wrap:wrap;gap:15px}
        .page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}
        .btn-add{background-color:var(--primary-color);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;transition:background-color .3s ease;display:inline-flex;align-items:center}
        .btn-add:hover{background-color:var(--primary-hover)}
        .btn-add i{margin-right:8px}
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .berita-thumb{width:100px;height:60px;border-radius:8px;object-fit:cover;border:2px solid #eee}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-view { background-color: var(--success-color); }
        .btn-edit { background-color: var(--warning-color); }
        .btn-delete { background-color: var(--danger-color); }
        @media (max-width:992px){.sidebar{width:70px}.sidebar-header h3,.sidebar-nav li a span{display:none}.main-content{margin-left:70px;width:calc(100% - 70px)}}
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>
            <li class="active"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Berita</h1>
            <a href="tambah_berita.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Berita Baru</a>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Tanggal Publikasi</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($berita_list)): ?>
                            <tr>
                                <td colspan="7" style="text-align: center;">Belum ada data berita.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($berita_list as $index => $berita): ?>
                            <tr>
                                <td><?php echo $index + 1; ?></td>
                                <td>
                                    <img src="uploads/berita/<?php echo htmlspecialchars($berita['gambar_utama'] ?? 'default-berita.png'); ?>" alt="Gambar Utama" class="berita-thumb">
                                </td>
                                <td><?php echo htmlspecialchars(substr($berita['judul'], 0, 50)); ?><?php echo strlen($berita['judul']) > 50 ? '...' : ''; ?></td>
                                <td><?php echo htmlspecialchars($berita['kategori']); ?></td>
                                <td><?php echo htmlspecialchars($berita['penulis']); ?></td>
                                <td><?php echo date('d M Y', strtotime($berita['tanggal_publikasi'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="../detail_berita.php?slug=<?php echo $berita['slug']; ?>" class="btn-action btn-view" target="_blank" title="Lihat Berita"><i class="fas fa-eye"></i></a>
                                        <a href="edit_berita.php?id=<?php echo $berita['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $berita['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?> 
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Berita ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_berita.php?id=' + id;
                }
            });
        }

        <?php
        // Script untuk menampilkan notifikasi sukses/gagal dari session
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2500, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo "Swal.fire({ title: 'Gagal!', text: '" . addslashes($_SESSION['error_message']) . "', icon: 'error', confirmButtonColor: '#d33' });";
            unset($_SESSION['error_message']);
        }
        ?>
    </script>
</body>
</html>