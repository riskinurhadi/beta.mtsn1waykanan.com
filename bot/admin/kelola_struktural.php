<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- AMBIL DATA STRUKTUR ORGANISASI DARI DATABASE ---
$struktur_list = [];
// Query diurutkan berdasarkan kolom 'urutan' agar tampil sesuai hierarki
$sql = "SELECT id, nama_lengkap, jabatan, foto_url, kategori_jabatan, urutan 
        FROM struktur_organisasi 
        ORDER BY urutan ASC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $struktur_list[] = $row;
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
    <title>Kelola Struktur Organisasi - Admin</title>
    
    <!-- Google Fonts, Font Awesome, SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--warning-color:#f39c12}
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
        .struktur-thumb{width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid #eee}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-edit{background-color:var(--warning-color)}
        .btn-delete{background-color:var(--danger-color)}
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
            <li><a href="kelola_kontak_pesan.php"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <!-- Menambahkan menu baru untuk Struktur Organisasi -->
            <li class="active"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Struktur Organisasi</h1>
            <a href="tambah_struktural.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Anggota</a>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Urutan</th>
                            <th>Foto</th>
                            <th>Nama Lengkap</th>
                            <th>Jabatan</th>
                            <th>Kategori</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($struktur_list)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada data struktur organisasi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($struktur_list as $item): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($item['urutan']); ?></strong></td>
                                <td>
                                    <!-- Asumsi gambar disimpan di folder uploads/struktur/ -->
                                    <img src="uploads/struktur/<?php echo htmlspecialchars($item['foto_url'] ?? 'default-profile.png'); ?>" alt="Foto <?php echo htmlspecialchars($item['nama_lengkap']); ?>" class="struktur-thumb">
                                </td>
                                <td><?php echo htmlspecialchars($item['nama_lengkap']); ?></td>
                                <td><?php echo htmlspecialchars($item['jabatan']); ?></td>
                                <td><?php echo htmlspecialchars($item['kategori_jabatan']); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="edit_struktural.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $item['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
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
                text: "Data anggota ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_struktural.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>
