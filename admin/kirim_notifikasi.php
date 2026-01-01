<?php
require_once 'config.php';

// --- SECURITY CHECK ---
// 1. Cek apakah operator sudah login
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Cek apakah rolenya adalah 'superadmin'
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    // PERUBAHAN: Mengganti die() dengan halaman HTML yang terdesain
    http_response_code(403); // Set status HTTP ke 403 Forbidden
    echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --danger-color: #e74c3c; --main-bg: #f4f7f6; --text-color: #333; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--main-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
        }
        .access-denied-container {
            text-align: center;
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .icon-wrapper i {
            font-size: 60px;
            color: var(--danger-color);
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-back {
            background-color: #6c757d;
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="access-denied-container">
        <div class="icon-wrapper">
            <i class="fas fa-ban"></i>
        </div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk menghapus atau mengedit Admin <br> Jika menurut anda tindakan ini perlu dilakukan, silahkan hubungi Kepala Madrasah <br> atau hubungi Pengembang via WhatsApp 082371869118 (Riski Nurhadi).</p>
        <a href="kelola_operator.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>
</html>
HTML;
    exit(); // Hentikan eksekusi skrip setelah menampilkan halaman
}
// 3. Cek ID penerima dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: kelola_operator.php");
    exit();
}
$id_penerima = $_GET['id'];
$id_pengirim = $_SESSION['operator_id']; // ID developer yang sedang login

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'operator';

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
    $pesan = trim($_POST['pesan']);

    if (empty($pesan)) {
        $errors[] = "Pesan notifikasi tidak boleh kosong.";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO notifikasi_operator (id_penerima, id_pengirim, pesan) VALUES (?, ?, ?)";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("iis", $id_penerima, $id_pengirim, $pesan);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Notifikasi berhasil dikirim.";
                header("Location: kelola_operator.php");
                exit();
            } else {
                $errors[] = "Gagal mengirim notifikasi.";
            }
            $stmt->close();
        }
    }
}

// --- Ambil nama penerima untuk ditampilkan di form ---
$nama_penerima = '';
$sql_get_penerima = "SELECT nama_lengkap FROM operator_madrasah WHERE id = ?";
if($stmt_get = $conn->prepare($sql_get_penerima)) {
    $stmt_get->bind_param("i", $id_penerima);
    $stmt_get->execute();
    $stmt_get->bind_result($nama_penerima);
    $stmt_get->fetch();
    $stmt_get->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kirim Notifikasi - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}.sidebar-header h3{font-weight:600;color:#fff}.sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}.sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}.sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group textarea{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif;resize:vertical;min-height:150px}.form-group textarea:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}.sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}.sidebar-dropdown.open .dropdown-menu{display:block}.dropdown-menu li a{padding-left:65px}.dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}.sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}.notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
    </style>
</head>
<body>
    
    <aside class="sidebar">
        <!-- ... (Kode sidebar lengkap Anda di sini) ... -->
    </aside> 
    
    <main class="main-content">
        <header class="page-header">
            <h1>Kirim Notifikasi</h1>
            <a href="kelola_operator.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="kirim_notifikasi.php?id=<?php echo $id_penerima; ?>" method="post">
                    <div class="form-group">
                        <label for="penerima">Kirim Ke:</label>
                        <input type="text" id="penerima" name="penerima" value="<?php echo htmlspecialchars($nama_penerima); ?>" disabled style="background-color: #e9ecef; cursor: not-allowed;">
                    </div>
                    <div class="form-group">
                        <label for="pesan">Isi Pesan Notifikasi</label>
                        <textarea id="pesan" name="pesan" required></textarea>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Kirim Notifikasi</button>
                </form>
            </div>
        </section>
    </main>
    
    <script>
        // ... (Skrip dropdown Anda) ...
    </script>
</body>
</html>
