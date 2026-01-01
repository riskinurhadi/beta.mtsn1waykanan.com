<?php
require_once 'config.php';

$errors = [];

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location:login.php");
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil dan bersihkan data
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    // Validasi
    if (empty($nama_lengkap) || empty($username) || empty($email) || empty($password)) {
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
        // Cek apakah username atau email sudah ada
        $sql = "SELECT id FROM operator_madrasah WHERE username = ? OR email = ?";
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("ss", $username, $email);
            $stmt->execute();
            $stmt->store_result();

            if ($stmt->num_rows > 0) {
                $errors[] = "Username atau Email sudah terdaftar.";
            }
            $stmt->close();
        }
    }
    
    // Jika semua validasi lolos, masukkan data ke database
    if (empty($errors)) {
        // Hash password sebelum disimpan
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql_insert = "INSERT INTO operator_madrasah (nama_lengkap, username, email, password, role) VALUES (?, ?, ?, ?, 'operator')";
        
        if ($stmt_insert = $conn->prepare($sql_insert)) {
            $stmt_insert->bind_param("ssss", $nama_lengkap, $username, $email, $hashed_password);
            
            if ($stmt_insert->execute()) {
                // Set pesan sukses dan redirect ke halaman login
                $_SESSION['success_message'] = "Registrasi berhasil! Silakan login dengan akun Anda.";
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
    <title>Registrasi Operator - MTsN 1 Way Kanan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --light-gray: #f4f7f6;
            --text-color: #333;
            --border-color: #e0e0e0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px 0;
        }

        .auth-container {
            width: 100%;
            max-width: 480px;
            background: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            margin: 20px;
        }

        .auth-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .auth-header img {
            max-width: 150px;
            margin-bottom: 10px;
        }

        .auth-header h2 {
            color: var(--text-color);
            font-weight: 600;
            margin: 0;
        }

        .auth-header p {
            color: #777;
            font-size: 15px;
            margin-top: 5px;
        }

        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            font-size: 15px;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
        }

        .input-group i {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #aaa;
        }

        .input-group input {
            width: 100%;
            padding: 14px 14px 14px 50px;
            border: 1px solid var(--border-color);
            border-radius: 8px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
        }

        .btn-submit {
            width: 100%;
            padding: 15px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-submit:hover {
            background: var(--primary-hover);
        }

        .auth-footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
        }

        .auth-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <img src="https://i.pinimg.com/736x/8b/42/da/8b42da28243a95af418ef32cc5df9261.jpg" alt="Logo Madrasah">
            <h2>Buat Akun Operator Baru</h2>
            <p>Silakan isi Identitas Operator Baru.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger">
                <?php foreach ($errors as $error): ?>
                    <p style="margin: 0;"><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <i class="fas fa-id-card"></i>
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required value="<?php echo isset($_POST['nama_lengkap']) ? htmlspecialchars($_POST['nama_lengkap']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password (min. 8 karakter)" required>
            </div>
            <div class="input-group">
                <i class="fas fa-check-circle"></i>
                <input type="password" name="confirm_password" placeholder="Konfirmasi Password" required>
            </div>
            <button type="submit" class="btn-submit">Submit</button>
        </form>

        <div class="auth-footer">
            <p>Copyright 2025 | MTs Negeri 1 Way Kanan</a></p>
        </div>
    </div>

</body>
</html>