<?php
session_start();
require 'koneksi.php';

// --- FUNGSI UNTUK MEMBUAT SLUG ---
function create_slug($string) {
    $slug = preg_replace('/[^A-Za-z0-9-]+/', '-', strtolower($string));
    return $slug;
}

// --- FUNGSI UNTUK MENGATUR ALERT SESSION ---
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type, // 'success' or 'error'
        'message' => $message
    ];
}

// --- FUNGSI UNTUK REDIRECT ---
function redirect($url) {
    header("Location: $url");
    exit();
}


// --- LOGIKA PROSES DATA (CREATE, UPDATE, DELETE) ---
$upload_dir = 'uploads/'; // Pastikan folder ini ada dan writable

// Proses Tambah Berita
if (isset($_POST['tambah_berita'])) {
    $judul = trim($_POST['judul']);
    $isi = $_POST['isi']; 
    $id_admin = $_SESSION['admin_id'];
    $slug = create_slug($judul);
    
    $gambar = $_FILES['gambar']['name'];
    $target_file = $upload_dir . basename($gambar);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $unique_gambar = uniqid() . '.' . $imageFileType;
    $target_file_unique = $upload_dir . $unique_gambar;

    $check = getimagesize($_FILES['gambar']['tmp_name']);
    if($check !== false) {
        if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file_unique)) {
            $stmt = $koneksi->prepare("INSERT INTO tb_berita (judul, isi, slug, gambar, id_admin) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssi", $judul, $isi, $slug, $unique_gambar, $id_admin);
            if ($stmt->execute()) {
                set_alert('success', 'Berita berhasil ditambahkan.');
            } else {
                set_alert('error', 'Gagal menambahkan berita.');
            }
            $stmt->close();
        } else {
            set_alert('error', 'Gagal mengupload gambar utama.');
        }
    } else {
        set_alert('error', 'File yang diupload bukan gambar.');
    }
    redirect('kelola_berita.php');
}

// Proses Edit Berita
else if (isset($_POST['edit_berita'])) {
    $id_berita = $_POST['id_berita'];
    $judul = trim($_POST['judul']);
    $isi = $_POST['isi'];
    $gambar_lama = $_POST['gambar_lama'];
    $slug = create_slug($judul);
    
    $nama_gambar_db = $gambar_lama;
    $upload_error = false;

    if (!empty($_FILES['gambar']['name'])) {
        $gambar_baru = $_FILES['gambar']['name'];
        $target_file = $upload_dir . basename($gambar_baru);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $unique_gambar_baru = uniqid() . '.' . $imageFileType;
        $target_file_unique_baru = $upload_dir . $unique_gambar_baru;

        $check = getimagesize($_FILES['gambar']['tmp_name']);
        if($check !== false) {
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $target_file_unique_baru)) {
                $nama_gambar_db = $unique_gambar_baru;
                if (file_exists($upload_dir . $gambar_lama)) {
                    unlink($upload_dir . $gambar_lama);
                }
            } else {
                 set_alert('error', 'Gagal mengupload gambar baru.');
                 $upload_error = true;
            }
        } else {
            set_alert('error', 'File baru bukan gambar.');
            $upload_error = true;
        }
    }

    if (!$upload_error) {
        $stmt = $koneksi->prepare("UPDATE tb_berita SET judul = ?, isi = ?, slug = ?, gambar = ? WHERE id_berita = ?");
        $stmt->bind_param("ssssi", $judul, $isi, $slug, $nama_gambar_db, $id_berita);

        if ($stmt->execute()) {
            set_alert('success', 'Berita berhasil diperbarui.');
        } else {
            set_alert('error', 'Gagal memperbarui berita.');
        }
        $stmt->close();
    }
    redirect('kelola_berita.php');
}

// Proses Hapus Berita
else if (isset($_GET['hapus'])) {
    $id_berita = $_GET['hapus'];
    
    $stmt_select = $koneksi->prepare("SELECT gambar FROM tb_berita WHERE id_berita = ?");
    $stmt_select->bind_param("i", $id_berita);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $row = $result_select->fetch_assoc();
    $gambar_lama = $row['gambar'];
    $stmt_select->close();

    $stmt_delete = $koneksi->prepare("DELETE FROM tb_berita WHERE id_berita = ?");
    $stmt_delete->bind_param("i", $id_berita);
    if ($stmt_delete->execute()) {
        if (file_exists($upload_dir . $gambar_lama)) {
            unlink($upload_dir . $gambar_lama);
        }
        set_alert('success', 'Berita berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal menghapus berita.');
    }
    $stmt_delete->close();
    redirect('kelola_berita.php');
}


require 'sidebar.php'; 

$query_berita = "SELECT b.id_berita, b.judul, b.isi, b.tanggal_publish, b.gambar, a.nama_lengkap 
                 FROM tb_berita b 
                 JOIN tb_admin a ON b.id_admin = a.id_admin 
                 ORDER BY b.tanggal_publish DESC";
