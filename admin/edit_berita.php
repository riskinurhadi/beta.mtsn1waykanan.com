<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

function create_slug($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9\s-]/', '', $string);
    $string = preg_replace('/[\s-]+/', '-', $string);
    return trim($string, '-');
}

// Validasi ID dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: kelola_berita.php");
    exit();
}
$id = $_GET['id'];
$errors = [];

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $judul = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $isi = $_POST['isi'];
    $current_gambar = $_POST['current_gambar'];

    // Validasi dasar
    if (empty($judul) || empty($kategori) || empty($isi)) {
        $errors[] = "Judul, Kategori, dan Isi Berita tidak boleh kosong.";
    }

    // Proses upload gambar jika ada gambar baru yang diunggah
    $new_file_name = $current_gambar;
    if (isset($_FILES['gambar_utama']) && $_FILES['gambar_utama']['error'] == 0) {
        $target_dir = "uploads/berita/";
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_ext = strtolower(pathinfo($_FILES['gambar_utama']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            $errors[] = "Format gambar tidak diizinkan.";
        }
        if ($_FILES['gambar_utama']['size'] > $max_file_size) {
            $errors[] = "Ukuran gambar tidak boleh lebih dari 2 MB.";
        }

        if (empty($errors)) {
            $new_file_name = uniqid('berita_', true) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['gambar_utama']['tmp_name'], $target_dir . $new_file_name)) {
                // Hapus gambar lama jika ada
                if ($current_gambar && file_exists($target_dir . $current_gambar)) {
                    unlink($target_dir . $current_gambar);
                }
            } else {
                $errors[] = "Gagal mengunggah gambar baru.";
            }
        }
    }

    // Jika tidak ada error, update database
    if (empty($errors)) {
        $slug = create_slug($judul);
        $sql = "UPDATE berita SET judul = ?, isi = ?, kategori = ?, gambar_utama = ?, slug = ? WHERE id = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("sssssi", $judul, $isi, $kategori, $new_file_name, $slug, $id);
            if ($stmt->execute()) {
                $_SESSION['success_message'] = "Berita berhasil diperbarui.";
                header("Location: kelola_berita.php");
                exit();
            } else {
                $errors[] = "Gagal memperbarui data di database.";
            }
            $stmt->close();
        }
    }
}

// Ambil data berita yang akan diedit untuk ditampilkan di form
$sql_select = "SELECT judul, isi, kategori, gambar_utama FROM berita WHERE id = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $berita = $result->fetch_assoc();
    } else {
        header("Location: kelola_berita.php");
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
    <title>Edit Berita - Admin MTsN 1 Way Kanan</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <style>
        /* Salin CSS yang sama dari halaman tambah_berita.php agar konsisten */
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;z-index: 1040;}
        .sidebar-nav{list-style:none;padding-top:20px}
        .sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}
        .sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}
        .sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}
        .main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}
        .page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}
        .btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center}
        .btn-back i{margin-right:8px}
        .form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}
        .form-group label{font-weight:500;color:#555;}
        .form-control { border-radius: 8px !important; } /* Override Bootstrap */
        .btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}
        .btn-submit:hover{background-color:var(--primary-hover)}
        .alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}
        .current-image{max-width:200px;height:auto;border-radius:8px;border:2px solid var(--border-color);margin-top:10px}
        .note-editor.note-frame { border-radius: 8px; border-color: var(--border-color); }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
            <li class="active"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Edit Berita</h1>
            <a href="kelola_berita.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="edit_berita.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="current_gambar" value="<?php echo htmlspecialchars($berita['gambar_utama']); ?>">

                    <div class="form-group">
                        <label for="judul">Judul Berita</label>
                        <input type="text" id="judul" name="judul" class="form-control" value="<?php echo htmlspecialchars($berita['judul']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" class="form-control" value="<?php echo htmlspecialchars($berita['kategori']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="isi_berita">Isi Berita</label>
                        <textarea id="isi_berita" name="isi" class="form-control"><?php echo htmlspecialchars($berita['isi']); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Gambar Utama Saat Ini</label><br>
                        <img src="uploads/berita/<?php echo htmlspecialchars($berita['gambar_utama']); ?>" alt="Gambar saat ini" class="current-image">
                    </div>

                    <div class="form-group">
                        <label for="gambar_utama">Ganti Gambar Utama (Opsional)</label>
                        <input type="file" id="gambar_utama" name="gambar_utama" class="form-control" accept="image/png, image/jpeg, image/jpg">
                        <small>Kosongkan jika tidak ingin mengubah gambar.</small>
                    </div>
                    
                    <button type="submit" class="btn-submit"><i class="fas fa-sync-alt"></i> Perbarui Berita</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        // PERUBAHAN: Inisialisasi Summernote
        $(document).ready(function() {
            $('#isi_berita').summernote({
                height: 300,
                minHeight: null,
                maxHeight: null,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
        });
    </script>
</body>
</html>