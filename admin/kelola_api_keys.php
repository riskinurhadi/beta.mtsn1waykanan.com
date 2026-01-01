<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}
// Hanya Super Admin dan Developer yang bisa mengakses
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    http_response_code(403);
    // Tampilkan halaman akses ditolak
    echo <<<HTML
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Akses Ditolak</title><link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"><style>:root{--danger-color:#e74c3c;--main-bg:#f4f7f6;--text-color:#333}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;color:var(--text-color)}.access-denied-container{text-align:center;background-color:#fff;padding:40px 50px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1)}.icon-wrapper i{font-size:60px;color:var(--danger-color);margin-bottom:20px}h1{font-size:28px;font-weight:600;margin-bottom:10px}p{font-size:16px;color:#666;margin-bottom:30px}.btn-back{background-color:#6c757d;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px;transition:background-color .3s ease}.btn-back:hover{background-color:#5a6268}</style></head><body><div class="access-denied-container"><div class="icon-wrapper"><i class="fas fa-ban"></i></div><h1>Akses Ditolak</h1><p>Anda tidak memiliki izin untuk mengakses halaman ini.</p><a href="index.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a></div></body></html>
HTML;
    exit();
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'api_keys';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

// --- AMBIL DATA API KEYS DARI DATABASE ---
$api_keys_list = [];
$sql = "SELECT id, nama_pengguna, api_key, izin, status FROM api_keys ORDER BY id DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $api_keys_list[] = $row;
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
    <title>Kelola API Keys - Admin</title>
    
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
        .btn-add{background-color:var(--primary-color);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;transition:background-color .3s ease;display:inline-flex;align-items:center;gap:8px}
        .btn-add:hover{background-color:var(--primary-hover)}
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;color:#fff}
        .badge-aktif{background-color:var(--primary-color)}
        .badge-tidak-aktif{background-color:var(--secondary-color)}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-toggle{background-color:var(--info-color)}
        .btn-delete{background-color:var(--danger-color)}
        .btn-copy{background-color:var(--primary-color)} /* CSS Baru */
        .api-key-text{font-family:monospace;background-color:#f0f0f0;padding:2px 6px;border-radius:4px;color:#333;font-size:14px;}
        .sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}
        .sidebar-dropdown.open .dropdown-menu{display:block}
        .dropdown-menu li a{padding-left:65px}
        .dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}
        .sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}
        .notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
        @media (max-width:992px){.sidebar{width:70px}.sidebar-header h3,.sidebar-nav li a span,.dropdown-icon{display:none}.main-content{margin-left:70px;width:calc(100% - 70px)}.sidebar-dropdown .dropdown-menu{position:absolute;left:70px;top:0;background-color:var(--sidebar-bg);width:200px;box-shadow:5px 0 15px rgba(0,0,0,.2);border-left:1px solid var(--sidebar-active)}.dropdown-menu li a{padding-left:20px}.dropdown-menu li a span{display:inline-block!important}}
    </style>
</head>
<body>

    <aside class="sidebar">
        <!-- ... (konten sidebar Anda) ... -->
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola API Keys</h1>
            <a href="tambah_api_key.php" class="btn-add"><i class="fas fa-plus"></i> Tambah API Key Baru</a>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nama Pengguna</th>
                            <th>API Key</th>
                            <th>Izin</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($api_keys_list)): ?>
                            <tr>
                                <td colspan="5" style="text-align: center;">Belum ada API Key yang dibuat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($api_keys_list as $key): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($key['nama_pengguna']); ?></strong></td>
                                <td><span class="api-key-text"><?php echo htmlspecialchars(substr($key['api_key'], 0, 8)) . '...'; ?></span></td>
                                <td><?php echo htmlspecialchars($key['izin'] ?? 'Tidak ada'); ?></td>
                                <td>
                                    <?php if ($key['status'] == 'aktif'): ?>
                                        <span class="badge badge-aktif">Aktif</span>
                                    <?php else: ?>
                                        <span class="badge badge-tidak-aktif">Tidak Aktif</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <!-- PERUBAHAN: Tombol Copy ditambahkan -->
                                        <button type="button" class="btn-action btn-copy" onclick="copyApiKey('<?php echo htmlspecialchars($key['api_key']); ?>')" title="Salin API Key"><i class="fas fa-copy"></i></button>
                                        <a href="toggle_api_key.php?id=<?php echo $key['id']; ?>" class="btn-action btn-toggle" title="<?php echo ($key['status'] == 'aktif') ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                                            <i class="fas <?php echo ($key['status'] == 'aktif') ? 'fa-toggle-off' : 'fa-toggle-on'; ?>"></i>
                                        </a>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $key['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
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
        // --- PERUBAHAN: Fungsi baru untuk menyalin API Key ---
        function copyApiKey(apiKey) {
            // Buat elemen textarea sementara
            const textArea = document.createElement("textarea");
            textArea.value = apiKey;
            document.body.appendChild(textArea);
            textArea.select();
            try {
                document.execCommand('copy');
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: 'API Key disalin!',
                    showConfirmButton: false,
                    timer: 2000
                });
            } catch (err) {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal',
                    text: 'Tidak dapat menyalin API Key.',
                });
            }
            document.body.removeChild(textArea);
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "API Key ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_api_key.php?id=' + id;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ... (skrip dropdown Anda)
        });
    </script>
</body>
</html>
