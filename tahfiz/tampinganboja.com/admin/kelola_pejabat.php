<?php
session_start();
require 'koneksi.php';

// --- FUNGSI BANTUAN ---
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// --- LOGIKA PROSES DATA ---
$upload_dir = 'uploads/pejabat/'; // Folder khusus untuk foto pejabat
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// Proses Tambah Pejabat
if (isset($_POST['tambah_pejabat'])) {
    $nama_pejabat = trim($_POST['nama_pejabat']);
    $jabatan = trim($_POST['jabatan']);
    $urutan = (int)$_POST['urutan'];
    
    if (empty($_FILES['foto']['name'])) {
        set_alert('error', 'Foto wajib diisi.');
        redirect('kelola_pejabat.php');
    }

    $foto = $_FILES['foto']['name'];
    $target_file = $upload_dir . basename($foto);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    $unique_foto = uniqid() . '.' . $imageFileType;
    $target_file_unique = $upload_dir . $unique_foto;

    $check = getimagesize($_FILES['foto']['tmp_name']);
    if($check !== false) {
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file_unique)) {
            $stmt = $koneksi->prepare("INSERT INTO tb_pejabat (nama_pejabat, jabatan, foto, urutan) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sssi", $nama_pejabat, $jabatan, $unique_foto, $urutan);
            if ($stmt->execute()) {
                set_alert('success', 'Data pejabat berhasil ditambahkan.');
            } else {
                set_alert('error', 'Gagal menambahkan data pejabat.');
            }
            $stmt->close();
        } else {
            set_alert('error', 'Gagal mengupload foto.');
        }
    } else {
        set_alert('error', 'File yang diupload bukan gambar.');
    }
    redirect('kelola_pejabat.php');
}

// Proses Edit Pejabat
else if (isset($_POST['edit_pejabat'])) {
    $id_pejabat = $_POST['id_pejabat'];
    $nama_pejabat = trim($_POST['nama_pejabat']);
    $jabatan = trim($_POST['jabatan']);
    $urutan = (int)$_POST['urutan'];
    $foto_lama = $_POST['foto_lama'];
    
    $nama_foto_db = $foto_lama;
    $upload_error = false;

    // Cek jika ada foto baru diupload
    if (!empty($_FILES['foto']['name'])) {
        $foto_baru = $_FILES['foto']['name'];
        $target_file = $upload_dir . basename($foto_baru);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $unique_foto_baru = uniqid() . '.' . $imageFileType;
        $target_file_unique_baru = $upload_dir . $unique_foto_baru;

        $check = getimagesize($_FILES['foto']['tmp_name']);
        if($check !== false) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file_unique_baru)) {
                $nama_foto_db = $unique_foto_baru;
                // Hapus foto lama jika ada
                if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
                    unlink($upload_dir . $foto_lama);
                }
            } else {
                 set_alert('error', 'Gagal mengupload foto baru.');
                 $upload_error = true;
            }
        } else {
            set_alert('error', 'File baru bukan gambar.');
            $upload_error = true;
        }
    }

    if (!$upload_error) {
        $stmt = $koneksi->prepare("UPDATE tb_pejabat SET nama_pejabat = ?, jabatan = ?, foto = ?, urutan = ? WHERE id_pejabat = ?");
        $stmt->bind_param("sssii", $nama_pejabat, $jabatan, $nama_foto_db, $urutan, $id_pejabat);

        if ($stmt->execute()) {
            set_alert('success', 'Data pejabat berhasil diperbarui.');
        } else {
            set_alert('error', 'Gagal memperbarui data pejabat.');
        }
        $stmt->close();
    }
    redirect('kelola_pejabat.php');
}

// Proses Hapus Pejabat
else if (isset($_GET['hapus'])) {
    $id_pejabat = $_GET['hapus'];
    
    $stmt_select = $koneksi->prepare("SELECT foto FROM tb_pejabat WHERE id_pejabat = ?");
    $stmt_select->bind_param("i", $id_pejabat);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $row = $result_select->fetch_assoc();
    $foto_lama = $row['foto'];
    $stmt_select->close();

    $stmt_delete = $koneksi->prepare("DELETE FROM tb_pejabat WHERE id_pejabat = ?");
    $stmt_delete->bind_param("i", $id_pejabat);
    if ($stmt_delete->execute()) {
        if (!empty($foto_lama) && file_exists($upload_dir . $foto_lama)) {
            unlink($upload_dir . $foto_lama);
        }
        set_alert('success', 'Data pejabat berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal menghapus data pejabat.');
    }
    $stmt_delete->close();
    redirect('kelola_pejabat.php');
}

require 'sidebar.php'; 

