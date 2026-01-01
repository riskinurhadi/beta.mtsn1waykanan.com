<?php
// Memanggil file konfigurasi dari direktori utama
require_once '../admin/config.php';

$errors = [];
// Inisialisasi variabel untuk menampung nilai form jika terjadi error
$nama_lengkap = '';
$email = '';
$no_hp = '';
$instansi = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $email = trim($_POST['email']);
    $no_hp = trim($_POST['no_hp']);
    $instansi = trim($_POST['instansi']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi
    if (empty($nama_lengkap) || empty($email) || empty($password) || empty($instansi)) {
        $errors[] = "Semua field wajib diisi.";
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Format email tidak valid.";
    }
    if (strlen($password) < 8) {
        $errors[] = "Password minimal harus 8 karakter.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Konfirmasi password tidak cocok.";
    }

    // Jika tidak ada error validasi, cek ke database
    if (empty($errors)) {
        // Cek apakah email sudah ada
        $sql = "SELECT id FROM kontributor WHERE email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Email sudah terdaftar. Silakan gunakan email lain.";
            }
            $stmt->close();
        }
    }
    
    // Jika semua validasi lolos, masukkan data ke database
    if (empty($errors)) {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql_insert = "INSERT INTO kontributor (nama_lengkap, email, no_hp, instansi, password, status_akun) VALUES (?, ?, ?, ?, ?, 'pending')";
        
        if ($stmt_insert = $conn->prepare($sql_insert)) {
            $stmt_insert->bind_param("sssss", $nama_lengkap, $email, $no_hp, $instansi, $hashed_password);
            
            if ($stmt_insert->execute()) {
                // Set pesan sukses dan redirect ke halaman login
                $_SESSION['success_message'] = "Registrasi berhasil! Akun Anda sedang ditinjau oleh admin.";
                header("Location: login.php");
                exit();
            } else {
                $errors[] = "Terjadi kesalahan saat registrasi. Silakan coba lagi.";
            }
            $stmt_insert->close();
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
    <title>Registrasi Kontributor - MTsN 1 Way Kanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --primary-color: #28a745; --primary-hover: #218838; --light-gray: #f4f7f6; --text-color: #333; --border-color: #e0e0e0; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px 0; }
        .auth-container { width: 100%; max-width: 480px; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin: 20px; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h2 { color: var(--text-color); font-weight: 600; margin: 0; }
        .auth-header p { color: #777; font-size: 15px; margin-top: 5px; }
        .alert-danger { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .alert-danger ul { padding-left: 20px; margin-bottom: 0; }
        .input-group { position: relative; margin-bottom: 20px; }
        .input-group i { position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #aaa; }
        .input-group input { width: 100%; padding: 14px 14px 14px 50px; border: 1px solid var(--border-color); border-radius: 8px; font-size: 16px; font-family: 'Poppins', sans-serif; box-sizing: border-box; transition: border-color 0.3s ease; }
        .input-group input:focus { outline: none; border-color: var(--primary-color); }
        .btn-submit { width: 100%; padding: 15px; background: var(--primary-color); color: #fff; border: none; border-radius: 8px; font-size: 16px; font-weight: 600; cursor: pointer; transition: background-color 0.3s ease; }
        .btn-submit:hover { background: var(--primary-hover); }
        .auth-footer { text-align: center; margin-top: 20px; font-size: 14px; }
        .auth-footer a { color: var(--primary-color); text-decoration: none; font-weight: 500; }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-header">
            <h2>Buat Akun Kontributor</h2>
            <p>Daftar untuk mulai berkontribusi berita.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li><?php echo $error; ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <form action="registrasi.php" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required value="<?php echo htmlspecialchars($nama_lengkap); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required value="<?php echo htmlspecialchars($email); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="tel" name="no_hp" placeholder="No. HP / WhatsApp" value="<?php echo htmlspecialchars($no_hp); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-building"></i>
                <input type="text" name="instansi" placeholder="Asal Instansi / Jabatan" required value="<?php echo htmlspecialchars($instansi); ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password (min. 8 karakter)" required>
            </div>
            <div class="input-group">
                <i class="fas fa-check-circle"></i>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" class="btn-submit">Daftar</button>
        </form>

        <div class="auth-footer">
            <p>Sudah punya akun? <a href="login.php">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
