<?php
session_start();
require 'koneksi.php';

// --- LOGIKA UNTUK PROSES DATA (CREATE, UPDATE, DELETE) ---
$pesan = '';

// Proses Tambah Admin
if (isset($_POST['tambah_admin'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    $stmt_check = $koneksi->prepare("SELECT id_admin FROM tb_admin WHERE username = ?");
    $stmt_check->bind_param("s", $username);
    $stmt_check->execute();
    $stmt_check->store_result();

    if ($stmt_check->num_rows > 0) {
        $pesan = "<div class='alert alert-danger'>Username sudah ada. Gagal menambahkan admin.</div>";
    } else {
        $stmt = $koneksi->prepare("INSERT INTO tb_admin (nama_lengkap, username, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $nama_lengkap, $username, $hashed_password);
        if ($stmt->execute()) {
            $pesan = "<div class='alert alert-success'>Admin baru berhasil ditambahkan.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menambahkan admin.</div>";
        }
        $stmt->close();
    }
    $stmt_check->close();
}

// Proses Edit Admin
if (isset($_POST['edit_admin'])) {
    $id_admin = $_POST['id_admin'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    // Jika password diisi, update password. Jika tidak, jangan update password.
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $koneksi->prepare("UPDATE tb_admin SET nama_lengkap = ?, username = ?, password = ? WHERE id_admin = ?");
        $stmt->bind_param("sssi", $nama_lengkap, $username, $hashed_password, $id_admin);
    } else {
        $stmt = $koneksi->prepare("UPDATE tb_admin SET nama_lengkap = ?, username = ? WHERE id_admin = ?");
        $stmt->bind_param("ssi", $nama_lengkap, $username, $id_admin);
    }
    
    if ($stmt->execute()) {
        $pesan = "<div class='alert alert-success'>Data admin berhasil diperbarui.</div>";
    } else {
        $pesan = "<div class='alert alert-danger'>Gagal memperbarui data admin.</div>";
    }
    $stmt->close();
}

// Proses Hapus Admin
if (isset($_GET['hapus'])) {
    $id_admin = $_GET['hapus'];
    // Pencegahan agar admin tidak bisa menghapus akunnya sendiri
    if ($id_admin == $_SESSION['admin_id']) {
        $pesan = "<div class='alert alert-warning'>Anda tidak dapat menghapus akun Anda sendiri.</div>";
    } else {
        $stmt = $koneksi->prepare("DELETE FROM tb_admin WHERE id_admin = ?");
        $stmt->bind_param("i", $id_admin);
        if ($stmt->execute()) {
            $pesan = "<div class='alert alert-success'>Admin berhasil dihapus.</div>";
        } else {
            $pesan = "<div class='alert alert-danger'>Gagal menghapus admin.</div>";
        }
        $stmt->close();
    }
}


// Memanggil sidebar setelah logika proses data agar session tetap valid
require 'sidebar.php'; 

// Mengambil semua data admin untuk ditampilkan
$result_admin = $koneksi->query("SELECT id_admin, nama_lengkap, username FROM tb_admin ORDER BY nama_lengkap ASC");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* Menggunakan variabel dari sidebar.php */
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg-color);
            color: var(--text-dark);
        }

        .main-content {
            margin-left: var(--sidebar-width);
            padding: 30px;
            transition: margin-left 0.3s ease;
        }

        .header-page {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .header-page h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.5rem;
        }
        
        .content-card {
            background-color: white;
            border-radius: 12px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }

        /* Responsive */
        @media (max-width: 992px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            #menu-toggle { display: block !important; }
        }
    </style>
</head>
<body>

    <main class="main-content" id="main-content">
        <header class="header-page">
            <div class="d-flex align-items-center">
                 <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
                <h5>Kelola Admin</h5>
            </div>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahAdminModal">
                <i class="bi bi-plus-circle me-2"></i>Tambah Admin
            </button>
        </header>
        
        <?php echo $pesan; ?>

        <div class="content-card">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead>
                        <tr>
                            <th scope="col">#</th>
                            <th scope="col">Nama Lengkap</th>
                            <th scope="col">Username</th>
                            <th scope="col">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $no = 1; while($admin = $result_admin->fetch_assoc()): ?>
                        <tr>
                            <th scope="row"><?php echo $no++; ?></th>
                            <td><?php echo htmlspecialchars($admin['nama_lengkap']); ?></td>
                            <td><?php echo htmlspecialchars($admin['username']); ?></td>
                            <td>
                                <button class="btn btn-sm btn-warning edit-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#editAdminModal"
                                    data-id="<?php echo $admin['id_admin']; ?>"
                                    data-nama="<?php echo htmlspecialchars($admin['nama_lengkap']); ?>"
                                    data-username="<?php echo htmlspecialchars($admin['username']); ?>">
                                    <i class="bi bi-pencil-square"></i> Edit
                                </button>
                                <a href="kelola_admin.php?hapus=<?php echo $admin['id_admin']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Apakah Anda yakin ingin menghapus admin ini?')">
                                    <i class="bi bi-trash-fill"></i> Hapus
                                </a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <!-- Modal Tambah Admin -->
    <div class="modal fade" id="tambahAdminModal" tabindex="-1" aria-labelledby="tambahAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="tambahAdminModalLabel">Tambah Admin Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_admin.php">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="tambah_admin" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Edit Admin -->
    <div class="modal fade" id="editAdminModal" tabindex="-1" aria-labelledby="editAdminModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editAdminModalLabel">Edit Data Admin</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST" action="kelola_admin.php">
                    <div class="modal-body">
                        <input type="hidden" id="edit_id_admin" name="id_admin">
                        <div class="mb-3">
                            <label for="edit_nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="edit_nama_lengkap" name="nama_lengkap" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="edit_username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="edit_password" class="form-label">Password Baru (Opsional)</label>
                            <input type="password" class="form-control" id="edit_password" name="password">
                            <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="edit_admin" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script untuk toggle sidebar di mobile
        document.getElementById('menu-toggle').addEventListener('click', function() {
            document.getElementById('sidebar').classList.toggle('active');
        });

        // Script untuk mengisi data ke modal edit
        const editAdminModal = document.getElementById('editAdminModal');
        editAdminModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const id = button.getAttribute('data-id');
            const nama = button.getAttribute('data-nama');
            const username = button.getAttribute('data-username');
            
            const modalBodyInputId = editAdminModal.querySelector('#edit_id_admin');
            const modalBodyInputNama = editAdminModal.querySelector('#edit_nama_lengkap');
            const modalBodyInputUsername = editAdminModal.querySelector('#edit_username');

            modalBodyInputId.value = id;
            modalBodyInputNama.value = nama;
            modalBodyInputUsername.value = username;
        });
    </script>
</body>
</html>
