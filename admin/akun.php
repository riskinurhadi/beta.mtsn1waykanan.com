<?php
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'akun';
$operator_id = $_SESSION['operator_id'];

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

// --- PERUBAHAN: Ambil notifikasi yang belum dibaca (termasuk ID) ---
$notifikasi_list = [];
$sql_notif = "SELECT n.id, n.pesan, n.tanggal_kirim, o.nama_lengkap as nama_pengirim 
              FROM notifikasi_operator n 
              JOIN operator_madrasah o ON n.id_pengirim = o.id 
              WHERE n.id_penerima = ? AND n.sudah_dibaca = FALSE 
              ORDER BY n.tanggal_kirim DESC";
if($stmt_notif = $conn->prepare($sql_notif)) {
    $stmt_notif->bind_param("i", $operator_id);
    $stmt_notif->execute();
    $result_notif = $stmt_notif->get_result();
    while($row = $result_notif->fetch_assoc()) {
        $notifikasi_list[] = $row;
    }
    $stmt_notif->close();
}


$errors = [];

// --- PROSES UPDATE SAAT FORM DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (Logika update profil Anda tetap sama)
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $no_whatsapp = trim($_POST['no_whatsapp']);
    $password = $_POST['password'];
    $password_konfirmasi = $_POST['password_konfirmasi'];
    $current_foto = $_POST['current_foto'];
    
    if (empty($nama_lengkap) || empty($email)) { $errors[] = "Nama Lengkap dan Email tidak boleh kosong."; }
    if (!empty($password) && $password !== $password_konfirmasi) { $errors[] = "Konfirmasi password baru tidak cocok."; }
    if (!empty($password) && strlen($password) < 8) { $errors[] = "Password baru minimal harus 8 karakter."; }

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
            } else { $errors[] = "Gagal mengunggah foto baru."; }
        }
    }
    
    if (empty($errors)) {
        $sql_update = "UPDATE operator_madrasah SET nama_lengkap = ?, email = ?, no_whatsapp = ?, foto_profil = ?";
        $params = [$nama_lengkap, $email, $no_whatsapp, $new_file_name];
        $types = "ssss";
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_update .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        $sql_update .= " WHERE id = ?";
        $params[] = $operator_id;
        $types .= "i";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param($types, ...$params);
        if ($stmt_update->execute()) {
            $_SESSION['nama_lengkap'] = $nama_lengkap;
            $_SESSION['foto_profil'] = $new_file_name;
            $_SESSION['success_message'] = "Profil Anda berhasil diperbarui.";
            header("Location: akun.php");
            exit();
        } else { $errors[] = "Gagal memperbarui data di database."; }
        $stmt_update->close();
    }
}

