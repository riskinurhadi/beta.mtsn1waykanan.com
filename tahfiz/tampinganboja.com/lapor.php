<?php
// Mulai sesi untuk menyimpan pesan notifikasi
session_start();

// Panggil file koneksi.php
require 'koneksi.php';

// Inisialisasi variabel pesan
$pesan = '';
$jenis_pesan = '';

// Cek apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form dan lakukan sanitasi dasar
    $nama_pelapor = htmlspecialchars(trim($_POST['nama_pelapor']));
    $no_hp_pelapor = htmlspecialchars(trim($_POST['no_hp_pelapor']));
    $judul_laporan = htmlspecialchars(trim($_POST['judul_laporan']));
    $isi_laporan = htmlspecialchars(trim($_POST['isi_laporan']));
    $lokasi_kejadian = htmlspecialchars(trim($_POST['lokasi_kejadian']));
    $nama_lampiran = null;

    // Validasi input wajib
    if (empty($nama_pelapor) || empty($no_hp_pelapor) || empty($judul_laporan) || empty($isi_laporan)) {
        $pesan = "Gagal mengirim laporan. Mohon lengkapi semua kolom yang wajib diisi.";
        $jenis_pesan = "error";
    } else {
        // Proses upload lampiran jika ada
        if (isset($_FILES['lampiran']) && $_FILES['lampiran']['error'] == 0) {
            $target_dir = "admin/uploads/laporan/"; // Pastikan folder ini ada dan writable
            // Buat nama file unik untuk menghindari duplikasi
            $nama_lampiran = time() . '_' . basename($_FILES["lampiran"]["name"]);
            $target_file = $target_dir . $nama_lampiran;
            $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

            // Cek tipe file (misal: jpg, png, mp4, pdf)
            $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf", "mp4", "mov", "avi");
            if (in_array($file_type, $allowed_types)) {
                // Pindahkan file ke folder tujuan
                if (!move_uploaded_file($_FILES["lampiran"]["tmp_name"], $target_file)) {
                    $pesan = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
                    $jenis_pesan = "error";
                    $nama_lampiran = null; // Batalkan jika upload gagal
                }
            } else {
                $pesan = "Maaf, hanya file JPG, JPEG, PNG, GIF, PDF, MP4, MOV, & AVI yang diizinkan.";
                $jenis_pesan = "error";
                $nama_lampiran = null;
            }
        }

        // Lanjutkan ke database jika tidak ada error pada upload file
        if ($jenis_pesan !== 'error') {
            // Siapkan query SQL menggunakan prepared statements untuk keamanan
            $sql = "INSERT INTO tb_laporan (nama_pelapor, no_hp_pelapor, judul_laporan, isi_laporan, lokasi_kejadian, lampiran) VALUES (?, ?, ?, ?, ?, ?)";

            if ($stmt = $koneksi->prepare($sql)) {
                // Bind parameter ke statement
                $stmt->bind_param("ssssss", $nama_pelapor, $no_hp_pelapor, $judul_laporan, $isi_laporan, $lokasi_kejadian, $nama_lampiran);

                // Eksekusi statement
                if ($stmt->execute()) {
                    // Set session untuk notifikasi sukses
                    $_SESSION['pesan'] = "Laporan Anda telah berhasil dikirim. Terima kasih atas partisipasi Anda.";
                    $_SESSION['jenis_pesan'] = "success";
                } else {
                    $_SESSION['pesan'] = "Terjadi kesalahan pada server. Gagal menyimpan laporan.";
                    $_SESSION['jenis_pesan'] = "error";
                }
                $stmt->close();
                 // Redirect untuk mencegah resubmission form
                header("Location: lapor.php");
                exit();
            } else {
                $pesan = "Terjadi kesalahan dalam persiapan query database.";
                $jenis_pesan = "error";
            }
        }
    }
     // Simpan pesan error sementara jika redirect tidak terjadi
    if (!empty($pesan)) {
        $_SESSION['pesan'] = $pesan;
        $_SESSION['jenis_pesan'] = $jenis_pesan;
        header("Location: lapor.php");
        exit();
    }
}

// Ambil pesan dari session dan hapus setelahnya
if (isset($_SESSION['pesan'])) {
    $pesan = $_SESSION['pesan'];
    $jenis_pesan = $_SESSION['jenis_pesan'];
    unset($_SESSION['pesan']);
    unset($_SESSION['jenis_pesan']);
}

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lapor Pak Kades - Desa Tampingan</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <!-- Google Fonts: Poppins -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #0d47a1;
            --secondary-color: #ff9800;
            --light-gray: #f8f9fa;
            --dark-text: #333;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-gray);
        }

        /* ----- Navbar Styling (diasumsikan sama dengan halaman utama) ----- */
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; padding: 0.8rem 1rem; border-radius: 5px; transition: background-color 0.3s; }
        .dropdown-menu { border-radius: 10px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15); }
        .dropdown-item:hover { background-color: var(--light-gray); color: var(--primary-color); }
        @media (min-width: 992px) {
            .navbar-nav .nav-link.active, .navbar-nav .nav-link:hover { background-color: rgba(255,255,255,0.1); }
            .dropdown:hover > .dropdown-menu { display: block; }
        }
        
        .navbar .btn-outline-light {
    /* Menambahkan transisi agar hover lebih halus */
    transition: all 0.3s ease;
    font-weight: 500;
}

