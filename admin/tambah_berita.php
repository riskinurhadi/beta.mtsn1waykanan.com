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

// --- Helper function untuk upload file ---
function upload_image($file_input_name, $target_dir) {
    if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024; // 2 MB

        $file_ext = strtolower(pathinfo($_FILES[$file_input_name]['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            return ['error' => "Format gambar untuk " . $file_input_name . " tidak diizinkan."];
        }
        if ($_FILES[$file_input_name]['size'] > $max_file_size) {
            return ['error' => "Ukuran gambar untuk " . $file_input_name . " tidak boleh lebih dari 2 MB."];
        }

        $new_file_name = uniqid('berita_', true) . '.' . $file_ext;
        if (move_uploaded_file($_FILES[$file_input_name]['tmp_name'], $target_dir . $new_file_name)) {
            return ['success' => $new_file_name];
        } else {
            return ['error' => "Gagal mengunggah file " . $file_input_name];
        }
    }
    return ['success' => null]; // Tidak ada file atau ada error upload dari sisi klien
}


$errors = [];
$judul = '';
$kategori = '';
$isi = '';

// --- PROSES FORM SAAT DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $judul = trim($_POST['judul']);
    $kategori = trim($_POST['kategori']);
    $isi = $_POST['isi'];
    $penulis = $_SESSION['nama_lengkap'];

    if (empty($judul)) { $errors[] = "Judul berita tidak boleh kosong."; }
    if (empty($kategori)) { $errors[] = "Kategori tidak boleh kosong."; }
    if (empty($isi)) { $errors[] = "Isi berita tidak boleh kosong."; }

    // Validasi Gambar Utama (Wajib)
    if (!isset($_FILES['gambar_utama']) || $_FILES['gambar_utama']['error'] != 0) {
        $errors[] = "Gambar utama wajib diunggah.";
    }

    // Jika validasi dasar lolos, proses upload gambar
    if (empty($errors)) {
        $target_dir = "uploads/berita/";
        
        $upload_utama = upload_image('gambar_utama', $target_dir);
        $upload_2 = upload_image('gambar_2', $target_dir);
        $upload_3 = upload_image('gambar_3', $target_dir);

        if (isset($upload_utama['error'])) { $errors[] = $upload_utama['error']; }
        if (isset($upload_2['error'])) { $errors[] = $upload_2['error']; }
        if (isset($upload_3['error'])) { $errors[] = $upload_3['error']; }

        // Jika semua upload (yang ada) berhasil
        if (empty($errors)) {
            $gambar_utama_db = $upload_utama['success'];
            $gambar_2_db = $upload_2['success'];
            $gambar_3_db = $upload_3['success'];

            // Buat dan cek keunikan slug
            $slug = create_slug($judul);
            $slug_check_sql = "SELECT id FROM berita WHERE slug = ?";
            $stmt_check = $conn->prepare($slug_check_sql);
            $stmt_check->bind_param("s", $slug);
            $stmt_check->execute();
            $stmt_check->store_result();
            if ($stmt_check->num_rows > 0) {
                $slug = $slug . '-' . time();
            }
            $stmt_check->close();

            // Simpan ke database
            $sql = "INSERT INTO berita (judul, isi, penulis, kategori, gambar_utama, gambar_2, gambar_3, slug) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("ssssssss", $judul, $isi, $penulis, $kategori, $gambar_utama_db, $gambar_2_db, $gambar_3_db, $slug);
                if ($stmt->execute()) {
                    $_SESSION['success_message'] = "Berita baru berhasil dipublikasikan!";
                    header("Location: kelola_berita.php");
                    exit();
                } else {
                    $errors[] = "Gagal menyimpan data ke database.";
                }
                $stmt->close();
            }
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
    <title>Tambah Berita Baru - Admin MTsN 1 Way Kanan</title>
    
    <!-- Font dan Ikon -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- PERUBAHAN: Link untuk Summernote -->
    <!-- 1. JQuery (wajib untuk Summernote) -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- 2. Bootstrap JS (opsional, tapi disarankan untuk tampilan modal) -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
    <!-- 3. Bootstrap CSS (untuk styling) -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css" rel="stylesheet">
    <!-- 4. Summernote JS & CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote.min.js"></script>

    <style>
        /* CSS Admin Anda (disesuaikan agar tidak bentrok dengan Bootstrap) */
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;z-index: 1040;} /* Tambah z-index */
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
        .img-preview{max-width:200px;height:auto;border-radius:8px;border:2px solid var(--border-color);margin-top:15px;display:none}
        /* Override Summernote panel agar sesuai tema */
        .note-editor.note-frame { border-radius: 8px; border-color: var(--border-color); }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div style="padding: 20px; text-align: center; border-bottom: 1px solid #34495e;"><h3>Admin MTsN 1</h3></div>
        <ul class="sidebar-nav">
             <li class="active"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
             <!-- ... menu lainnya ... -->
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Tulis Berita Baru</h1>
            <a href="kelola_berita.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="tambah_berita.php" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="judul">Judul Berita</label>
                        <input type="text" id="judul" name="judul" class="form-control" placeholder="Masukkan judul berita" required value="<?php echo htmlspecialchars($judul); ?>">
                    </div>
                    <div class="form-group">
                        <label for="kategori">Kategori</label>
                        <input type="text" id="kategori" name="kategori" class="form-control" placeholder="Contoh: Kegiatan, Akademik, Umum" required value="<?php echo htmlspecialchars($kategori); ?>">
                    </div>
                    <div class="form-group">
                        <label for="isi_berita">Isi Berita</label>
                        <!-- Textarea ini akan diubah menjadi Summernote -->
                        <textarea id="isi_berita" name="isi" class="form-control"><?php echo htmlspecialchars($isi); ?></textarea>
                    </div>
                    <!-- Input Gambar Utama (Wajib) -->
                    <div class="form-group">
                        <label for="gambar_utama">Gambar Utama (Wajib)</label>
                        <input type="file" id="gambar_utama" name="gambar_utama" class="form-control" required accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this, 'image-preview-1')">
                        <small>Format: JPG, PNG. Ukuran maks: 2 MB.</small>
                        <!-- PERBAIKAN: ID unik dan style inline ditambahkan -->
                        <img id="image-preview-1" src="#" alt="Pratinjau Gambar 1" class="img-preview" style="max-width: 250px; height: 150px; object-fit: cover; display: none;"/>
                    </div>

                    <!-- Input Gambar 2 (Opsional) -->
                    <div class="form-group">
                        <label for="gambar_2">Gambar Kedua (Opsional)</label>
                        <input type="file" id="gambar_2" name="gambar_2" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this, 'image-preview-2')">
                        <small>Format: JPG, PNG. Ukuran maks: 2 MB.</small>
                        <!-- PERBAIKAN: ID unik dan style inline ditambahkan -->
                        <img id="image-preview-2" src="#" alt="Pratinjau Gambar 2" class="img-preview" style="max-width: 250px; height: 150px; object-fit: cover; display: none;"/>
                    </div>
                    
                    <!-- Input Gambar 3 (Opsional) -->
                    <div class="form-group">
                        <label for="gambar_3">Gambar Ketiga (Opsional)</label>
                        <input type="file" id="gambar_3" name="gambar_3" class="form-control" accept="image/png, image/jpeg, image/jpg" onchange="previewImage(this, 'image-preview-3')">
                        <small>Format: JPG, PNG. Ukuran maks: 2 MB.</small>
                        <!-- PERBAIKAN: ID unik dan style inline ditambahkan -->
                        <img id="image-preview-3" src="#" alt="Pratinjau Gambar 3" class="img-preview" style="max-width: 250px; height: 150px; object-fit: cover; display: none;"/>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-paper-plane"></i> Publikasikan Berita</button>
                </form>
            </div>
        </section>
    </main>

    <script>
        // PERUBAHAN: Inisialisasi Summernote
        $(document).ready(function() {
            $('#isi_berita').summernote({
                height: 300,                 // atur tinggi editor
                minHeight: null,             // tinggi minimum
                maxHeight: null,             // tinggi maksimum
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

    
        function previewImage(input, previewId) {
            const imagePreview = document.getElementById(previewId);
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                imagePreview.style.display = 'block';
                reader.onload = function(e) {
                    imagePreview.setAttribute('src', e.target.result);
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                imagePreview.style.display = 'none';
                imagePreview.setAttribute('src', '#');
            }
        }
    </script>
</body>
</html>
