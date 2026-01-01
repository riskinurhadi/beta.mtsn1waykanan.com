<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

$errors = [];
// Variabel untuk menampung nilai form agar tidak hilang jika terjadi error
$nama_lengkap = '';
$jabatan = '';
$kategori_jabatan = '';
$urutan = 0;

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $jabatan = trim($_POST['jabatan']);
    $kategori_jabatan = trim($_POST['kategori_jabatan']);
    $urutan = filter_var($_POST['urutan'], FILTER_VALIDATE_INT);

    // 1. Validasi Input Teks
    if (empty($nama_lengkap) || empty($jabatan) || empty($kategori_jabatan) || $urutan === false) {
        $errors[] = "Semua field wajib diisi dan urutan harus berupa angka.";
    }

    // 2. Validasi dan Proses File Upload (Opsional)
    $new_file_name = NULL; // Default NULL jika tidak ada foto
    if (isset($_FILES['foto_url']) && $_FILES['foto_url']['error'] == 0) {
        $target_dir = "uploads/struktur/";
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_ext = strtolower(pathinfo($_FILES['foto_url']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) { $errors[] = "Format foto tidak diizinkan."; }
        if ($_FILES['foto_url']['size'] > $max_file_size) { $errors[] = "Ukuran foto tidak boleh lebih dari 2 MB."; }

        if (empty($errors)) {
            $new_file_name = uniqid('struktur_', true) . '.' . $file_ext;
            if (!move_uploaded_file($_FILES['foto_url']['tmp_name'], $target_dir . $new_file_name)) {
                $errors[] = "Gagal mengunggah foto. Pastikan folder 'uploads/struktur' ada dan bisa ditulis.";
                $new_file_name = NULL;
            }
        }
    }

    // 3. Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $sql = "INSERT INTO struktur_organisasi (nama_lengkap, jabatan, foto_url, kategori_jabatan, urutan) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $nama_lengkap, $jabatan, $new_file_name, $kategori_jabatan, $urutan);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Anggota baru berhasil ditambahkan.";
                header("Location: kelola_struktural.php");
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
    <title>Tambah Anggota Struktural - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0}.sidebar-nav{list-style:none;padding-top:20px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}.sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center}.btn-back i{margin-right:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input[type=text],.form-group input[type=number]{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif;transition:border-color .3s ease}.form-group input[type=file]{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:8px}.form-group input:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
             <li class="active"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
             <!-- ... menu lainnya ... -->
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Tambah Anggota Struktural</h1>
            <a href="kelola_struktural.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="tambah_struktural.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_lengkap">Nama Lengkap</label>
                        <input type="text" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($nama_lengkap); ?>">
                    </div>
                    <div class="form-group">
                        <label for="jabatan">Jabatan</label>
                        <input type="text" id="jabatan" name="jabatan" required value="<?php echo htmlspecialchars($jabatan); ?>">
                    </div>
                    <div class="form-group">
                        <label for="kategori_jabatan">Kategori Jabatan</label>
                        <input type="text" id="kategori_jabatan" name="kategori_jabatan" placeholder="Contoh: Pimpinan, Staf Pengajar" required value="<?php echo htmlspecialchars($kategori_jabatan); ?>">
                    </div>
                    <div class="form-group">
                        <label for="urutan">Nomor Urut Tampilan</label>
                        <input type="number" id="urutan" name="urutan" required value="<?php echo htmlspecialchars($urutan); ?>">
                        <small style="color: #777; margin-top: 5px; display: block;">Angka lebih kecil akan tampil lebih dulu.</small>
                    </div>
                    <div class="form-group">
                        <label for="foto_url">Foto (Opsional)</label>
                        <input type="file" id="foto_url" name="foto_url" accept="image/png, image/jpeg, image/jpg">
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Data</button>
                </form>
            </div>
        </section>
    </main>
</body>
</html>
