<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}
// 2. Cek apakah rolenya adalah 'superadmin'
if (!in_array($_SESSION['role'], ['superadmin', 'developer'])) {
    // PERUBAHAN: Mengganti die() dengan halaman HTML yang terdesain
    http_response_code(403); // Set status HTTP ke 403 Forbidden
    echo <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akses Ditolak</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --danger-color: #e74c3c; --main-bg: #f4f7f6; --text-color: #333; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--main-bg);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            color: var(--text-color);
        }
        .access-denied-container {
            text-align: center;
            background-color: #fff;
            padding: 40px 50px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .icon-wrapper i {
            font-size: 60px;
            color: var(--danger-color);
            margin-bottom: 20px;
        }
        h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 10px;
        }
        p {
            font-size: 16px;
            color: #666;
            margin-bottom: 30px;
        }
        .btn-back {
            background-color: #6c757d;
            color: #fff;
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: background-color 0.3s ease;
        }
        .btn-back:hover {
            background-color: #5a6268;
        }
    </style>
</head>
<body>
    <div class="access-denied-container">
        <div class="icon-wrapper">
            <i class="fas fa-ban"></i>
        </div>
        <h1>Akses Ditolak</h1>
        <p>Anda tidak memiliki izin untuk menghapus atau mengedit Admin <br> Jika menurut anda tindakan ini perlu dilakukan, silahkan hubungi Kepala Madrasah <br> atau hubungi Pengembang via WhatsApp 082371869118 (Riski Nurhadi).</p>
        <a href="kelola_operator.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
    </div>
</body>
</html>
HTML;
    exit(); // Hentikan eksekusi skrip setelah menampilkan halaman
}

// Cek ID dari URL
if (!isset($_GET['id']) || !filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    header("Location: kelola_operator.php");
    exit();
}
$id = $_GET['id'];

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'operator';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

$errors = [];

// --- PROSES UPDATE SAAT FORM DI-SUBMIT (METHOD POST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $status = $_POST['status'];
    $password = $_POST['password'];
    $current_foto = $_POST['current_foto'];
    
    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($role) || empty($status)) {
        $errors[] = "Semua field kecuali password dan foto profil wajib diisi.";
    }

    $new_file_name = $current_foto;
    if (isset($_FILES['foto_profil']) && $_FILES['foto_profil']['error'] == 0) {
        $target_dir = "uploads/operator/";
        $allowed_types = ['jpg', 'jpeg', 'png'];
        $max_file_size = 2 * 1024 * 1024;

        $file_ext = strtolower(pathinfo($_FILES['foto_profil']['name'], PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) { $errors[] = "Format foto tidak diizinkan."; }
        if ($_FILES['foto_profil']['size'] > $max_file_size) { $errors[] = "Ukuran foto tidak boleh lebih dari 2 MB."; }

        if (empty($errors)) {
            $new_file_name = uniqid('operator_', true) . '.' . $file_ext;
            if (move_uploaded_file($_FILES['foto_profil']['tmp_name'], $target_dir . $new_file_name)) {
                if ($current_foto !== 'default.png' && file_exists($target_dir . $current_foto)) {
                    unlink($target_dir . $current_foto);
                }
            } else {
                $errors[] = "Gagal mengunggah foto baru.";
            }
        }
    }
    
    if (empty($errors)) {
        $sql_update = "UPDATE operator_madrasah SET nama_lengkap = ?, username = ?, email = ?, role = ?, status = ?, foto_profil = ?";
        $params = [$nama_lengkap, $username, $email, $role, $status, $new_file_name];
        $types = "ssssss";
        
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_update .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        $sql_update .= " WHERE id = ?";
        $params[] = $id;
        $types .= "i";
        
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($types, ...$params);

        if ($stmt_update->execute()) {
            if ($id == $_SESSION['operator_id']) {
                $_SESSION['nama_lengkap'] = $nama_lengkap;
                $_SESSION['foto_profil'] = $new_file_name;
                $_SESSION['role'] = $role;
            }
            $_SESSION['success_message'] = "Data operator berhasil diperbarui.";
            header("Location: kelola_operator.php");
            exit();
        } else {
            $errors[] = "Gagal memperbarui data di database.";
        }
        $stmt_update->close();
    }
}

