<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}
// Hanya Super Admin dan Developer yang bisa mengakses
if (!in_array($_SESSION['role'], [' ', 'developer'])) {
    // Tampilkan halaman akses ditolak
    http_response_code(403);
    echo <<<HTML
<!DOCTYPE html><html lang="id"><head><meta charset="UTF-8"><title>Akses Ditolak</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>:root{--danger-color:#e74c3c;--main-bg:#f4f7f6;--text-color:#333}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0;color:var(--text-color)}.access-denied-container{text-align:center;background-color:#fff;padding:40px 50px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1)}.icon-wrapper i{font-size:60px;color:var(--danger-color);margin-bottom:20px}h1{font-size:28px;font-weight:600;margin-bottom:10px}p{font-size:16px;color:#666;margin-bottom:30px}.btn-back{background-color:#6c757d;color:#fff;padding:12px 25px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px;transition:background-color .3s ease}.btn-back:hover{background-color:#5a6268}</style>
</head>
<body>
<div class="access-denied-container">
<div class="icon-wrapper">
<i class="fas fa-ban"></i>
</div>
<h1>Akses Ditolak</h1>
<p>Anda tidak memiliki Akses<br>Halaman ini hanya bisa di akses oleh Pengembang sistem ini.</p>
<a href="index.php" class="btn-back"><i class="fas fa-arrow-left">
</i> Kembali ke Dashboard</a></div></body></html>
HTML;
    exit();
}

$halaman_aktif = 'pengaturan';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

$errors = [];

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Loop melalui semua data yang dikirim dari form
    foreach ($_POST as $key => $value) {
        $clean_value = trim($value);
        
        $sql = "INSERT INTO pengaturan (nama_pengaturan, nilai_pengaturan) VALUES (?, ?) ON DUPLICATE KEY UPDATE nilai_pengaturan = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sss", $key, $clean_value, $clean_value);
            $stmt->execute();
            $stmt->close();
        }
    }
    
    $_SESSION['success_message'] = "Pengaturan berhasil diperbarui.";
    header("Location: developer.php");
    exit();
}


// --- AMBIL SEMUA DATA PENGATURAN DARI DATABASE ---
$pengaturan = [];
$sql_get = "SELECT nama_pengaturan, nilai_pengaturan FROM pengaturan";
if ($result = $conn->query($sql_get)) {
    while ($row = $result->fetch_assoc()) {
        $pengaturan[$row['nama_pengaturan']] = $row['nilai_pengaturan'];
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Developer Page</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}.sidebar-header h3{font-weight:600;color:#fff}.sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}.sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}.sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-section h3{font-size:20px;font-weight:600;margin-bottom:20px;padding-bottom:10px;border-bottom:1px solid #eee}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input,.form-group textarea,.form-group select{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif}.form-group input:focus,textarea:focus,select:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 30px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease;float:right}.btn-submit:hover{background-color:var(--primary-hover)}.sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}.sidebar-dropdown.open .dropdown-menu{display:block}.dropdown-menu li a{padding-left:65px}.dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}.sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}.notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
    </style>
</head>
<body>
    <aside class="sidebar">
        <!-- ... (Kode sidebar lengkap Anda di sini) ... -->
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Halaman Pengembang</h1>
        </header>

        <section class="content">
            <form action="developer.php" method="post">
                <div class="form-card">
                    <!-- Bagian Pengaturan Lanjutan -->
                    <div class="form-section">
                        <h3><i class="fas fa-cogs"></i> Pengaturan Lanjutan</h3>
                        <div class="form-group">
                            <label for="maintenance_mode">Mode Perawatan (Maintenance)</label>
                            <select id="maintenance_mode" name="maintenance_mode" class="form-control">
                                <option value="off" <?php echo (($pengaturan['maintenance_mode'] ?? 'off') == 'off') ? 'selected' : ''; ?>>
                                    Off (Website dapat diakses publik)
                                </option>
                                <option value="on" <?php echo (($pengaturan['maintenance_mode'] ?? 'off') == 'on') ? 'selected' : ''; ?>>
                                    On (Website menampilkan halaman perbaikan)
                                </option>
                            </select>
                            <small style="color: #777; margin-top: 5px; display: block;">Jika 'On', pengunjung hanya akan melihat halaman maintenance.</small>
                        </div>
                    </div>

                    <div style="overflow: hidden; margin-top: 30px;">
                        <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Pengaturan</button>
                    </div>
                </div>
            </form>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2000, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        ?>
    </script>
</body>
</html>
