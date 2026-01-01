<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Cek ID dari URL, jika tidak ada atau tidak valid, redirect
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: kelola_kontak_pesan.php");
    exit();
}
$id = $_GET['id'];

// --- PROSES UPDATE STATUS (JIKA ADA FORM SUBMIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['status'])) {
        $new_status = $_POST['status'];
        $update_sql = "UPDATE pesan_kontak SET status = ? WHERE id = ?";
        if ($stmt_update = $conn->prepare($update_sql)) {
            $stmt_update->bind_param("si", $new_status, $id);
            $stmt_update->execute();
            $stmt_update->close();
            $_SESSION['success_message'] = "Status pesan berhasil diperbarui.";
            // Redirect untuk mencegah resubmit form
            header("Location: detail_pesan.php?id=$id");
            exit();
        }
    }
}

// --- AMBIL DETAIL PESAN DARI DATABASE ---
$sql = "SELECT * FROM pesan_kontak WHERE id = ? LIMIT 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Jika pesan tidak ditemukan
    $_SESSION['error_message'] = "Pesan tidak ditemukan.";
    header("Location: kelola_kontak_pesan.php");
    exit();
}
$pesan = $result->fetch_assoc();
$stmt->close();

// --- OTOMATIS UPDATE STATUS DARI 'Baru' MENJADI 'Sudah Dibaca' ---
if ($pesan['status'] == 'Baru') {
    $update_status_sql = "UPDATE pesan_kontak SET status = 'Sudah Dibaca' WHERE id = ?";
    if ($stmt_read = $conn->prepare($update_status_sql)) {
        $stmt_read->bind_param("i", $id);
        $stmt_read->execute();
        $stmt_read->close();
        // Update variabel lokal agar badge status langsung berubah
        $pesan['status'] = 'Sudah Dibaca';
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesan - Admin MTsN 1 Way Kanan</title>
    
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
        .btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center}
        .btn-back i{margin-right:8px}
        .message-container{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}
        .message-header{border-bottom:1px solid #eee;padding-bottom:20px;margin-bottom:20px}
        .message-header h2{font-size:22px;margin:0;font-weight:600}
        .sender-info{list-style:none;padding:0;margin-top:15px;color:#555}
        .sender-info li{display:flex;align-items:center;margin-bottom:8px}
        .sender-info li i{width:25px;font-size:16px;color:var(--primary-color)}
        .message-body p{line-height:1.8;font-size:16px;white-space:pre-wrap;/* Agar format baris baru tetap tampil */}
        .status-form{margin-top:30px;padding-top:20px;border-top:1px solid #eee;display:flex;align-items:center;gap:15px}
        .status-form label{font-weight:500}
        .status-form select{padding:8px 12px;border-radius:6px;border:1px solid #ccc;font-family:'Poppins',sans-serif}
        .btn-submit{background-color:var(--primary-color);color:#fff;padding:8px 20px;border:none;border-radius:6px;cursor:pointer}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
            <li class="active"><a href="kelola_kontak_pesan.php"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a></li>
            <!-- ... menu lainnya ... -->
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Detail Pesan</h1>
            <a href="kelola_kontak_pesan.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Inbox</a>
        </header>

        <section class="content">
            <div class="message-container">
                <div class="message-header">
                    <h2><?php echo htmlspecialchars($pesan['subjek']); ?></h2>
                    <ul class="sender-info">
                        <li><i class="fas fa-user"></i> <?php echo htmlspecialchars($pesan['nama_pengirim']); ?></li>
                        <li><i class="fas fa-envelope"></i> <a href="mailto:<?php echo htmlspecialchars($pesan['email_pengirim']); ?>"><?php echo htmlspecialchars($pesan['email_pengirim']); ?></a></li>
                        <li><i class="fas fa-phone"></i> <?php echo htmlspecialchars($pesan['telepon_pengirim']); ?></li>
                        <li><i class="fas fa-calendar-alt"></i> <?php echo date('d F Y, H:i', strtotime($pesan['tanggal_kirim'])); ?></li>
                    </ul>
                </div>
                <div class="message-body">
                    <p><?php echo nl2br(htmlspecialchars($pesan['isi_pesan'])); ?></p>
                </div>
                <div class="status-form">
                    <form action="detail_pesan.php?id=<?php echo $id; ?>" method="post">
                        <label for="status">Ubah Status Pesan:</label>
                        <select name="status" id="status">
                            <option value="Sudah Dibaca" <?php echo ($pesan['status'] == 'Sudah Dibaca') ? 'selected' : ''; ?>>Sudah Dibaca</option>
                            <option value="Sudah Dibalas" <?php echo ($pesan['status'] == 'Sudah Dibalas') ? 'selected' : ''; ?>>Sudah Dibalas</option>
                        </select>
                        <button type="submit" class="btn-submit">Simpan Status</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        // Script untuk menampilkan notifikasi sukses dari session
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2000, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        ?>
    </script>
</body>
</html>