// --- AMBIL DATA OPERATOR YANG SEDANG LOGIN ---
$sql_select = "SELECT * FROM operator_madrasah WHERE id = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $operator_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $operator = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--border-color:#e0e0e0}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}.sidebar-header h3{font-weight:600;color:#fff}.sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}.sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}.sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px}.page-header h1{font-size:24px;font-weight:600}.btn-submit{background-color:var(--primary-color);color:#fff;padding:10px 25px;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background-color:var(--primary-hover)}.alert-danger{background-color:#f8d7da;color:#721c24;padding:15px;border:1px solid #f5c6cb;border-radius:8px;margin-bottom:20px}.sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}.sidebar-dropdown.open .dropdown-menu{display:block}.dropdown-menu li a{padding-left:65px}.dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}.sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}.notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
        .profile-layout { display: grid; grid-template-columns: 300px 1fr; gap: 30px; }
        .profile-sidebar, .profile-main { display: flex; flex-direction: column; gap: 30px; }
        .profile-card { background-color: #fff; padding: 25px; border-radius: 12px; box-shadow: var(--card-shadow); }
        .profile-sidebar .profile-card { text-align: center; }
        .profile-pic { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; border: 5px solid #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 15px; }
        .profile-name { font-size: 22px; font-weight: 600; margin: 0; }
        .profile-role { font-size: 15px; color: #777; margin-bottom: 20px; }
        .profile-contact { list-style: none; padding: 0; margin: 0; text-align: left; }
        .profile-contact li { display: flex; align-items: center; margin-bottom: 10px; color: #555; }
        .profile-contact i { width: 30px; font-size: 16px; color: var(--primary-color); }
        .btn-upload { background-color: var(--primary-color); color: #fff; padding: 10px 20px; border-radius: 8px; cursor: pointer; display: inline-block; margin-top: 20px; }
        .info-card-header { display: flex; justify-content: space-between; align-items: center; border-bottom: 1px solid #eee; padding-bottom: 10px; margin-bottom: 20px; }
        .info-card-header h3 { font-size: 18px; font-weight: 600; margin: 0; }
        
        .notification-list { list-style: none; padding: 0; margin: 0; min-height: 150px; }
        .notification-item { padding: 15px; border-bottom: 1px solid #f5f5f5; transition: background-color 0.2s ease; cursor: pointer; }
        .notification-item:hover { background-color: #f9f9f9; }
        .notification-item:last-child { border-bottom: none; }
        .notification-item p { margin: 0; line-height: 1.5; }
        .notification-item .meta { font-size: 12px; color: #999; margin-top: 5px; }
        
        
        /* PERBAIKAN: Menggunakan Grid untuk perataan di Info Akun */
        .info-list { list-style: none; padding: 0; }
        .info-list li { display: grid; grid-template-columns: auto 1fr; gap: 15px; align-items: center; padding: 12px 0; border-bottom: 1px solid #f5f5f5; }
        .info-list li:last-child { border-bottom: none; }
        .info-list .label { color: #777; font-size: 15px; }
        .info-list .value { font-weight: 500; font-size: 15px; text-align: right; }

        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; font-weight: 500; color: #555; }
        .form-group input { width: 100%; padding: 10px 15px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 15px; font-family: 'Poppins', sans-serif; }
        .form-group input:focus { outline:0; border-color:var(--primary-color); }
        .form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .profile-main-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: start;
        }
        
        .notification-list { list-style: none; padding: 0; margin: 0; height: 210px; /* PERBAIKAN: Tinggi minimal untuk notifikasi */ }
        .notification-item { padding: 15px 0; border-bottom: 1px solid #f5f5f5; }
        .notification-item:last-child { border-bottom: none; }
        .notification-item p { margin: 0; line-height: 1.5; }
        .notification-item .meta { font-size: 12px; color: #999; margin-top: 5px; }
        @media (max-width: 1200px) { .profile-layout { grid-template-columns: 1fr; } }
        @media (max-width: 992px) { .profile-main-grid { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>"><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <!--<li class="<?php echo ($halaman_aktif == 'calon_siswa') ? 'active' : ''; ?>"><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>-->
            <li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">
                        <a href="kelola_kontak_pesan.php">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Masuk</span>
                            <?php if ($jumlah_pesan_baru > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </a>
                    </li>
            <li class="<?php echo ($halaman_aktif == 'guru') ? 'active' : ''; ?>"><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'testimoni') ? 'active' : ''; ?>"><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'berita') ? 'active' : ''; ?>"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'prestasi') ? 'active' : ''; ?>"><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'tata_tertib') ? 'active' : ''; ?>"><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'struktural') ? 'active' : ''; ?>"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'galeri') ? 'active' : ''; ?>"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, [ 'operator', 'pengaturan', 'akun']) ? 'active open' : ''; ?>">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <!--<li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">-->
                    <!--    <a href="kelola_kontak_pesan.php">-->
                    <!--        <i class="fas fa-envelope"></i>-->
                    <!--        <span>Pesan Masuk</span>-->
                    <!--        <?php if ($jumlah_pesan_baru > 0): ?>-->
                    <!--            <span class="notification-dot"></span>-->
                    <!--        <?php endif; ?>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <!--<li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>-->
                    <li class="<?php echo ($halaman_aktif == 'akun') ? 'active' : ''; ?>"><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <form action="akun.php" method="post" enctype="multipart/form-data">
            <header class="page-header">
                <h1>Profil Operator</h1>
                <button type="submit" class="btn-submit"><i class="fas fa-save"></i> Simpan Perubahan</button>
            </header>

            <section class="content">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger" style="margin-bottom: 30px;"><strong>Gagal!</strong><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>

                <div class="profile-layout">
                    <!-- Kolom Kiri -->
                    <div class="profile-sidebar">
                        <div class="profile-card">
                            <!-- PERUBAHAN: Menambahkan id pada <img> -->
                            <img id="profilePicPreview" src="uploads/operator/<?php echo htmlspecialchars($operator['foto_profil'] ?? 'default-profile.png'); ?>" alt="Foto Profil" class="profile-pic">
                            <h2 class="profile-name"><?php echo htmlspecialchars($operator['nama_lengkap']); ?></h2>
                            <p class="profile-role"><?php echo htmlspecialchars(ucfirst($operator['role'])); ?></p>
                            <hr>
                            <ul class="profile-contact">
                                <li><i class="fas fa-envelope"></i> <span><?php echo htmlspecialchars($operator['email']); ?></span></li>
                                <li><i class="fab fa-whatsapp"></i> <span><?php echo htmlspecialchars($operator['no_whatsapp'] ?? 'Belum diisi'); ?></span></li>
                            </ul>
                            <label for="foto_profil" class="btn-upload"><i class="fas fa-camera"></i> Ganti Foto</label>
                            <!-- PERUBAHAN: Menambahkan onchange pada <input> -->
                            <input type="file" id="foto_profil" name="foto_profil" accept="image/png, image/jpeg, image/jpg" style="display: none;" onchange="previewProfilePic(this)">
                            <input type="hidden" name="current_foto" value="<?php echo htmlspecialchars($operator['foto_profil']); ?>">
                        </div>

                        <div class="profile-card">
                            <div class="info-card-header"><h3><i class="fas fa-bell" style="margin-right: 10px;"></i>Notifikasi</h3></div>
                        <ul class="notification-list" id="notificationList">
                            <?php if (empty($notifikasi_list)): ?>
                                <li id="no-notification-msg"><p class="meta" style="padding: 15px 0;">Tidak ada notifikasi baru.</p></li>
                            <?php else: ?>
                                <?php foreach ($notifikasi_list as $notif): ?>
                                <li class="notification-item" 
                                    data-id="<?php echo $notif['id']; ?>"
                                    data-pesan="<?php echo htmlspecialchars($notif['pesan']); ?>"
                                    data-pengirim="<?php echo htmlspecialchars($notif['nama_pengirim']); ?>"
                                    data-tanggal="<?php echo date('d F Y, H:i', strtotime($notif['tanggal_kirim'])); ?>"
                                    onclick="showNotificationModal(this)">
                                    <p><?php echo htmlspecialchars(substr($notif['pesan'], 0, 50)) . '...'; ?></p>
                                    <p class="meta">Dari: <?php echo htmlspecialchars($notif['nama_pengirim']); ?></p>
                                </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                        </div>

                    </div>
                    <!-- Kolom Kanan -->
                    <div class="profile-main">
                        <div class="profile-card mb-5">
                            <div class="info-card-header"><h3>Informasi Umum</h3></div>
                            <div class="form-group">
                                <label for="nama_lengkap">Nama Lengkap</label>
                                <input type="text" id="nama_lengkap" name="nama_lengkap" value="<?php echo htmlspecialchars($operator['nama_lengkap']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($operator['email']); ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="no_whatsapp">Nomor WhatsApp</label>
                                <input type="text" id="no_whatsapp" name="no_whatsapp" value="<?php echo htmlspecialchars($operator['no_whatsapp'] ?? ''); ?>">
                            </div>
                        </div>
                        
                        <div class="profile-main-grid">
                            <div class="profile-card " style="height: 350px;">
                                <div class="info-card-header"><h3>Info Akun</h3></div>
                                <ul class="info-list">
                                    <li><span class="label">Username</span> <span class="value"><?php echo htmlspecialchars($operator['username']); ?></span></li>
                                    <li><span class="label">Status</span> <span class="value"><?php echo htmlspecialchars(ucfirst($operator['status'])); ?></span></li>
                                    <li><span class="label">Login Terakhir</span> <span class="value"><?php echo $operator['last_login'] ? date('d M Y, H:i', strtotime($operator['last_login'])) : 'N/A'; ?></span></li>
                                </ul>
                            </div>
                            <div class="profile-card">
                                <div class="info-card-header"><h3>Ubah Password</h3></div>
                                <p style="font-size: 14px; color: #666; margin-top: -15px; margin-bottom: 20px;">Kosongkan jika tidak ingin mengubah password.</p>
                                <div class="form-group">
                                    <label for="password">Password Baru</label>
                                    <input type="password" id="password" name="password" placeholder="Minimal 8 karakter">
                                </div>
                                <div class="form-group">
                                    <label for="password_konfirmasi">Konfirmasi Password Baru</label>
                                    <input type="password" id="password_konfirmasi" name="password_konfirmasi" placeholder="Ketik ulang password baru">
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </section>
        </form>
    </main>

   <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // --- PERUBAHAN: Skrip baru untuk preview foto profil ---
        function previewProfilePic(input) {
            const preview = document.getElementById('profilePicPreview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        function showNotificationModal(element) {
            const id = element.getAttribute('data-id');
            const pesan = element.getAttribute('data-pesan');
            const pengirim = element.getAttribute('data-pengirim');
            const tanggal = element.getAttribute('data-tanggal');

            Swal.fire({
                title: 'Notifikasi dari ' + pengirim,
                html: `<p style="text-align: left; margin-bottom: 15px;">${pesan}</p>
                       <p style="text-align: left; font-size: 13px; color: #888;">Diterima pada: ${tanggal}</p>`,
                icon: 'info',
                confirmButtonText: 'OK, Tandai Sudah Dibaca',
                confirmButtonColor: '#28a745',
            }).then((result) => {
                if (result.isConfirmed) {
                    const formData = new FormData();
                    formData.append('id', id);
                    fetch('tandai_dibaca.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            element.remove();
                            const list = document.getElementById('notificationList');
                            if (list.children.length === 0) {
                                list.innerHTML = '<li id="no-notification-msg"><p class="meta" style="padding: 15px 0;">Tidak ada notifikasi baru.</p></li>';
                            }
                        } else {
                            Swal.fire('Error!', 'Gagal menandai notifikasi.', 'error');
                        }
                    });
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // ... (skrip dropdown Anda)
        });

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2000, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        if (!empty($errors)) {
            echo "Swal.fire({ title: 'Gagal!', html: '" . addslashes(implode('<br>', $errors)) . "', icon: 'error' });";
        }
        ?>
    </script>
</body>
</html>








    
