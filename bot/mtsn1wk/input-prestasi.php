<?php
// Memulai session untuk menangani pesan notifikasi
session_start();

// Inisialisasi variabel pesan
$pesan_sukses = '';
$pesan_error = '';

// Memeriksa apakah form telah disubmit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Menyertakan file koneksi database
    include 'koneksi.php';

    // Mengambil data dari form dan membersihkannya
    $nama_siswa = trim($_POST['nama_siswa']);
    $nama_prestasi = trim($_POST['nama_prestasi']);
    $tingkat = trim($_POST['tingkat']);
    $tahun = trim($_POST['tahun']);
    $deskripsi = trim($_POST['deskripsi']);
    $nama_foto_db = null;

    // Logika untuk menangani file upload
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $target_dir = "img/prestasi/";
        $nama_file_unik = uniqid() . '_' . basename($_FILES["foto"]["name"]);
        $target_file = $target_dir . $nama_file_unik;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($_FILES["foto"]["tmp_name"]);
        if ($check !== false) {
            if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                $nama_foto_db = $nama_file_unik;
            } else {
                // Set pesan error di session jika upload gagal
                $_SESSION['error'] = "Maaf, terjadi error saat mengupload file Anda.";
            }
        } else {
            $_SESSION['error'] = "File yang diupload bukan gambar.";
        }
    }

    // Menyimpan data ke database jika tidak ada error upload
    if (!isset($_SESSION['error'])) {
        $sql = "INSERT INTO prestasi (nama_siswa, nama_prestasi, tingkat, tahun, deskripsi, foto_url) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $koneksi->prepare($sql);
        
        if ($stmt) {
            $stmt->bind_param("ssssss", $nama_siswa, $nama_prestasi, $tingkat, $tahun, $deskripsi, $nama_foto_db);
            if ($stmt->execute()) {
                // --- PERUBAHAN UTAMA 1 ---
                // Set pesan sukses di session
                $_SESSION['sukses'] = "Prestasi baru berhasil ditambahkan!";
            } else {
                $_SESSION['error'] = "Gagal menyimpan data ke database: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $_SESSION['error'] = "Gagal mempersiapkan statement: " . $koneksi->error;
        }
    }
    
    $koneksi->close();
    
    // Redirect kembali ke halaman ini untuk menampilkan notifikasi
    header("Location: input-prestasi.php");
    exit();
}

// --- PERUBAHAN UTAMA 2 ---
// Mengambil pesan dari session untuk ditampilkan oleh SweetAlert2
if (isset($_SESSION['sukses'])) {
    $pesan_sukses = $_SESSION['sukses'];
    unset($_SESSION['sukses']); // Hapus pesan setelah diambil
}
if (isset($_SESSION['error'])) {
    $pesan_error = $_SESSION['error'];
    unset($_SESSION['error']); // Hapus pesan setelah diambil
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Prestasi Baru - Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Menambahkan CSS SweetAlert2 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body style="background-color: #f8f9fa;">

    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h4 class="mb-0">Form Input Prestasi Siswa</h4>
                    </div>
                    <div class="card-body p-4">
                        <!-- Formulir Input (tidak ada notifikasi Bootstrap lagi di sini) -->
                        <form action="input-prestasi.php" method="post" enctype="multipart/form-data">
                            <!-- ... (semua elemen form tetap sama) ... -->
                            <div class="mb-3">
                                <label for="nama_siswa" class="form-label">Nama Siswa / Tim</label>
                                <input type="text" class="form-control" id="nama_siswa" name="nama_siswa" required>
                            </div>
                            <div class="mb-3">
                                <label for="nama_prestasi" class="form-label">Nama Prestasi / Kompetisi</label>
                                <input type="text" class="form-control" id="nama_prestasi" name="nama_prestasi" required>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="tingkat" class="form-label">Tingkat</label>
                                    <select class="form-select" id="tingkat" name="tingkat" required>
                                        <option value="">-- Pilih Tingkat --</option>
                                        <option value="Sekolah">Sekolah</option>
                                        <option value="Kecamatan">Kecamatan</option>
                                        <option value="Kabupaten">Kabupaten</option>
                                        <option value="Provinsi">Provinsi</option>
                                        <option value="Nasional">Nasional</option>
                                        <option value="Internasional">Internasional</option>
                                    </select>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="tahun" class="form-label">Tahun</label>
                                    <input type="number" class="form-control" id="tahun" name="tahun" min="2000" max="<?php echo date('Y'); ?>" placeholder="Contoh: <?php echo date('Y'); ?>" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="deskripsi" class="form-label">Deskripsi Singkat (Opsional)</label>
                                <textarea class="form-control" id="deskripsi" name="deskripsi" rows="3"></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Upload Foto (Opsional)</label>
                                <input class="form-control" type="file" id="foto" name="foto" accept="image/png, image/jpeg, image/jpg">
                            </div>
                            <hr>
                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="index.php" class="btn btn-secondary me-md-2">Kembali ke Beranda</a>
                                <button type="submit" class="btn btn-success">Simpan Prestasi</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Menambahkan JS SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- --- PERUBAHAN UTAMA 3 --- -->
    <!-- Script untuk menampilkan notifikasi -->
    <script>
        <?php if (!empty($pesan_sukses)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: '<?php echo addslashes($pesan_sukses); ?>',
            timer: 3000, // Notifikasi hilang setelah 3 detik
            showConfirmButton: false
        });
        <?php endif; ?>

        <?php if (!empty($pesan_error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: '<?php echo addslashes($pesan_error); ?>'
        });
        <?php endif; ?>
    </script>

</body>
</html>