// --- AMBIL DATA SAAT INI UNTUK DITAMPILKAN DI FORM (METHOD GET) ---
$sql_select = "SELECT * FROM operator_madrasah WHERE id = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows == 1) {
        $op = $result->fetch_assoc();
    } else {
        $_SESSION['error_message'] = "Operator tidak ditemukan.";
        header("Location: kelola_operator.php");
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
    <title>Edit Operator - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}.sidebar-header h3{font-weight:600;color:#fff}.sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}.sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}.sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600}.btn-back{background-color:#6c757d;color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;display:inline-flex;align-items:center;gap:8px}.form-card{background-color:#fff;padding:30px;border-radius:12px;box-shadow:var(--card-shadow)}.form-grid{display:grid;grid-template-columns:1fr 1fr;gap:25px}.form-group{margin-bottom:25px}.form-group.full-width{grid-column:1/-1}.form-group label{display:block;margin-bottom:8px;font-weight:500;color:#555}.form-group input[type=text],.form-group input[type=email],.form-group input[type=password],.form-group select,.form-group input[type=file]{width:100%;padding:12px 15px;border:1px solid var(--border-color);border-radius:8px;font-size:15px;font-family:'Poppins',sans-serif}.form-group input:focus,select:focus{outline:0;border-color:var(--primary-color)}.btn-submit{background-color:var(--primary-color);color:#fff;padding:12px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}.sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}.sidebar-dropdown.open .dropdown-menu{display:block}.dropdown-menu li a{padding-left:65px}.dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}.sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}.notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}.current-image{width:100px;height:100px;object-fit:cover;border-radius:50%;border:3px solid var(--border-color);margin-top:10px}
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'calon_siswa') ? 'active' : ''; ?>"><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'guru') ? 'active' : ''; ?>"><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'testimoni') ? 'active' : ''; ?>"><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'berita') ? 'active' : ''; ?>"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'prestasi') ? 'active' : ''; ?>"><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'tata_tertib') ? 'active' : ''; ?>"><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'struktural') ? 'active' : ''; ?>"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'galeri') ? 'active' : ''; ?>"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, ['pesan', 'operator', 'pengaturan']) ? 'active open' : ''; ?>">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">
                        <a href="kelola_kontak_pesan.php">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Masuk</span>
                            <?php if ($jumlah_pesan_baru > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Edit Data Operator</h1>
            <a href="kelola_operator.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
        </header>

        <section class="content">
            <div class="form-card">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <form action="edit_operator.php?id=<?php echo $id; ?>" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                    <input type="hidden" name="current_foto" value="<?php echo htmlspecialchars($op['foto_profil']); ?>">

                    <div class="form-group">
                        <label>Foto Profil Saat Ini</label><br>
                        <img src="uploads/operator/<?php echo htmlspecialchars($op['foto_profil']); ?>" class="current-image">
                    </div>
                    <div class="form-group">
                        <label for="foto_profil">Ganti Foto Profil (Opsional)</label>
                        <input type="file" id="foto_profil" name="foto_profil" accept="image/png, image/jpeg, image/jpg">
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="nama_lengkap">Nama Lengkap</label>
                            <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($op['nama_lengkap']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($op['username']); ?>" required>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($op['email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password Baru (Opsional)</label>
                        <input type="password" id="password" name="password" placeholder="Kosongkan jika tidak ingin diubah">
                    </div>
                     <div class="form-grid">
                        <div class="form-group">
                            <label for="role">Role</label>
                            <select id="role" name="role" required>
                                <option value="operator" <?php echo ($op['role'] == 'operator') ? 'selected' : ''; ?>>Operator</option>
                                <option value="superadmin" <?php echo ($op['role'] == 'superadmin') ? 'selected' : ''; ?>>Super Admin</option>
                                <!--<option value="developer" <?php echo ($op['role'] == 'developer') ? 'selected' : ''; ?>>Developer</option>-->
                            </select>
                        </div>
                         <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="aktif" <?php echo ($op['status'] == 'aktif') ? 'selected' : ''; ?>>Aktif</option>
                                <option value="tidak_aktif" <?php echo ($op['status'] == 'tidak_aktif') ? 'selected' : ''; ?>>Tidak Aktif</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn-submit"><i class="fas fa-sync-alt"></i> Perbarui Data</button>
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
