<?php
// Memanggil file konfigurasi dari direktori utama
require_once '../admin/config.php';

$errors = [];
$success_message = '';

// Inisialisasi variabel untuk menampung nilai form jika terjadi error
$nama_lengkap = '';
$email = '';
$no_hp = '';
$instansi = '';

// --- PROSES FORM REGISTRASI SAAT DI-SUBMIT ---
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
        $errors[] = "Semua field kecuali No. HP wajib diisi.";
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
    // --- PERUBAHAN: Menambahkan validasi untuk checkbox ---
    if (!isset($_POST['persetujuan'])) {
        $errors[] = "Anda harus menyetujui syarat dan ketentuan untuk mendaftar.";
    }


    // Jika tidak ada error validasi, cek ke database
    if (empty($errors)) {
        // Cek apakah email sudah ada
        $sql_check = "SELECT id FROM kontributor WHERE email = ?";
        if ($stmt_check = $conn->prepare($sql_check)) {
            $stmt_check->bind_param("s", $email);
            $stmt_check->execute();
            $stmt_check->store_result();

            if ($stmt_check->num_rows > 0) {
                $errors[] = "Email sudah terdaftar. Silakan gunakan email lain atau login.";
            }
            $stmt_check->close();
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
                // Siapkan pesan sukses untuk ditampilkan via SweetAlert
                $success_message = "Registrasi berhasil! Akun Anda telah dibuat dan sedang menunggu persetujuan dari admin. Kami akan memberitahu Anda melalui email jika akun sudah diaktifkan.";
                // Kosongkan variabel form
                $nama_lengkap = $email = $no_hp = $instansi = '';
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
    <title>Portal Kontributor Berita - MTsN 1 Way Kanan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
        .hero-section { background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('https://images.unsplash.com/photo-1523240795612-9a054b0db644?q=80&w=2070&auto=format&fit=crop') center center; background-size: cover; color: white; padding: 100px 0; }
        .card-custom { border: none; border-radius: 12px; box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        .step-card { text-align: center; }
        .step-icon { width: 80px; height: 80px; background-color: #e0f2f1; color: #28a745; border-radius: 50%; display: inline-flex; justify-content: center; align-items: center; font-size: 32px; font-weight: 700; margin-bottom: 20px; }
        .modal-content { border-radius: 12px; }
    </style>
</head>
<body>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Selamat Datang, Kontributor!</h1>
            <p class="lead col-lg-8 mx-auto">Jadilah bagian dari penyebar informasi positif seputar kegiatan dan prestasi di MTs Negeri 1 Way Kanan.</p>
        </div>
    </div>

    <div class="container my-5">
        <section id="aturan" class="mb-5">
            <div class="row justify-content-center text-center mb-4">
                <div class="col-lg-8">
                    <h2 class="fw-bold">Alur Menjadi Kontributor</h2>
                    <p class="text-muted">Untuk menjaga kualitas konten, ikuti langkah-langkah berikut untuk mendapatkan akses menulis berita.</p>
                </div>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card card-custom p-4 step-card h-100">
                        <div class="step-icon">1</div>
                        <h5 class="fw-bold">Daftar Akun</h5>
                        <p class="mb-3">Buat akun baru dengan mengisi formulir pendaftaran melalui tombol di bawah ini.</p>
                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#registrasiModal">
                            Daftar Sekarang
                        </button>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 step-card h-100">
                        <div class="step-icon">2</div>
                        <h5 class="fw-bold">Tunggu Persetujuan</h5>
                        <p class="mb-0">Tim admin kami akan meninjau pendaftaran Anda. Proses ini mungkin memerlukan waktu beberapa hari kerja.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card card-custom p-4 step-card h-100">
                        <div class="step-icon">3</div>
                        <h5 class="fw-bold">Mulai Menulis</h5>
                        <p class="mb-0">Jika disetujui, akun Anda akan diaktifkan dan Anda bisa mulai login untuk menulis dan mengirimkan berita.</p>
                        <a href="https://mtsn1waykanan.com/kontributor/login.php" class="btn btn-success">Login</a>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <!-- Modal untuk Form Registrasi -->
    <div class="modal fade" id="registrasiModal" tabindex="-1" aria-labelledby="registrasiModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content card-custom">
                <div class="modal-header border-0">
                    <h1 class="modal-title fs-5 fw-bold" id="registrasiModalLabel">Formulir Registrasi Kontributor</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <strong>Gagal!</strong>
                            <ul><?php foreach ($errors as $error): ?><li><?php echo $error; ?></li><?php endforeach; ?></ul>
                        </div>
                    <?php endif; ?>

                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>#aturan" method="post">
                        <div class="mb-3">
                            <label for="nama_lengkap" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" required value="<?php echo htmlspecialchars($nama_lengkap); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Alamat Email</label>
                            <input type="email" class="form-control" id="email" name="email" required value="<?php echo htmlspecialchars($email); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="no_hp" class="form-label">Nomor Telepon/WhatsApp (Opsional)</label>
                            <input type="tel" class="form-control" id="no_hp" name="no_hp" value="<?php echo htmlspecialchars($no_hp); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="instansi" class="form-label">Asal Instansi / Jabatan</label>
                            <input type="text" class="form-control" id="instansi" name="instansi" placeholder="Contoh: Jurnalis Media Lokal / Guru Mapel" required value="<?php echo htmlspecialchars($instansi); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Minimal 8 karakter" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>

                        <!-- --- PERUBAHAN: Menambahkan checkbox persetujuan --- -->
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="persetujuan" name="persetujuan" required>
                            <label class="form-check-label" for="persetujuan">Saya menyatakan bahwa data yang saya isi adalah benar dan saya bersedia mematuhi semua <a href="syarat_ketentuan.php" target="_blank">syarat & ketentuan</a> yang berlaku.</label>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success btn-lg">Buat Akun</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    <?php
    // Tampilkan notifikasi SweetAlert jika pesan sukses
    if (!empty($success_message)) {
        echo "Swal.fire({
            title: 'Registrasi Terkirim!',
            text: '" . addslashes($success_message) . "',
            icon: 'success',
            confirmButtonText: 'OK',
            confirmButtonColor: '#28a745'
        });";
    }

    // Buka kembali modal jika ada error setelah submit
    if (!empty($errors)) {
        echo "
        document.addEventListener('DOMContentLoaded', function() {
            var registrasiModal = new bootstrap.Modal(document.getElementById('registrasiModal'));
            registrasiModal.show();
        });
        ";
    }
    ?>
    </script>
</body>
</html>