/* Penyesuaian untuk layar sangat kecil agar tidak terlalu besar */
@media (max-width: 576px) {
    .navbar .btn {
        /* Menggunakan ukuran tombol 'small' dari Bootstrap */
        padding: 0.375rem 0.75rem;
        font-size: 0.875rem;
    }

    /* Mengurangi margin kanan logo agar ada lebih banyak ruang */
    .navbar-brand img {
        margin-right: 10px;
    }
}

        /* ----- Page Header ----- */
        .page-header {
            background: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('/img/fotobersama.png') no-repeat center center;
            background-size: cover;
            padding: 8rem 0;
            color: white;
            text-align: center;
        }
        .page-header h1 {
            font-weight: 700;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
        }

        /* ----- Form Section ----- */
        .laporan-section {
            padding: 4rem 0;
        }
        .form-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        .form-card .card-header {
            background-color: var(--primary-color);
            color: white;
            border-top-left-radius: 15px;
            border-top-right-radius: 15px;
            padding: 1.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }
        .form-card .card-body {
            padding: 2rem;
        }
        .form-label {
            font-weight: 500;
        }
        .form-control, .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }
        .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(13, 71, 161, 0.25);
        }
        .btn-submit {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-submit:hover {
            background-color: #e68900;
            border-color: #e68900;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <!-- Brand/Logo (tidak berubah) -->
            <a class="navbar-brand" href="index.php">
                <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                <div class="logo-text">
                    <span class="title">Pemerintah Kabupaten Kendal</span>
                    <span class="subtitle">Desa Tampingan</span>
                </div>
            </a>

            <!-- Tombol hamburger (Toggler) untuk mobile -->
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavProfil" aria-controls="navbarNavProfil" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Konten yang akan disembunyikan di mobile -->
            <div class="collapse navbar-collapse" id="navbarNavProfil">
                <ul class="navbar-nav ms-auto">
                    <!-- Hanya satu menu item -->
                    <li class="nav-item">
                        <a class="nav-link" href="https://tampinganboja.com/">
                            <i class="bi bi-house-door-fill me-1"></i> Kembali ke Beranda
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
</header>

<main>
    <section class="page-header">
        <div class="container">
            <h1 class="display-4 mt-5">Layanan Aspirasi<br> Dan Laporan</h1>
            <p class="lead">Sampaikan laporan atau aspirasi Anda secara langsung kepada kami.</p>
        </div>
    </section>

    <section class="laporan-section">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <div class="card form-card">
                        <div class="card-header text-center">
                            <i class="bi bi-pencil-square me-2"></i> Formulir Laporan Warga
                        </div>
                        <div class="card-body">
                            <form action="lapor.php" method="POST" enctype="multipart/form-data" id="formLaporan">
                                <div class="mb-3">
                                    <label for="nama_pelapor" class="form-label">Nama Lengkap Anda <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="nama_pelapor" name="nama_pelapor" required>
                                </div>
                                <div class="mb-3">
                                    <label for="no_hp_pelapor" class="form-label">Nomor HP/WhatsApp Aktif <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="no_hp_pelapor" name="no_hp_pelapor" placeholder="Contoh: 08123456789" required>
                                </div>
                                <hr class="my-4">
                                <div class="mb-3">
                                    <label for="judul_laporan" class="form-label">Judul Laporan <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="judul_laporan" name="judul_laporan" required>
                                </div>
                                <div class="mb-3">
                                    <label for="isi_laporan" class="form-label">Isi Laporan Lengkap <span class="text-danger">*</span></label>
                                    <textarea class="form-control" id="isi_laporan" name="isi_laporan" rows="6" required></textarea>
                                </div>
                                 <div class="mb-3">
                                    <label for="lokasi_kejadian" class="form-label">Lokasi Kejadian (Opsional)</label>
                                    <input type="text" class="form-control" id="lokasi_kejadian" name="lokasi_kejadian" placeholder="Contoh: Depan Balai Desa, RT 01/RW 02">
                                </div>
                                <div class="mb-4">
                                    <label for="lampiran" class="form-label">Lampiran (Opsional)</label>
                                    <input class="form-control" type="file" id="lampiran" name="lampiran" accept="image/*,video/*,.pdf">
                                    <div class="form-text">
                                        Anda dapat melampirkan foto, video, atau dokumen PDF sebagai bukti.
                                    </div>
                                </div>
                                <div class="d-grid">
                                    <button type="submit" class="btn btn-submit">
                                        <i class="bi bi-send-fill me-2"></i> Kirim Laporan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<footer class="bg-dark text-white text-center p-4 mt-4">
    <div class="container">
        <p class="mb-0">&copy; <?= date('Y') ?> Pemerintah Desa Tampingan. All Rights Reserved.</p>
    </div>
</footer>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Tampilkan notifikasi dari server jika ada
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (!empty($pesan) && !empty($jenis_pesan)): ?>
            Swal.fire({
                icon: '<?= $jenis_pesan; ?>',
                title: '<?= ($jenis_pesan == "success" ? "Berhasil!" : "Oops..."); ?>',
                text: '<?= addslashes($pesan); ?>',
                confirmButtonColor: 'var(--primary-color)'
            });
        <?php endif; ?>

        // Client-side validation (sederhana)
        const form = document.getElementById('formLaporan');
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
                // Menampilkan pesan error default dari browser
                Swal.fire({
                    icon: 'warning',
                    title: 'Form Belum Lengkap',
                    text: 'Mohon isi semua kolom yang ditandai dengan bintang (*).',
                    confirmButtonColor: 'var(--primary-color)'
                });
            }
            form.classList.add('was-validated');
        }, false);
    });
</script>

</body>
</html>
