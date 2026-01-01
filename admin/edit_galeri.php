<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Cek ID dari URL, jika tidak ada, redirect
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: kelola_galeri.php");
    exit();
}

$id = $_GET['id'];
$errors = [];

// --- PROSES UPDATE SAAT FORM DI-SUBMIT (METHOD POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $deskripsi = trim($_POST['deskripsi']);
    $kategori = trim($_POST['kategori']);
    $current_foto = $_POST['current_foto'];
    
    // Validasi input
    if (empty($deskripsi) || empty($kategori)) {
        $errors[] = "Deskripsi dan Kategori tidak boleh kosong.";
    }

    $new_file_name = $current_foto;

    // Cek jika ada file baru yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "uploads/galeri/";
        $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
        $max_file_size = 2 * 1024 * 1024;

        $file_ext = strtolower(pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) { $errors[] = "Format file baru tidak diizinkan."; }
        if ($_FILES['foto']['size'] > $max_file_size) { $errors[] = "Ukuran file baru tidak boleh lebih dari 2 MB."; }

        if (empty($errors)) {
            $new_file_name = uniqid('galeri_', true) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_dir . $new_file_name)) {
                if ($current_foto && file_exists($target_dir . $current_foto)) {
                    unlink($target_dir . $current_foto);
                }
            } else {
                $errors[] = "Gagal mengunggah file baru.";
            }
        }
    }

    if (empty($errors)) {
        $sql = "UPDATE galeri SET deskripsi = ?, kategori = ?, foto_url = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssi", $deskripsi, $kategori, $new_file_name, $id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Data galeri berhasil diperbarui.";
                header("Location: kelola_galeri.php");
                exit();
            } else {
                $errors[] = "Gagal memperbarui data di database.";
            }
            $stmt->close();
        }
    }
}

// --- AMBIL DATA SAAT INI UNTUK DITAMPILKAN DI FORM (METHOD GET) ---
$sql_select = "SELECT foto_url, kategori, deskripsi FROM galeri WHERE id = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $item = $result->fetch_assoc();
        $deskripsi = $item['deskripsi'];
        $kategori = $item['kategori'];
        $foto_url = $item['foto_url'];
    } else {
        $_SESSION['error_message'] = "Data galeri tidak ditemukan.";
        header("Location: kelola_galeri.php");
        exit();
    }
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Galeri - Admin MTsN 1 Way Kanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --sidebar-bg: #2c3e50;
            --sidebar-text: #ecf0f1;
            --sidebar-active: #34495e;
            --main-bg: #f4f7f6;
            --text-color: #333;
            --card-shadow: 0 4px 15px rgba(0,0,0,.08);
            --danger-color: #e74c3c;
            --border-color: #e0e0e0;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--main-bg); display: flex; }

        /* --- PERUBAHAN: Menggunakan blok CSS Sidebar yang baru --- */
        .sidebar {
            width: 260px;
            background-color: var(--sidebar-bg);
            color: var(--sidebar-text);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            display: flex;
            flex-direction: column;
            transition: width 0.3s ease;
        }
        .sidebar-header {
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid #34495e;
        }
        .sidebar-header h3 {
            font-weight: 600;
        }
        .sidebar-nav {
            flex-grow: 1;
            list-style: none;
            padding-top: 20px;
        }
        .sidebar-nav li a {
            display: flex;
            align-items: center;
            padding: 15px 20px;
            color: var(--sidebar-text);
            text-decoration: none;
            transition: background-color 0.3s ease;
            font-size: 15px;
        }
        .sidebar-nav li a i {
            width: 30px;
            font-size: 18px;
            margin-right: 10px;
        }
        .sidebar-nav li a:hover,
        .sidebar-nav li.active a {
            background-color: var(--sidebar-active);
        }

        /* --- MAIN CONTENT STYLE --- */
        .main-content { margin-left: 260px; width: calc(100% - 260px); padding: 20px; }
        .page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .page-header h1 { font-size: 24px; font-weight: 600; color: var(--text-color); }
        .btn-back { background-color: #6c757d; color: #fff; padding: 10px 20px; border-radius: 8px; text-decoration: none; font-weight: 500; display: inline-flex; align-items: center; }
        .btn-back i { margin-right: 8px; }

        /* --- FORM STYLE --- */
        .form-card { background-color: #fff; padding: 30px; border-radius: 12px; box-shadow: var(--card-shadow); }
        .form-group { margin-bottom: 25px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        .form-group input[type="text"], .form-group textarea { width: 100%; padding: 12px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; font-family: 'Poppins', sans-serif; transition: border-color 0.3s ease; }
        .form-group input[type="file"] { width: 100%; padding: 10px; border: 1px solid var(--border-color); border-radius: 8px; }
        .form-group textarea { resize: vertical; min-height: 120px; }
        .form-group input:focus, .form-group textarea:focus { outline: none; border-color: var(--primary-color); }
        .btn-submit { background-color: var(--primary-color); color: #fff; padding: 12px 25px; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-submit:hover { background-color: var(--primary-hover); }

        /* --- ALERT MESSAGES --- */
        .alert-danger { background-color: #f8d7da; color: #721c24; padding: 15px; border: 1px solid #f5c6cb; border-radius: 8px; margin-bottom: 20px; }
        .current-image { width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 2px solid var(--border-color); margin-top: 10px; }

        /* --- RESPONSIVE --- */
        @media (max-width: 992px) {
            .sidebar { width: 70px; }
            .sidebar-header h3, .sidebar-nav li a span { display: none; }
            .main-content { margin-left: 70px; width: calc(100% - 70px); }
        }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li class="active"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Edit Foto Galeri</h1>
            <a href="kelola_galeri.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="edit_galeri.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="current_foto" value="<?php echo htmlspecialchars($foto_url); ?>">

                    <div class="form-group">
                        <label>Foto Saat Ini</label><br>
                        <img src="uploads/galeri/<?php echo htmlspecialchars($foto_url); ?>" alt="Foto saat ini" class="current-image">
                    </div>

                    <div class="form-group">
                        <label for="foto">Ubah Foto (Opsional)</label>
                        <input type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg, image/gif">
                        <small style="color: #777; margin-top: 5px; display: block;">Kosongkan jika tidak ingin mengubah foto.</small>
                    </div>

                    <div class="form-group">
                        <label for="deskripsi">Deskripsi / Judul Foto</label>
                        <textarea id="deskripsi" name="deskripsi" rows="4" required><?php echo htmlspecialchars($deskripsi); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" required value="<?php echo htmlspecialchars($kategori); ?>">
                    </div>

                    <button type="submit" class="btn-submit"><i class="fas fa-sync-alt"></i> Perbarui Galeri</button>
                </form>
            </div>
        </section>
    </main>

</body>
</html>