// Mengambil semua data pejabat
$query_pejabat = "SELECT id_pejabat, nama_pejabat, jabatan, foto, urutan FROM tb_pejabat ORDER BY urutan ASC, nama_pejabat ASC";
$result_pejabat = $koneksi->query($query_pejabat);

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pejabat</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-page h5 { margin: 0; font-weight: 600; font-size: 1.5rem; }
        .content-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .pejabat-img-thumb { width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 2px solid #eee; }
        .preview-img { max-width: 100px; max-height: 100px; object-fit: cover; border-radius: 8px; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } #menu-toggle { display: block !important; } }
    </style>
</head>
<body>

    <main class="main-content" id="main-content">
        <header class="header-page">
            <div class="d-flex align-items-center">
                <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
                <h5>Kelola Pejabat</h5>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahPejabatModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Pejabat
            </button>
        </header>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">Foto</th>
                            <th scope="col">Nama Pejabat</th>
                            <th scope="col">Jabatan</th>
                            <th scope="col">Urutan</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if($result_pejabat->num_rows > 0): ?>
                        <?php while($pejabat = $result_pejabat->fetch_assoc()): ?>
                        <tr>
                            <td><img src="<?php echo $upload_dir . htmlspecialchars($pejabat['foto']); ?>" alt="Foto <?php echo htmlspecialchars($pejabat['nama_pejabat']); ?>" class="pejabat-img-thumb"></td>
                            <td><?php echo htmlspecialchars($pejabat['nama_pejabat']); ?></td>
                            <td><?php echo htmlspecialchars($pejabat['jabatan']); ?></td>
                            <td><?php echo $pejabat['urutan']; ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" 
                                        data-bs-toggle="modal" 
                                        data-bs-target="#editPejabatModal"
                                        data-id="<?php echo $pejabat['id_pejabat']; ?>"
                                        data-nama="<?php echo htmlspecialchars($pejabat['nama_pejabat']); ?>"
                                        data-jabatan="<?php echo htmlspecialchars($pejabat['jabatan']); ?>"
                                        data-urutan="<?php echo $pejabat['urutan']; ?>"
                                        data-foto="<?php echo $pejabat['foto']; ?>">
                                    <i class="bi bi-pencil-square"></i>
                                </button>
                                <a href="kelola_pejabat.php?hapus=<?php echo $pejabat['id_pejabat']; ?>" class="btn btn-sm btn-danger delete-btn">
                                    <i class="bi bi-trash-fill"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center">Belum ada data pejabat.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Pejabat -->
    <div class="modal fade" id="tambahPejabatModal" tabindex="-1" aria-labelledby="tambahPejabatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahPejabatModalLabel">Tambah Pejabat Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_pejabat.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_pejabat" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_pejabat" name="nama_pejabat" required>
                        </div>
                        <div class="mb-3">
                            <label for="jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="jabatan" name="jabatan" required>
                        </div>
                        <div class="mb-3">
                            <label for="urutan" class="form-label">Nomor Urut Tampil</label>
                            <input type="number" class="form-control" id="urutan" name="urutan" value="0" required>
                        </div>
                        <div class="mb-3">
                            <label for="foto" class="form-label">Foto</label>
                            <input type="file" class="form-control" id="foto" name="foto" accept="image/*" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_pejabat" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Modal Edit Pejabat -->
    <div class="modal fade" id="editPejabatModal" tabindex="-1" aria-labelledby="editPejabatModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPejabatModalLabel">Edit Data Pejabat</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_pejabat.php" enctype="multipart/form-data">
                    <div class="modal-body">
                        <input type="hidden" name="id_pejabat" id="edit_id_pejabat">
                        <input type="hidden" name="foto_lama" id="edit_foto_lama">
                        <div class="mb-3">
                            <label for="edit_nama_pejabat" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama_pejabat" name="nama_pejabat" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_jabatan" class="form-label">Jabatan</label>
                            <input type="text" class="form-control" id="edit_jabatan" name="jabatan" required>
                        </div>
                         <div class="mb-3">
                            <label for="edit_urutan" class="form-label">Nomor Urut Tampil</label>
                            <input type="number" class="form-control" id="edit_urutan" name="urutan" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_foto" class="form-label">Foto</label><br>
                            <img src="" id="preview_foto_lama" class="preview-img mb-2">
                            <input type="file" class="form-control" id="edit_foto" name="foto" accept="image/*">
                            <div class="form-text">Kosongkan jika tidak ingin mengubah foto.</div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_pejabat" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        $(document).ready(function() {
            // Logika untuk menampilkan notifikasi Toast
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
                unset($_SESSION['alert']);
            }
            ?>

            // Toggle sidebar
            $('#menu-toggle').on('click', function() {
                $('#sidebar').toggleClass('active');
            });

            // Mengisi data ke modal edit
            $('#editPejabatModal').on('show.bs.modal', function (event) {
                var button = $(event.relatedTarget);
                
                var id = button.data('id');
                var nama = button.data('nama');
                var jabatan = button.data('jabatan');
                var urutan = button.data('urutan');
                var foto = button.data('foto');
                
                var modal = $(this);
                modal.find('.modal-body #edit_id_pejabat').val(id);
                modal.find('.modal-body #edit_nama_pejabat').val(nama);
                modal.find('.modal-body #edit_jabatan').val(jabatan);
                modal.find('.modal-body #edit_urutan').val(urutan);
                modal.find('.modal-body #edit_foto_lama').val(foto);
                modal.find('.modal-body #preview_foto_lama').attr('src', '<?php echo $upload_dir; ?>' + foto);
            });

            // Logika untuk konfirmasi hapus
            $('.delete-btn').on('click', function(e) {
                e.preventDefault();
                const href = $(this).attr('href');

                Swal.fire({
                    title: 'Apakah Anda yakin?',
                    text: "Data yang dihapus tidak dapat dikembalikan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Ya, hapus!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.location.href = href;
                    }
                });
            });
        });
    </script>
</body>
</html>
