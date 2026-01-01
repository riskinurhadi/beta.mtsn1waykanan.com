<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
// Inisialisasi variabel untuk menampung nilai form jika terjadi error
$nama_siswa = '';
$nama_prestasi = '';
$tingkat = '';
$tahun = '';
$deskripsi = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $nama_siswa = trim($_POST['nama_siswa']);
    $nama_prestasi = trim($_POST['nama_prestasi']);
    $tingkat = trim($_POST['tingkat']);
    $tahun = trim($_POST['tahun']);
    $deskripsi = trim($_POST['deskripsi']);

    // 1. Validasi Input Teks
    if (empty($nama_siswa)) { $errors[] = "Nama siswa tidak boleh kosong."; }
    if (empty($nama_prestasi)) { $errors[] = "Nama prestasi tidak boleh kosong."; }
    if (empty($tingkat)) { $errors[] = "Tingkat tidak boleh kosong."; }
    if (empty($tahun)) { 
        $errors[] = "Tahun tidak boleh kosong.";
    } elseif (!preg_match("/^[0-9]{4}$/", $tahun)) {
        $errors[] = "Format tahun tidak valid (Contoh: 2024).";
    }

    // 2. Validasi dan Proses File Upload (Opsional)
    $new_file_name = NULL; // Default NULL jika tidak ada foto
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/prestasi/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (!in_array($file_ext, $allowed_types)) { $errors[] = "Format foto tidak diizinkan. Hanya JPG, JPEG, PNG, dan GIF."; }
        if ($file_size > $max_file_size) { $errors[] = "Ukuran foto tidak boleh lebih dari 2 MB."; }

        if (empty($errors)) {
            // Buat nama file unik
            $new_file_name = uniqid('prestasi_', true) . '.' . $file_ext;
            $target_file = $target_dir . $new_file_name;

            if (!move_uploaded_file($file_tmp, $target_file)) {
                $errors[] = "Gagal mengunggah foto. Pastikan folder 'uploads/prestasi' ada dan bisa ditulis.";
                $new_file_name = NULL; // Gagal upload, kembalikan ke NULL
            }
        }
    }

    // 3. Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        // Query disesuaikan dengan struktur tabel Anda
        $sql = "INSERT INTO prestasi (nama_siswa, nama_prestasi, tingkat, tahun, deskripsi, foto_url) VALUES (?, ?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            // Asumsi nama tabel adalah 'prestasi'
            $stmt->bind_param("ssssss", $nama_siswa, $nama_prestasi, $tingkat, $tahun, $deskripsi, $new_file_name);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data prestasi baru berhasil ditambahkan!";
                header("Location: kelola_prestasi.php");
                exit();
            } else {
                $errors[] = "Gagal menyimpan data ke database.";
            }
            $stmt->close();
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
    <title>Tambah Prestasi - Admin MTsN 1 Way Kanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0}.sidebar-nav{list-style:none;padding-top:20px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}.sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center}.btn-back i{margin-right:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input[type=text],.form-group input[type=number],.form-group textarea{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif;transition:border-color .3s ease}.form-group input[type=file]{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:8px}.form-group textarea{resize:vertical;min-height:120px}.form-group input:focus,.form-group textarea:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li class="active"><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Tambah Prestasi Baru</h1>
            <a href="kelola_prestasi.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger">
                        <strong>Gagal!</strong>
                        <ul>
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo $error; ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <form action="tambah_prestasi.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_siswa">Nama Siswa</label>
                        <input type="text" id="nama_siswa" name="nama_siswa" placeholder="Masukkan nama lengkap siswa" required value="<?php echo htmlspecialchars($nama_siswa); ?>">
                    </div>
                    <div class="form-group">
                        <label for="nama_prestasi">Nama Lomba / Prestasi</label>
                        <input type="text" id="nama_prestasi" name="nama_prestasi" placeholder="Contoh: Juara 1 Lomba Kaligrafi" required value="<?php echo htmlspecialchars($nama_prestasi); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tingkat">Tingkat</label>
                        <input type="text" id="tingkat" name="tingkat" placeholder="Contoh: Kabupaten, Provinsi, Nasional" required value="<?php echo htmlspecialchars($tingkat); ?>">
                    </div>
                    <div class="form-group">
                        <label for="tahun">Tahun</label>
                        <input type="number" id="tahun" name="tahun" placeholder="Contoh: 2024" required min="2000" max="<?php echo date('Y'); ?>" value="<?php echo htmlspecialchars($tahun); ?>">
                    </div>
                    <div class="form-group">
                        <label for="deskripsi">Deskripsi (Opsional)</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" placeholder="Jelaskan sedikit tentang prestasi ini..."><?php echo htmlspecialchars($deskripsi); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="foto">Foto (Opsional)</label>
                        <input type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg, image/gif">
                        <small style="color: #777; margin-top: 5px; display: block;">Format: JPG, PNG, GIF. Ukuran maks: 2 MB.</small>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Prestasi</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>