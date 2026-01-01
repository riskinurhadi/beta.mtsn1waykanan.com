<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location:login.php");
    exit();
}

$errors = [];
$deskripsi = '';
$kategori = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $deskripsi = trim($_POST['deskripsi']);
    $kategori = trim($_POST['kategori']);

    // 1. Validasi Input Teks
    if (empty($deskripsi)) {
        $errors[] = "Deskripsi tidak boleh kosong.";
    }
    if (empty($kategori)) {
        $errors[] = "Kategori tidak boleh kosong.";
    }

    // 2. Validasi File Upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/galeri/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_name = $_FILES['foto']['name'];
        $file_size = $_FILES['foto']['size'];
        $file_tmp = $_FILES['foto']['tmp_name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        // Cek ekstensi file
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = "Format file tidak diizinkan. Hanya JPG, JPEG, PNG, dan GIF yang diperbolehkan.";
        }

        // Cek ukuran file
        if ($file_size > $max_file_size) {
            $errors[] = "Ukuran file tidak boleh lebih dari 2 MB.";
        }

    } else {
        $errors[] = "Anda harus memilih sebuah file foto untuk diunggah.";
    }

    // 3. Jika tidak ada error, proses upload dan simpan ke database
    if (empty($errors)) {
        // Buat nama file unik untuk mencegah penimpaan file
        $new_file_name = uniqid('galeri_', true) . '.' . $file_ext;
        $target_file = $target_dir . $new_file_name;

        // Pindahkan file dari temporary ke direktori tujuan
        if (move_uploaded_file($file_tmp, $target_file)) {
            // Jika upload berhasil, simpan informasi ke database
            // Query disesuaikan dengan struktur tabel Anda
            $sql = "INSERT INTO galeri (foto_url, kategori, deskripsi) VALUES (?, ?, ?)";
            
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("sss", $new_file_name, $kategori, $deskripsi);
                
                if ($stmt->execute()) {
                    // Set pesan sukses dan redirect ke halaman kelola galeri
                    $_SESSION['success_message'] = "Foto baru berhasil ditambahkan ke galeri!";
                    header("Location: kelola_galeri.php");
                    exit();
                } else {
                    $errors[] = "Gagal menyimpan data ke database.";
                }
                $stmt->close();
            }
        } else {
            $errors[] = "Gagal mengunggah file. Pastikan folder 'uploads/galeri' ada dan bisa ditulis (writable).";
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
    <title>Tambah Galeri - Admin MTsN 1 Way Kanan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        /* Menggunakan CSS yang sama dari dashboard.php untuk konsistensi */
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --sidebar-active: #34495e;
            --main-bg: #f4f7f6;
            --text-color: #333;
            --card-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            --danger-color: #e74c3c;
            --border-color: #e0e0e0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--main-bg); display: flex; }

        /* --- SIDEBAR STYLE (Sama seperti dashboard) --- */
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); height: 100vh; position: fixed; left: 0; top: 0; }
        /* ... (CSS Sidebar lengkap bisa dicopy dari file dashboard/kelola_galeri.php) ... */
        .sidebar-nav {list-style: none; padding-top: 20px;}
        .sidebar-nav li a { display: flex; align-items: center; padding: 15px 20px; color: var(--sidebar-text); text-decoration: none; transition: background-color 0.3s ease; font-size: 15px; }
        .sidebar-nav li a i { width: 30px; font-size: 18px; margin-right: 10px; }
        .sidebar-nav li a:hover, .sidebar-nav li.active a { background-color: var(--sidebar-active); }

        /* --- MAIN CONTENT STYLE --- */
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 600; color: var(--text-color); }
        .btn-back { background-color: #6c757d; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; }
        .btn-back i { margin-right: 8px; }

        /* --- FORM STYLE --- */
        .form-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--card-shadow);
        }
        .form-group {
            margin-bottom: 25px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #555;
        }
        .form-group input[type="text"],
        .form-group textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
            transition: border-color 0.3s ease;
        }
        .form-group input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 120px;
        }
        .form-group input:focus, .form-group textarea:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        .btn-submit {
            background-color: var(--primary-color);
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn-submit:hover { background-color: var(--primary-hover); }

        /* --- ALERT MESSAGES --- */
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            padding: 15px;
            border: 1px solid #f5c6cb;
            border-radius: 8px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="#"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="#"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li class="active"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="#"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="#"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Tambah Foto Baru</h1>
            <a href="kelola_galeri.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
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

                <form action="tambah_galeri.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="foto">Pilih Foto</label>
                        <input type="file" id="foto" name="foto" required accept="image/png, image/jpeg, image/jpg, image/gif">
                        <small style="color: #777; margin-top: 5px; display: block;">Format yang diizinkan: JPG, JPEG, PNG, GIF. Ukuran maksimal: 2 MB.</small>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi / Judul Foto</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($deskripsi); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" placeholder="Contoh: Kegiatan Sekolah, Fasilitas, Prestasi" required value="<?php echo htmlspecialchars($kategori); ?>">
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Galeri</button>
                </form>
            </div>
        </section>
    </main>

</body>
</html>