<?php
// Pastikan session dimulai di paling atas
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require_once 'config.php';

// Jika sudah login, redirect ke dashboard
if (isset($_SESSION['operator_id'])) {
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
                                
                                header("Location: index.php");
                                exit();

                            } else {
                                $error_message = "Username atau Password yang Anda masukkan salah.";
                            }
                        } else {
                            $error_message = "Akun Anda tidak aktif. Silakan hubungi Super Admin.";
                        }
                    }
                } else {
                    $error_message = "Username atau Password yang Anda masukkan salah.";
                }
            } else {
                $error_message = "Terjadi kesalahan pada server. Silakan coba lagi.";
            }
            $stmt->close();
        }
    }
}

// Cek status maintenance (jika diperlukan)
// $maintenance_status = 'off'; 
// ... (logika maintenance Anda bisa diletakkan di sini)

$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Operator - MTsN 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="https://lulusku.kemusukkidul.com/img/kemenag.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #28a745;
            --primary-hover: #218838;
            --secondary-color: #f0f2f5; /* Sedikit lebih gelap untuk kontras */
            --text-dark: #343a40;
            --text-light: #6c757d;
            --border-color: #dee2e6;
            --background-gradient: linear-gradient(135deg, #28a745 0%, #218838 100%);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--secondary-color);
            color: var(--text-dark);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            /* Hapus overflow: hidden agar bisa scroll di mobile */
        }

        .login-container {
            display: flex;
            width: 100%;
            max-width: 1100px;
            min-height: 600px;
            background-color: #fff;
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            margin: 20px;
            animation: fadeIn 0.8s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }

        .illustration-panel {
            flex: 1;
            background: var(--background-gradient);
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            color: #fff;
            position: relative;
        }

        .illustration-panel::before {
            content: '';
            position: absolute;
            top: -50px;
            left: -50px;
            width: 200px;
            height: 200px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            filter: blur(30px);
        }
        
        .illustration-panel::after {
            content: '';
            position: absolute;
            bottom: -80px;
            right: -80px;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 50%;
            filter: blur(40px);
        }

        .logo-container img {
            max-height: 80px;
            margin-bottom: 25px;
        }

        .illustration-panel h1 {
            font-weight: 700;
            font-size: 28px;
            margin-bottom: 15px;
            z-index: 1;
        }

        .illustration-panel p {
            font-size: 16px;
            max-width: 350px;
            line-height: 1.7;
            opacity: 0.9;
            z-index: 1;
        }

        .form-panel {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-header {
            margin-bottom: 30px;
        }

        .form-header h2 {
            font-size: 32px;
            font-weight: 700;
            color: var(--text-dark);
            margin-bottom: 5px;
        }

        .form-header p {
            color: var(--text-light);
            font-size: 16px;
        }

        .input-group {
            position: relative;
            margin-bottom: 25px;
        }

        .input-group i {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            transition: color 0.3s ease;
        }

        .input-group input {
            width: 100%;
            padding: 16px 16px 16px 55px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: #aaa;
        }

        .input-group input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 4px rgba(40, 167, 69, 0.1);
        }
        
        .input-group input:focus + i {
            color: var(--primary-color);
        }

        .btn-submit {
            width: 100%;
            padding: 16px;
            background: var(--primary-color);
            color: #fff;
            border: none;
            border-radius: 10px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.2);
        }

        .btn-submit:hover {
            background: var(--primary-hover);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(40, 167, 69, 0.3);
        }

        .form-footer {
            text-align: center;
            margin-top: 40px;
            font-size: 14px;
            color: var(--text-light);
        }

        /* --- PERBAIKAN RESPONSIVE DESIGN --- */

        /* Untuk Tablet dan Perangkat Lebih Kecil */
        @media (max-width: 992px) {
            .login-container {
                flex-direction: column;
                max-width: 500px;
                min-height: auto; /* Biarkan tinggi menyesuaikan konten */
                margin: 40px 20px; /* Beri jarak atas bawah */
            }

            .illustration-panel {
                /* Sembunyikan panel ilustrasi di layar kecil agar fokus ke form */
                display: none; 
            }

            .form-panel {
                padding: 40px;
            }
        }

        /* Untuk Ponsel */
        @media (max-width: 576px) {
            body {
                /* Ganti align-items agar container bisa di-scroll jika konten panjang */
                align-items: flex-start; 
                padding: 20px 0;
            }
            .login-container {
                margin: 0 15px; /* Kurangi margin horizontal */
                width: calc(100% - 30px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            }
            
            .form-panel {
                padding: 30px 25px; /* Kurangi padding di dalam form */
            }

            .form-header h2 {
                font-size: 26px; /* Kecilkan judul */
            }
            
            .form-header p {
                font-size: 15px;
            }

            .input-group input {
                padding: 15px 15px 15px 50px; /* Sedikit kurangi padding input */
                font-size: 15px;
            }

            .btn-submit {
                padding: 15px;
                font-size: 16px;
            }

            .form-footer {
                margin-top: 30px;
                font-size: 13px;
            }
        }
    </style>
</head>
<body>

    <div class="login-container mt-5 mb-5">
        <!-- Panel Ilustrasi (Kiri) -->
        <div class="illustration-panel">
            <div class="logo-container">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo Madrasah">
                <img src="https://mtsn1waykanan.com/img/mtsn1logo.png" alt="Logo Madrasah">
            </div>
            <h1>Sistem Informasi<br>MTsN 1 Way Kanan</h1>
            <p>Akses ke dasbor admin untuk mengelola konten, berita, dan informasi penting lainnya dengan mudah.</p>
        </div>

        <!-- Panel Form (Kanan) -->
        <div class="form-panel">
            <div class="form-header">
                <h2>Login Admin</h2>
                <p>Selamat datang, silakan masuk.</p>
            </div>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <div class="input-group">
                    <input type="text" name="username" placeholder="Username" required>
                    <i class="fas fa-user"></i>
                </div>
                <div class="input-group">
                    <input type="password" name="password" placeholder="Password" required>
                    <i class="fas fa-lock"></i>
                </div>
                <button type="submit" class="btn-submit">Login</button>
            </form>

            <div class="form-footer">
                <p>&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan</p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php
    if (!empty($error_message)) {
        echo "
        Swal.fire({
            icon: 'error',
            title: 'Login Gagal',
            text: '" . addslashes($error_message) . "',
            confirmButtonColor: '#28a745',
            confirmButtonText: 'Coba Lagi'
        });
        ";
    }
    ?>
    </script>
</body>
</html>
