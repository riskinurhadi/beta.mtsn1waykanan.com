<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- AMBIL DATA PESAN DARI DATABASE ---
$pesan_list = [];
// Memilih kolom kunci untuk ditampilkan di tabel utama
$sql = "SELECT id, nama_pengirim, subjek, tanggal_kirim, status FROM pesan_kontak ORDER BY tanggal_kirim DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $pesan_list[] = $row;
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
    <title>Kelola Pesan Masuk - Admin MTsN 1 Way Kanan</title>
    
    <!-- Google Fonts, Font Awesome, SweetAlert2 -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--info-color:#3498db;--warning-color:#f39c12}
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
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
        .page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle;white-space:nowrap}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .data-table tbody tr.pesan-baru td { font-weight: 600; color: #000; }

        /* --- BADGE STATUS PESAN --- */
        .badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;color:#fff;text-transform:capitalize}
        .badge-baru{background-color:var(--info-color)}
        .badge-sudah-dibaca{background-color:#6c757d}
        .badge-sudah-dibalas{background-color:var(--primary-color)}

        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-view{background-color:var(--primary-color)}
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
            <!-- Menambahkan menu baru untuk Pesan Masuk -->
            <li class="active"><a href="kelola_kontak_pesan.php"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Pesan Masuk</h1>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Nama Pengirim</th>
                            <th>Subjek</th>
                            <th>Tanggal Kirim</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($pesan_list)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Tidak ada pesan masuk.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($pesan_list as $index => $pesan): ?>
                            <!-- Menambahkan class jika pesan masih baru -->
                            <tr class="<?php echo ($pesan['status'] == 'Baru') ? 'pesan-baru' : ''; ?>">
                                <td><?php echo $index + 1; ?></td>
                                <td><?php echo htmlspecialchars($pesan['nama_pengirim']); ?></td>
                                <td><?php echo htmlspecialchars($pesan['subjek']); ?></td>
                                <td><?php echo date('d M Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></td>
                                <td>
                                    <?php 
                                        $status = str_replace(' ', '-', strtolower($pesan['status']));
                                        echo "<span class='badge badge-{$status}'>{$pesan['status']}</span>";
                                    ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="detail_pesan.php?id=<?php echo $pesan['id']; ?>" class="btn-action btn-view" title="Lihat Detail Pesan"><i class="fas fa-eye"></i></a>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $pesan['id']; ?>)" title="Hapus Pesan"><i class="fas fa-trash"></i></button>
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
                text: "Pesan ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_pesan.php?id=' + id;
                }
            });
        }
    </script>
</body>
</html>
