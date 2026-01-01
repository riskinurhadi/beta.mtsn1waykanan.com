<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

$errors = [];
// Inisialisasi variabel untuk menampung nilai form jika terjadi error
$nama_alumni = '';
$info_singkat = '';
$testimoni = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $nama_alumni = trim($_POST['nama_alumni']);
    $info_singkat = trim($_POST['info_singkat']);
    $testimoni = trim($_POST['testimoni']);
    $is_tampil = isset($_POST['is_tampil']) ? 1 : 0;

    // 1. Validasi Input Teks
    if (empty($nama_alumni)) { $errors[] = "Nama alumni tidak boleh kosong."; }
    if (empty($testimoni)) { $errors[] = "Isi testimoni tidak boleh kosong."; }

    // 2. Validasi dan Proses File Upload (Opsional)
    $new_file_name = NULL;
    if (isset($_FILES['foto_alumni']) && $_FILES['foto_alumni']['error'] == 0) {
        $target_dir = "uploads/testimoni/";
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_ext = strtolower(pathinfo($_FILES['foto_alumni']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) { $errors[] = "Format foto tidak diizinkan."; }
        if ($_FILES['foto_alumni']['size'] > $max_file_size) { $errors[] = "Ukuran foto tidak boleh lebih dari 2 MB."; }

        if (empty($errors)) {
            $new_file_name = uniqid('alumni_', true) . '.' . $file_ext;
            if (!move_uploaded_file($_FILES['foto_alumni']['tmp_name'], $target_dir . $new_file_name)) {
                $errors[] = "Gagal mengunggah foto. Pastikan folder 'uploads/testimoni' ada dan bisa ditulis.";
                $new_file_name = NULL;
            }
        }
    }

    // 3. Jika tidak ada error, simpan ke database
    if (empty($errors)) {
        $sql = "INSERT INTO testimoni_alumni (nama_alumni, info_singkat, testimoni, foto_alumni, is_tampil) VALUES (?, ?, ?, ?, ?)";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ssssi", $nama_alumni, $info_singkat, $testimoni, $new_file_name, $is_tampil);
            
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Testimoni baru berhasil ditambahkan.";
                header("Location: kelola_testimoni.php");
                exit();
            } else {
                $errors[] = "Gagal menyimpan data ke database: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Testimoni - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}.sidebar-header h3{font-weight:600;color:#fff}.sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}.sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}.sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-group{margin-bottom:25px}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input[type=text],.form-group textarea{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif}.form-group input[type=file]{width:100%;padding:10px;border:1px solid var(--border-color);border-radius:8px}.form-group textarea{resize:vertical;min-height:120px}.form-group input:focus,.form-group textarea:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}.checkbox-group{display:flex;align-items:center;gap:10px}.sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}.sidebar-dropdown.open .dropdown-menu{display:block}.dropdown-menu li a{padding-left:65px}.dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}.sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}.notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
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
            <li><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
            <li class="active"><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li>
                        <a href="kelola_kontak_pesan.php">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Masuk</span>
                            <?php if ($jumlah_pesan_baru > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
                </ul>
            </li>
        </ul>
    </aside> 
    
    <main class="main-content">
        <header class="page-header">
            <h1>Tambah Testimoni Baru</h1>
            <a href="kelola_testimoni.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="tambah_testimoni.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nama_alumni">Nama Alumni (Wajib)</label>
                        <input type="text" id="nama_alumni" name="nama_alumni" required value="<?php echo htmlspecialchars($nama_alumni); ?>">
                    </div>
                    <div class="form-group">
                        <label for="info_singkat">Info Singkat (Opsional)</label>
                        <input type="text" id="info_singkat" name="info_singkat" placeholder="Contoh: Lulusan 2020, Mahasiswa UGM" value="<?php echo htmlspecialchars($info_singkat); ?>">
                    </div>
                    <div class="form-group">
                        <label for="testimoni">Isi Testimoni (Wajib)</label>
                        <textarea id="testimoni" name="testimoni" rows="5" required><?php echo htmlspecialchars($testimoni); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="foto_alumni">Foto Alumni (Opsional)</label>
                        <input type="file" id="foto_alumni" name="foto_alumni" accept="image/png, image/jpeg, image/jpg">
                    </div>
                    <div class="form-group checkbox-group">
                        <input type="checkbox" id="is_tampil" name="is_tampil" value="1">
                        <label for="is_tampil" style="margin-bottom: 0;">Langsung tampilkan di halaman publik?</label>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Testimoni</button>
                </form>
            </div>
        </section>
    </main>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown > a');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('open');
                });
            });
        });
    </script>
</body>
</html>
