<?php
require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['operator_id'])) {
    // PERBAIKAN: Mengarahkan ke folder admin yang benar
    header("Location: index.php"); 
    exit();
}

$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error_message = "Username dan Password tidak boleh kosong.";
    } else {
        $sql = "SELECT id, nama_lengkap, username, password, role, status, foto_profil FROM operator_madrasah WHERE username = ?";
        
        if ($stmt = $conn->prepare($sql)) {
            $stmt->bind_param("s", $username);
            
            if ($stmt->execute()) {
                $stmt->store_result();
                
                if ($stmt->num_rows == 1) {
                    $stmt->bind_result($id, $nama_lengkap, $db_username, $hashed_password, $role, $status, $foto_profil);
                    if ($stmt->fetch()) {
                        if ($status == 'aktif') {
                            if (password_verify($password, $hashed_password)) {
                                // --- LOGIN BERHASIL ---
                                session_regenerate_id(true);
                                
                                $_SESSION['operator_id'] = $id;
                                $_SESSION['nama_lengkap'] = $nama_lengkap;
                                $_SESSION['username'] = $db_username;
                                $_SESSION['role'] = $role;
                                $_SESSION['foto_profil'] = $foto_profil;
                                
                                // Update last_login
                                $update_sql = "UPDATE operator_madrasah SET last_login = CURRENT_TIMESTAMP WHERE id = ?";
                                if ($update_stmt = $conn->prepare($update_sql)) {
                                    $update_stmt->bind_param("i", $id);
                                    $update_stmt->execute();
                                    $update_stmt->close();
                                }
                                
                                // PERBAIKAN: Tambahkan redirect setelah berhasil login
                                header("Location: index.php");
                                exit();

                            } else {
                                // TAMBAHAN: Pesan error jika password salah
                                $error_message = "Username atau Password yang Anda masukkan salah.";
                            }
                        } else {
                             $error_message = "Akun Anda tidak aktif. Silakan hubungi Super Admin.";
                        }
                    }
                } else {
                    // TAMBAHAN: Pesan error jika username tidak ditemukan
                    $error_message = "Username atau Password yang Anda masukkan salah.";
                }
            } else {
                $error_message = "Terjadi kesalahan pada server. Silakan coba lagi.";
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
    <title>Login Operator - MTsN 1 Way Kanan</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style> /* CSS Anda tidak perlu diubah, tetap sama */
        :root{--primary-color:#28a745;--primary-hover:#218838;--light-gray:#f4f7f6;--text-color:#333;--border-color:#e0e0e0}.auth-container{width:100%;max-width:450px;background:#fff;padding:40px;border-radius:12px;box-shadow:0 10px 30px rgba(0,0,0,.1);margin:20px}body{font-family:'Poppins',sans-serif;background-color:var(--light-gray);display:flex;justify-content:center;align-items:center;min-height:100vh;margin:0}.auth-header{text-align:center;margin-bottom:30px}.auth-header img{max-width:150px;margin-bottom:10px}.auth-header h2{color:var(--text-color);font-weight:600;margin:0}.auth-header p{color:#777;font-size:15px;margin-top:5px}.alert{padding:15px;margin-bottom:20px;border-radius:8px;font-size:15px}.alert-danger{background-color:#f8d7da;color:#721c24;border:1px solid #f5c6cb}.alert-success{background-color:#d4edda;color:#155724;border:1px solid #c3e6cb}.input-group{position:relative;margin-bottom:20px}.input-group i{position:absolute;left:18px;top:50%;transform:translateY(-50%);color:#aaa}.input-group input{width:100%;padding:14px 14px 14px 50px;border:1px solid var(--border-color);border-radius:8px;font-size:16px;font-family:'Poppins',sans-serif;box-sizing:border-box;transition:border-color .3s ease}.input-group input:focus{outline:0;border-color:var(--primary-color)}.btn-submit{width:100%;padding:15px;background:var(--primary-color);color:#fff;border:none;border-radius:8px;font-size:16px;font-weight:600;cursor:pointer;transition:background-color .3s ease}.btn-submit:hover{background:var(--primary-hover)}.auth-footer{text-align:center;margin-top:20px;font-size:14px}.auth-footer a{color:var(--primary-color);text-decoration:none;font-weight:500}
    </style>
</head>
<body>

    <div class="auth-container">
        <div class="auth-header">
            <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo Kemenag" style="height: 60px;">
            <img src="img/mtsn1logo.png" alt="Logo Madrasah" style="height: 60px;">
            <h2>Login Admin</h2>
            <p>Selamat datang, silakan masuk.</p>
        </div>

        <?php if (isset($_SESSION['success_message'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success_message']; ?></div>
            <?php unset($_SESSION['success_message']); ?>
        <?php endif; ?>

        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit" class="btn-submit">Login</button>
        </form>

        <div class="auth-footer">
            <p>Copyright <?php echo date('Y'); ?> | MTs Negeri 1 Way Kanan</p>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php
    if (!empty($error_message)) {
        echo "Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '" . addslashes($error_message) . "',
            confirmButtonColor: '#d33'
        });";
    }
    ?>
    </script>
</body>
</html>