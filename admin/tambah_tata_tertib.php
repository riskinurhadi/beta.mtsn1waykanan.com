<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
$judul = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $is_active = isset($_POST['is_active']) ? 1 : 0;

    // 1. Validasi Input
    if (empty($judul)) {
        $errors[] = "Judul tidak boleh kosong.";
    }

    // 2. Validasi File Upload (Wajib)
    if (isset($_FILES['nama_file']) && $_FILES['nama_file']['error'] == 0) {
        $target_dir = "uploads/tata_tertib/";
        
        // PERUBAHAN: Izinkan file PDF, PNG, JPG, JPEG
        $allowed_types = ['pdf', 'png', 'jpg', 'jpeg'];
        $max_file_size = 10 * 1024 * 1024; // 10 MB

        $file_ext = strtolower(pathinfo($_FILES['nama_file']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($file_ext, $allowed_types)) { 
            $errors[] = "Format file tidak diizinkan. Hanya PDF, PNG, JPG, dan JPEG."; 
        }
        if ($_FILES['nama_file']['size'] > $max_file_size) { 
            $errors[] = "Ukuran file tidak boleh lebih dari 10 MB."; 
        }
    } else {
        // PERUBAHAN: Pesan error lebih umum
        $errors[] = "Anda wajib memilih file untuk diunggah.";
    }

    // 3. Jika tidak ada error, proses data
    if (empty($errors)) {
        // Jika file baru akan diaktifkan, nonaktifkan semua yang lain
        if ($is_active) {
            $conn->query("UPDATE tata_tertib SET is_active = FALSE");
        }

        // Proses upload file
        $new_file_name = uniqid('tatib_', true) . '.' . $file_ext;
        $target_file = $target_dir . $new_file_name;

        if (move_uploaded_file($_FILES['nama_file']['tmp_name'], $target_file)) {
            $sql = "INSERT INTO tata_tertib (judul, nama_file, is_active) VALUES (?, ?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssi", $judul, $new_file_name, $is_active);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "File tata tertib baru berhasil diunggah!";
                    header("Location: kelola_tata_tertib.php");
                    exit();
                } else {
                    $errors[] = "Gagal menyimpan data ke database.";
                }
                $stmt->close();
            }
        } else {
            $errors[] = "Gagal mengunggah file. Pastikan folder 'uploads/tata_tertib' ada dan bisa ditulis.";
        }
    }
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unggah File Tata Tertib - Admin</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0}.sidebar-nav{list-style:none;padding-top:20px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}.sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center}.btn-back i{margin-right:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input[type=text]{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif;transition:border-color .3s ease}.form-group input[type=file]{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:8px}.form-group input:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}.checkbox-group{display:flex;align-items:center;gap:10px}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
             <li class="active"><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
             <!-- ... menu lainnya ... -->
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Unggah File Tata Tertib Baru</h1>
            <a href="kelola_tata_tertib.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="tambah_tata_tertib.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="judul">Judul File</label>
                        <input type="text" id="judul" name="judul" placeholder="Contoh: Tata Tertib Siswa 2024/2025" required value="<?php echo htmlspecialchars($judul); ?>">
                    </div>
                    
                    <div class="form-group">
                        <!-- PERUBAHAN: Label diubah -->
                        <label for="nama_file">Pilih File (PDF/Gambar)</label>
                        <!-- PERUBAHAN: Atribut 'accept' diubah -->
                        <input type="file" id="nama_file" name="nama_file" required accept=".pdf,.png,.jpg,.jpeg">
                        <!-- PERUBAHAN: Teks bantuan diubah -->
                        <small style="color: #777; margin-top: 5px; display: block;">Format: PDF, PNG, JPG. Ukuran maks: 10 MB.</small>
                    </div>

                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_active" name="is_active" value="1">
                        <label for="is_active" style="margin-bottom: 0;">Jadikan file ini aktif?</label>
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-upload"></i> Unggah dan Simpan</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
