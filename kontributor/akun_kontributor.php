<?php
require_once '../admin/config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['kontributor_id'])) {
    header("Location: login.php");
    exit();
}

$kontributor_id = $_SESSION['kontributor_id'];
$kontributor_nama = $_SESSION['kontributor_nama'];

$errors = [];

// --- PROSES UPDATE SAAT FORM DI-SUBMIT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $instansi = trim($_POST['instansi']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($nama_lengkap) || empty($email)) {
        $errors[] = "Nama Lengkap dan Email tidak boleh kosong.";
    }
    if (!empty($password) && $password !== $confirm_password) {
        $errors[] = "Konfirmasi password baru tidak cocok.";
    }

    if (empty($errors)) {
        $sql_update = "UPDATE kontributor SET nama_lengkap = ?, email = ?, no_hp = ?, instansi = ?";
        $params = [$nama_lengkap, $email, $no_hp, $instansi];
        $types = "ssss";
        
        if (!empty($password)) {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);
            $sql_update .= ", password = ?";
            $params[] = $hashed_password;
            $types .= "s";
        }
        
        $sql_update .= " WHERE id = ?";
        $params[] = $kontributor_id;
        $types .= "i";
        
        if ($stmt_update = $conn->prepare($sql_update)) {
            $stmt_update->bind_param($types, ...$params);
            if ($stmt_update->execute()) {
                $_SESSION['kontributor_nama'] = $nama_lengkap; // Update nama di session
                $_SESSION['success_message'] = "Profil Anda berhasil diperbarui.";
                header("Location: akun_kontributor.php");
                exit();
            } else {
                $errors[] = "Gagal memperbarui data.";
            }
            $stmt_update->close();
        }
    }
}

// Ambil data kontributor saat ini untuk ditampilkan di form
$sql_select = "SELECT nama_lengkap, email, no_hp, instansi FROM kontributor WHERE id = ?";
if ($stmt = $conn->prepare($sql_select)) {
    $stmt->bind_param("i", $kontributor_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $kontributor = $result->fetch_assoc();
    $stmt->close();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Akun Saya - Portal Kontributor</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <style>
        :root { --primary-color: #28a745; --sidebar-bg: #2c3e50; --sidebar-text: #ecf0f1; --sidebar-active: #34495e; --main-bg: #f4f7f6; --text-color: #333; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        body { font-family: 'Poppins', sans-serif; background-color: var(--main-bg); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid var(--sidebar-active); }
        .sidebar-header h5 { margin: 0; font-weight: 600; }
        .sidebar-nav { list-style: none; padding: 20px 0; }
        .sidebar-nav li a { display: flex; align-items: center; padding: 15px 20px; color: var(--sidebar-text); text-decoration: none; font-size: 15px; transition: background-color 0.2s ease; }
        .sidebar-nav li a i { width: 30px; font-size: 18px; margin-right: 10px; }
        .sidebar-nav li a:hover, .sidebar-nav li.active a { background-color: var(--sidebar-active); }
        .main-content { margin-left: 260px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background-color: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: var(--card-shadow); margin-bottom: 30px; }
        .header h1 { font-size: 24px; font-weight: 600; margin: 0; }
        .card-custom { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h5>Portal Kontributor</h5>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="tulis_berita.php"><i class="fas fa-plus-circle"></i><span>Tulis Berita</span></a></li>
            <li class="active"><a href="akun_kontributor.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Akun Saya</h1>
        </header>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger"><ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul></div>
                <?php endif; ?>
                
                <form action="akun_kontributor.php" method="post">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($kontributor['nama_lengkap']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($kontributor['email']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="no_hp" class="form-label">No. HP</label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($kontributor['no_hp']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="instansi" class="form-label">Instansi</label>
                            <input type="text" class="form-control" id="instansi" name="instansi" value="<?php echo htmlspecialchars($kontributor['instansi']); ?>">
                        </div>
                    </div>
                    <hr class="my-4">
                    <h5 class="fw-bold mb-3">Ubah Password</h5>
                    <p class="text-muted small">Kosongkan jika tidak ingin mengubah password.</p>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="password" class="form-label">Password Baru</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                        </div>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-success">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2000, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        ?>
    </script>
</body>
</html>
