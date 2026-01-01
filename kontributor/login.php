<?php
// Memanggil file konfigurasi dari direktori utama
require_once '../admin/config.php';

// Jika kontributor sudah login, arahkan ke dashboard mereka
if (isset($_SESSION['kontributor_id'])) {
    header("Location: dashboard.php");
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $error_message = "Email dan Password tidak boleh kosong.";
    } else {
        // --- PERUBAHAN LANGKAH 1: Verifikasi pengguna di tabel kontributor ---
        $sql = "SELECT id, nama_lengkap, email, password, status_akun FROM kontributor WHERE email = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $user = $result->fetch_assoc();
                
                // Verifikasi password
                if (password_verify($password, $user['password'])) {
                    // Cek status akun
                    if ($user['status_akun'] == 'aktif') {
                        
                        // --- PERUBAHAN LANGKAH 2: Ambil API Key dari tabel api_keys ---
                        $api_key = '';
                        $sql_key = "SELECT api_key FROM api_keys WHERE id_kontributor = ? AND status = 'aktif' LIMIT 1";
                        if($stmt_key = $conn->prepare($sql_key)) {
                            $stmt_key->bind_param("i", $user['id']);
                            $stmt_key->execute();
                            $stmt_key->bind_result($api_key);
                            $stmt_key->fetch();
                            $stmt_key->close();
                        }

                        // --- PERUBAHAN LANGKAH 3: Buat session dengan API Key yang benar ---
                        session_regenerate_id(true);
                        $_SESSION['kontributor_id'] = $user['id'];
                        $_SESSION['kontributor_nama'] = $user['nama_lengkap'];
                        $_SESSION['kontributor_email'] = $user['email'];
                        $_SESSION['kontributor_api_key'] = $api_key; // Menyimpan API Key dari tabel api_keys
                        
                        // Arahkan ke dashboard kontributor
                        header("Location: dashboard.php");
                        exit();

                    } else {
                        $error_message = "Akun Anda belum aktif, sedang ditinjau, atau telah diblokir.";
                    }
                } else {
                    $error_message = "Email atau Password yang Anda masukkan salah.";
                }
            } else {
                $error_message = "Email atau Password yang Anda masukkan salah.";
            }
            $stmt->close();
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kontributor - MTsN 1 Way Kanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        :root { --primary-color: #28a745; --primary-hover: #218838; --light-gray: #f4f7f6; --text-color: #333; --border-color: #e0e0e0; }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; }
        .auth-container { width: 100%; max-width: 450px; background: #fff; padding: 40px; border-radius: 12px; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1); margin: 20px; }
        .auth-header { text-align: center; margin-bottom: 30px; }
        .auth-header h2 { color: var(--text-color); font-weight: 600; margin: 0; }
        .auth-header p { color: #777; font-size: 15px; margin-top: 5px; }
        .alert-danger { padding: 15px; margin-bottom: 20px; border-radius: 8px; font-size: 15px; background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
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
            <h2>Portal Login Kontributor</h2>
            <p>Selamat datang, silakan masuk untuk menulis berita.</p>
        </div>
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <form action="login.php" method="post">
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" placeholder="Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>
        <div class="auth-footer">
            <p>Belum punya akun? <a href="registrasi.php">Daftar di sini</a></p>
        </div>
    </div>
</body>
</html>
