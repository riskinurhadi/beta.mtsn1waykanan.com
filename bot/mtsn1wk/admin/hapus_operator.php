<?php
require_once 'config.php';

// --- SECURITY CHECK ---
// 1. Cek apakah operator sudah login
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// 2. Cek apakah rolenya adalah 'superadmin'
if ($_SESSION['role'] !== 'superadmin') {
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
        <p>Anda tidak memiliki izin untuk menghapus atau mengedit Admin.</p>
        <a href="kelola_operator.php" class="btn-back"><i class="fas fa-arrow-left"></i> Kembali</a>
    </div>
</body>
</html>
HTML;
    exit(); // Hentikan eksekusi skrip setelah menampilkan halaman
}

// 3. Validasi ID dari URL
if (isset($_GET['id']) && filter_var($_GET['id'], FILTER_VALIDATE_INT)) {
    $id_to_delete = $_GET['id'];

    // 4. Mencegah superadmin menghapus akunnya sendiri
    if ($id_to_delete == $_SESSION['operator_id']) {
        $_SESSION['error_message'] = "Anda tidak dapat menghapus akun Anda sendiri.";
        header("Location: kelola_operator.php");
        exit();
    }

    $file_to_delete = null;

    // 5. Ambil nama file foto profil dari database sebelum menghapus
    $sql_select = "SELECT foto_profil FROM operator_madrasah WHERE id = ?";
    if ($stmt_select = $conn->prepare($sql_select)) {
        $stmt_select->bind_param("i", $id_to_delete);
        $stmt_select->execute();
        $stmt_select->bind_result($foto_profil);
        
        if ($stmt_select->fetch()) {
            $file_to_delete = $foto_profil;
        }
        $stmt_select->close();
    }

    // Jika operator ditemukan, lanjutkan proses hapus
    if ($file_to_delete !== null) {
        // 6. Hapus record dari database
        $sql_delete = "DELETE FROM operator_madrasah WHERE id = ?";
        if ($stmt_delete = $conn->prepare($sql_delete)) {
            $stmt_delete->bind_param("i", $id_to_delete);
            
            if ($stmt_delete->execute()) {
                // 7. Jika record berhasil dihapus, hapus file foto profil fisiknya
                // Jangan hapus file 'default.png'
                if ($file_to_delete && $file_to_delete !== 'default.png') {
                    $file_path = 'uploads/operator/' . $file_to_delete;
                    if (file_exists($file_path)) {
                        unlink($file_path);
                    }
                }
                $_SESSION['success_message'] = "Akun operator berhasil dihapus.";
            } else {
                $_SESSION['error_message'] = "Gagal menghapus akun dari database.";
            }
            $stmt_delete->close();
        }
    } else {
        $_SESSION['error_message'] = "Operator dengan ID tersebut tidak ditemukan.";
    }

} else {
    // Jika ID tidak ada atau tidak valid
    $_SESSION['error_message'] = "Permintaan tidak valid.";
}

$conn->close();

// Redirect kembali ke halaman kelola operator dengan notifikasi
header("Location: kelola_operator.php");
exit();