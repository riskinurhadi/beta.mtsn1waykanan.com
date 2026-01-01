<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: ../login.php");
    exit();
}
// Hanya Super Admin dan Developer yang bisa mengakses
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    die("Akses ditolak.");
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'kontributor';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

// --- AMBIL DATA KONTRIBUTOR DARI DATABASE ---
$kontributor_list = [];
$sql = "SELECT id, nama_lengkap, email, instansi, status_akun, tanggal_registrasi FROM kontributor ORDER BY tanggal_registrasi DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $kontributor_list[] = $row;
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
    <title>Kelola Kontributor - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--warning-color:#f39c12;--secondary-color:#6c757d;--info-color:#3498db}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}
        .sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}
        .sidebar-header h3{font-weight:600;color:#fff}
        .sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}
        .sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}
        .sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}
        .sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}
        .sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}
        .main-content{margin-left:260px;width:calc(100% - 260px);padding:20px;transition:all .3s ease}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;flex-wrap:wrap;gap:15px}
        .page-header h1{font-size:24px;font-weight:600}
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;color:#fff;text-transform:capitalize}
        .badge-pending{background-color:var(--warning-color)}
        .badge-aktif{background-color:var(--primary-color)}
        .badge-ditolak{background-color:var(--danger-color)}
        .badge-diblokir{background-color:#343a40}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-approve{background-color:var(--primary-color)}
        .btn-reject{background-color:var(--danger-color)}
        .btn-block{background-color:#343a40}
        .btn-delete{background-color:var(--danger-color)}
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <!-- ... (Menu sidebar Anda) ... -->
            <!-- Menambahkan menu baru untuk Kontributor -->
            <li class="active"><a href="kelola_kontributor.php"><i class="fas fa-users-cog"></i><span>Kontributor</span></a></li>
            <li class="sidebar-dropdown">
                <!-- ... (Dropdown Anda) ... -->
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Kontributor Berita</h1>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Instansi</th>
                            <th>Tgl. Daftar</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($kontributor_list)): ?>
                            <tr>
                                <td colspan="6" style="text-align: center;">Belum ada kontributor yang mendaftar.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($kontributor_list as $kontributor): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($kontributor['nama_lengkap']); ?></strong></td>
                                <td><?php echo htmlspecialchars($kontributor['email']); ?></td>
                                <td><?php echo htmlspecialchars($kontributor['instansi']); ?></td>
                                <td><?php echo date('d M Y', strtotime($kontributor['tanggal_registrasi'])); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo str_replace(' ', '-', strtolower($kontributor['status_akun'])); ?>">
                                        <?php echo htmlspecialchars($kontributor['status_akun']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <?php if ($kontributor['status_akun'] == 'pending'): ?>
                                            <a href="aksi_kontributor.php?id=<?php echo $kontributor['id']; ?>&aksi=setujui" class="btn-action btn-approve" title="Setujui"><i class="fas fa-check"></i></a>
                                            <a href="aksi_kontributor.php?id=<?php echo $kontributor['id']; ?>&aksi=tolak" class="btn-action btn-reject" title="Tolak"><i class="fas fa-times"></i></a>
                                        <?php elseif ($kontributor['status_akun'] == 'aktif'): ?>
                                            <a href="aksi_kontributor.php?id=<?php echo $kontributor['id']; ?>&aksi=blokir" class="btn-action btn-block" title="Blokir"><i class="fas fa-ban"></i></a>
                                        <?php endif; ?>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $kontributor['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
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
                text: "Data kontributor ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: 'Ya, Hapus!'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'aksi_kontributor.php?id=' + id + '&aksi=hapus';
                }
            });
        }
    </script>
</body>
</html>
