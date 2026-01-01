<?php
session_start();
require 'koneksi.php';

$error_msg = '';
$success_msg = '';

// --- LOGIKA REGISTER ---
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $nama_lengkap = trim($_POST['nama_lengkap']);

    if ($password !== $confirm_password) {
        $error_msg = "Konfirmasi password tidak cocok!";
    } else {
        // Cek apakah username sudah ada
        $stmt_check = $koneksi->prepare("SELECT id_admin FROM tb_admin WHERE username = ?");
        $stmt_check->bind_param("s", $username);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_msg = "Username sudah terdaftar, silakan gunakan username lain.";
        } else {
            // Hash password sebelum disimpan
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt_insert = $koneksi->prepare("INSERT INTO tb_admin (username, password, nama_lengkap) VALUES (?, ?, ?)");
            $stmt_insert->bind_param("sss", $username, $hashed_password, $nama_lengkap);

            if ($stmt_insert->execute()) {
                $success_msg = "Registrasi berhasil! Silakan login.";
            } else {
                $error_msg = "Registrasi gagal, terjadi kesalahan.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

// --- LOGIKA LOGIN ---
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $koneksi->prepare("SELECT id_admin, password, nama_lengkap FROM tb_admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id_admin, $hashed_password, $nama_lengkap);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            // Login berhasil, simpan data ke session
            $_SESSION['admin_id'] = $id_admin;
            $_SESSION['admin_username'] = $username;
            $_SESSION['admin_nama'] = $nama_lengkap;
            
            // Redirect ke halaman dashboard (buat file index.php nantinya)
            header("Location: index.php");
            exit();
        } else {
            $error_msg = "Username atau password salah.";
        }
    } else {
        $error_msg = "Username atau password salah.";
    }
    $stmt->close();
}

$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Authentication</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .auth-container {
            max-width: 450px;
            width: 100%;
        }
        .auth-card {
            background-color: white;
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .auth-header {
            background-color: #0d47a1;
            color: white;
            padding: 2rem;
            text-align: center;
        }
        .auth-header h3 {
            margin: 0;
            font-weight: 600;
        }
        .auth-body {
            padding: 2rem;
        }
        .form-control {
            height: 50px;
            border-radius: 8px;
        }
        .btn-primary {
            background-color: #0d47a1;
            border-color: #0d47a1;
            height: 50px;
            font-weight: 500;
            border-radius: 8px;
        }
        .toggle-link {
            cursor: pointer;
            color: #0d47a1;
            font-weight: 500;
        }
        #registerForm {
            display: none;
        }
    </style>
</head>
<body>

<div class="auth-container">
    <div class="auth-card">
        <div class="auth-header">
            <h3 id="formTitle">Admin Login</h3>
        </div>
        <div class="auth-body">
            
            <?php if ($error_msg): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>
            <?php if ($success_msg): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>

            <!-- Login Form -->
            <form id="loginForm" method="POST" action="auth.php">
                <div class="mb-3">
                    <label for="loginUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="loginUsername" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="loginPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="loginPassword" name="password" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">Login</button>
                <p class="text-center mt-3">
                    © Tampingan 2025 | All right reserved
                    <!--<span class="toggle-link" onclick="toggleForm()">Register di sini</span>-->
                </p>
            </form>

            <!-- Register Form -->
            <form id="registerForm" method="POST" action="auth.php">
                <div class="mb-3">
                    <label for="regNama" class="form-label">Nama Lengkap</label>
                    <input type="text" class="form-control" id="regNama" name="nama_lengkap" required>
                </div>
                <div class="mb-3">
                    <label for="regUsername" class="form-label">Username</label>
                    <input type="text" class="form-control" id="regUsername" name="username" required>
                </div>
                <div class="mb-3">
                    <label for="regPassword" class="form-label">Password</label>
                    <input type="password" class="form-control" id="regPassword" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="regConfirmPassword" class="form-label">Konfirmasi Password</label>
                    <input type="password" class="form-control" id="regConfirmPassword" name="confirm_password" required>
                </div>
                <button type="submit" name="register" class="btn btn-primary w-100">Register</button>
                 <p class="text-center mt-3">
                    Sudah punya akun? <span class="toggle-link" onclick="toggleForm()">Login di sini</span>
                </p>
            </form>

        </div>
    </div>
</div>

<script>
    function toggleForm() {
        const loginForm = document.getElementById('loginForm');
        const registerForm = document.getElementById('registerForm');
        const formTitle = document.getElementById('formTitle');

        if (loginForm.style.display === 'none') {
            loginForm.style.display = 'block';
            registerForm.style.display = 'none';
            formTitle.textContent = 'Admin Login';
        } else {
            loginForm.style.display = 'none';
            registerForm.style.display = 'block';
            formTitle.textContent = 'Admin Register';
        }
    }
</script>
</body>
</html>