$result_berita = $koneksi->query($query_berita);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Berita</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <!-- [BARU] SweetAlert2 CSS (jika ada, biasanya ter-bundle di JS) -->
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-page h5 { margin: 0; font-weight: 600; font-size: 1.5rem; }
        .content-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .berita-img-thumb { width: 100px; height: 60px; object-fit: cover; border-radius: 5px; }
        .note-editor.note-frame { border-radius: 0.375rem; border: 1px solid #dee2e6; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } #menu-toggle { display: block !important; } }
    </style>
</head>
<body>

    <main class="main-content" id="main-content">
        <header class="header-page">
            <div class="d-flex align-items-center">
                <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
                <h5>Kelola Berita</h5>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahBeritaModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Berita
            </button>
        </header>
        
        <!-- [HAPUS] PHP echo $pesan; tidak diperlukan lagi -->

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Gambar</th>
                            <th scope="col">Judul Berita</th>
                            <th scope="col">Penulis</th>
                            <th scope="col">Tanggal</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result_berita->num_rows > 0): ?>
                        <?php while($berita = $result_berita->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo $upload_dir . htmlspecialchars($berita['gambar']); ?>" alt="Gambar Berita" class="berita-img-thumb"></td>
                            <td><?php echo htmlspecialchars($berita['judul']); ?></td>
                            <td><?php echo htmlspecialchars($berita['nama_lengkap']); ?></td>
                            <td><?php echo date('d M Y', strtotime($berita['tanggal_publish'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editBeritaModal"
                                        data-id="<?php echo $berita['id_berita']; ?>"
                                        data-judul="<?php echo htmlspecialchars($berita['judul']); ?>"
                                        data-isi="<?php echo htmlspecialchars($berita['isi']); ?>"
                                        data-gambar-lama="<?php echo $berita['gambar']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <!-- [MODIFIKASI] Tombol Hapus -->
                                <a href="kelola_berita.php?hapus=<?php echo $berita['id_berita']; ?>" class="btn btn-sm btn-danger delete-btn">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada berita.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Berita (Tidak Berubah) -->
    <div class="modal fade" id="tambahBeritaModal" tabindex="-1" aria-labelledby="tambahBeritaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahBeritaModalLabel">Tambah Berita Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_berita.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="judul" class="form-label">Judul Berita</label>
                            <input type="text" class="form-control" id="judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="gambar" class="form-label">Gambar Utama (Thumbnail)</label>
                            <input type="file" class="form-control" id="gambar" name="gambar" accept="image/*" required>
                        </div>
                        <div class="mb-3">
                            <label for="summernote-editor" class="form-label">Isi Berita</label>
                            <textarea class="form-control" id="summernote-editor" name="isi" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_berita" class="btn btn-primary">Publikasikan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Berita (Tidak Berubah) -->
    <div class="modal fade" id="editBeritaModal" tabindex="-1" aria-labelledby="editBeritaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBeritaModalLabel">Edit Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_berita.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_berita" id="edit_id_berita">
                        <input type="hidden" name="gambar_lama" id="edit_gambar_lama">
                        <div class="mb-3">
                            <label for="edit_judul" class="form-label">Judul Berita</label>
                            <input type="text" class="form-control" id="edit_judul" name="judul" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_gambar" class="form-label">Gambar Utama (Thumbnail)</label>
                            <br><img src="" id="preview_gambar_lama" class="berita-img-thumb mb-2">
                            <input type="file" class="form-control" id="edit_gambar" name="gambar" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah gambar.</div>
                        </div>
                        <div class="mb-3">
                            <label for="edit_summernote-editor" class="form-label">Isi Berita</label>
                            <textarea class="form-control" id="edit_summernote-editor" name="isi" required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_berita" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Summernote JS -->
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <!-- [BARU] SweetAlert2 JS -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // [BARU] Logika untuk menampilkan notifikasi Toast
            <?php
            if (isset($_SESSION['alert'])) {
                $alert = $_SESSION['alert'];
                echo "
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });

                Toast.fire({
                    icon: '{$alert['type']}',
                    title: '{$alert['message']}'
                });
                ";
                unset($_SESSION['alert']); // Hapus session setelah ditampilkan
            }
            ?>

            document.getElementById('menu-toggle').addEventListener('click', function() {
                document.getElementById('sidebar').classList.toggle('active');
            });

            function initSummernote(selector) {
                $(selector).summernote({
                    placeholder: 'Tulis isi berita di sini...',
                    tabsize: 2,
                    height: 300,
                    toolbar: [
                        ['style', ['style']],
                        ['font', ['bold', 'italic', 'underline', 'clear']],
                        ['color', ['color']],
                        ['para', ['ul', 'ol', 'paragraph']],
                        ['table', ['table']],
                        ['insert', ['link', 'picture', 'video']],
                        ['view', ['fullscreen', 'codeview', 'help']]
                    ],
                    callbacks: {
                        onImageUpload: function(files) {
                            uploadImage(files[0], $(this));
                        }
                    }
                });
            }

            function uploadImage(file, editor) {
                let data = new FormData();
                data.append("file", file);
                $.ajax({
                    url: 'upload_gambar_editor.php',
                    method: 'POST',
                    data: data,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        let data = JSON.parse(response);
                        if(data.url) {
                            editor.summernote('insertImage', data.url);
                        } else {
                            alert(data.error);
                        }
                    },
                    error: function() {
                        alert('Gagal mengupload gambar.');
                    }
                });
            }

            initSummernote('#summernote-editor');
            initSummernote('#edit_summernote-editor');
            
            $('#editBeritaModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                
                var id = button.data('id');
                var judul = button.data('judul');
                var isi = button.data('isi');
                var gambarLama = button.data('gambar-lama');
                
                var modal = $(this);
                modal.find('.modal-body #edit_id_berita').val(id);
                modal.find('.modal-body #edit_judul').val(judul);
                modal.find('.modal-body #edit_summernote-editor').summernote('code', isi);
                modal.find('.modal-body #edit_gambar_lama').val(gambarLama);
                modal.find('.modal-body #preview_gambar_lama').attr('src', '<?php echo $upload_dir; ?>' + gambarLama);
            });

            // [BARU] Logika untuk konfirmasi hapus
            $('.delete-btn').on('click', function(e) {
                e.preventDefault(); // Mencegah link berjalan langsung
                const href = $(this).attr('href');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data berita yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Jika user menekan "Ya, hapus!", maka redirect ke link hapus
                        document.location.href = href;
                    }
                });
            });
        });
    </script>
</body>
</html>
