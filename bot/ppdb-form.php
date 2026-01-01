<?php
// =================================================================
// FILE: PPDB-FORM.PHP
// FUNGSI: Formulir pendaftaran siswa baru online.
// =================================================================
session_start();
include 'koneksi.php';

// -----------------------------------------------------------------
// BAGIAN 1: PEMROSESAN FORM SAAT DI-SUBMIT
// -----------------------------------------------------------------
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // --- Fungsi untuk upload file ---
    function upload_file($file_input_name, $target_dir) {
        if (isset($_FILES[$file_input_name]) && $_FILES[$file_input_name]['error'] == 0) {
            $nama_file_asli = basename($_FILES[$file_input_name]["name"]);
            $nama_file_unik = uniqid() . '-' . preg_replace("/[^a-zA-Z0-9.]/", "_", $nama_file_asli);
            $target_file = $target_dir . $nama_file_unik;
            
            if (move_uploaded_file($_FILES[$file_input_name]["tmp_name"], $target_file)) {
                return $nama_file_unik;
            }
        }
        return null;
    }

    // --- Generate Nomor Pendaftaran ---
    $nomor_pendaftaran = 'PPDB-' . date('Y') . '-' . mt_rand(1000, 9999);

    // --- Kumpulkan data dalam satu array ---
    $data_pendaftar = [
        'nomor_pendaftaran' => $nomor_pendaftaran,
        'nama_lengkap' => $_POST['nama_lengkap'] ?? null,
        'nisn' => $_POST['nisn'] ?? null,
        'nik_siswa' => $_POST['nik_siswa'] ?? null,
        'tempat_lahir' => $_POST['tempat_lahir'] ?? null,
        'tanggal_lahir' => $_POST['tanggal_lahir'] ?? null,
        'jenis_kelamin' => $_POST['jenis_kelamin'] ?? null,
        'agama' => $_POST['agama'] ?? 'Islam',
        'kewarganegaraan' => $_POST['kewarganegaraan'] ?? null,
        'anak_ke' => !empty($_POST['anak_ke']) ? (int)$_POST['anak_ke'] : null,
        'jumlah_saudara' => !empty($_POST['jumlah_saudara']) ? (int)$_POST['jumlah_saudara'] : null,
        'alamat_jalan' => $_POST['alamat_jalan'] ?? null,
        'alamat_rt_rw' => $_POST['alamat_rt_rw'] ?? null,
        'alamat_desa' => $_POST['alamat_desa'] ?? null,
        'alamat_kecamatan' => $_POST['alamat_kecamatan'] ?? null,
        'alamat_kabupaten' => $_POST['alamat_kabupaten'] ?? null,
        'alamat_provinsi' => $_POST['alamat_provinsi'] ?? null,
        'alamat_kode_pos' => $_POST['alamat_kode_pos'] ?? null,
        'no_hp_siswa' => $_POST['no_hp_siswa'] ?? null,
        'email_siswa' => $_POST['email_siswa'] ?? null,
        'sekolah_asal_nama' => $_POST['sekolah_asal_nama'] ?? null,
        'sekolah_asal_npsn' => $_POST['sekolah_asal_npsn'] ?? null,
        'sekolah_asal_status' => $_POST['sekolah_asal_status'] ?? null,
        'sekolah_asal_alamat' => $_POST['sekolah_asal_alamat'] ?? null,
        'tahun_lulus' => !empty($_POST['tahun_lulus']) ? (int)$_POST['tahun_lulus'] : null,
        'ayah_nama' => $_POST['ayah_nama'] ?? null,
        'ayah_nik' => $_POST['ayah_nik'] ?? null,
        'ayah_pendidikan' => $_POST['ayah_pendidikan'] ?? null,
        'ayah_pekerjaan' => $_POST['ayah_pekerjaan'] ?? null,
        'ayah_penghasilan' => $_POST['ayah_penghasilan'] ?? null,
        'ayah_no_hp' => $_POST['ayah_no_hp'] ?? null,
        'ibu_nama' => $_POST['ibu_nama'] ?? null,
        'ibu_nik' => $_POST['ibu_nik'] ?? null,
        'ibu_pendidikan' => $_POST['ibu_pendidikan'] ?? null,
        'ibu_pekerjaan' => $_POST['ibu_pekerjaan'] ?? null,
        'ibu_penghasilan' => $_POST['ibu_penghasilan'] ?? null,
        'ibu_no_hp' => $_POST['ibu_no_hp'] ?? null,
        'wali_nama' => $_POST['wali_nama'] ?? null,
        'wali_hubungan' => $_POST['wali_hubungan'] ?? null,
        'wali_alamat' => $_POST['wali_alamat'] ?? null,
        'wali_pekerjaan' => $_POST['wali_pekerjaan'] ?? null,
        'wali_no_hp' => $_POST['wali_no_hp'] ?? null,
        'berkas_foto' => upload_file('berkas_foto', 'admin/uploads/ppdb/'),
        'berkas_kk' => upload_file('berkas_kk', 'admin/uploads/ppdb/'),
        'berkas_akta' => upload_file('berkas_akta', 'admin/uploads/ppdb/'),
        'berkas_ijazah_skl' => upload_file('berkas_ijazah_skl', 'admin/uploads/ppdb/'),
        'berkas_kartu_bantuan' => upload_file('berkas_kartu_bantuan', 'admin/uploads/ppdb/'),
        'jalur_pendaftaran' => $_POST['jalur_pendaftaran'] ?? null,
        'prestasi_nama_lomba' => $_POST['prestasi_nama_lomba'] ?? null,
        'prestasi_tingkat' => $_POST['prestasi_tingkat'] ?? null,
        'prestasi_peringkat' => $_POST['prestasi_peringkat'] ?? null,
        'prestasi_tahun' => !empty($_POST['prestasi_tahun']) ? (int)$_POST['prestasi_tahun'] : null,
        'berkas_sertifikat_prestasi' => upload_file('berkas_sertifikat_prestasi', 'admin/uploads/ppdb/')
    ];

    // --- Simpan ke Database ---
    $sql_columns = implode(", ", array_keys($data_pendaftar));
    $sql_placeholders = implode(", ", array_fill(0, count($data_pendaftar), '?'));
    $sql = "INSERT INTO calon_siswa ($sql_columns) VALUES ($sql_placeholders)";
    
    $stmt = $koneksi->prepare($sql);
    
    if ($stmt) {
        // PERBAIKAN: Menyesuaikan tipe data (i untuk integer, s untuk string)
        $types = "sssssssssiissssssssssssisssssssssssssssssssssssis";
        $values = array_values($data_pendaftar);
        
        $stmt->bind_param($types, ...$values);

        if ($stmt->execute()) {
            $_SESSION['sukses'] = "Pendaftaran Anda berhasil! Nomor pendaftaran Anda adalah <strong>{$data_pendaftar['nomor_pendaftaran']}</strong>. Silakan simpan nomor ini untuk pengecekan status.";
        } else {
            $_SESSION['error'] = "Gagal menyimpan data pendaftaran: " . $stmt->error;
        }
        $stmt->close();
    } else {
        $_SESSION['error'] = "Gagal mempersiapkan statement SQL: " . $koneksi->error;
    }

    $koneksi->close();
    header("Location: ppdb-form.php");
    exit();
}

// Ambil pesan notifikasi dari session.
$pesan_sukses = $_SESSION['sukses'] ?? null;
$pesan_error  = $_SESSION['error'] ?? null;
unset($_SESSION['sukses'], $_SESSION['error']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Formulir PPDB Online - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body style="background-color: #f4f7f6;">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolled">
        <!-- ... (Salin kode navbar lengkap dari index.php ke sini) ... -->
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Formulir Pendaftaran</h1>
            <p class="page-subtitle">Penerimaan Peserta Didik Baru (PPDB) Online</p>
        </div>
    </header>

    <!-- Konten Formulir -->
    <main class="py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="card shadow-sm">
                        <div class="card-body p-4 p-md-5">
                            <h3 class="card-title text-center mb-4">Isi Data dengan Benar dan Lengkap</h3>
                            
                            <form action="ppdb-form.php" method="post" enctype="multipart/form-data" id="ppdbForm">
                                <div class="accordion" id="formAccordion">

                                    <!-- Bagian 1: Data Diri Siswa (Lengkap) -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                <strong>Bagian 1: Data Diri Calon Siswa</strong>
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#formAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-3"><label for="nama_lengkap" class="form-label">Nama Lengkap (sesuai Akta)</label><input type="text" class="form-control" id="nama_lengkap" name="nama_lengkap" placeholder="Contoh: Ahmad Fauzan" required></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="nisn" class="form-label">NISN</label><input type="text" class="form-control" id="nisn" name="nisn" placeholder="Masukkan 10 digit NISN" required></div><div class="col-md-6 mb-3"><label for="nik_siswa" class="form-label">NIK Siswa</label><input type="text" class="form-control" id="nik_siswa" name="nik_siswa" placeholder="Masukkan 16 digit NIK dari KK" required></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="tempat_lahir" class="form-label">Tempat Lahir</label><input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Contoh: Way Kanan" required></div><div class="col-md-6 mb-3"><label for="tanggal_lahir" class="form-label">Tanggal Lahir</label><input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="jenis_kelamin" class="form-label">Jenis Kelamin</label><select class="form-select" id="jenis_kelamin" name="jenis_kelamin" required><option value="">Pilih...</option><option value="Laki-laki">Laki-laki</option><option value="Perempuan">Perempuan</option></select></div><div class="col-md-6 mb-3"><label for="agama" class="form-label">Agama</label><input type="text" class="form-control" id="agama" name="agama" value="Islam" readonly></div></div>
                                                <div class="row"><div class="col-md-4 mb-3"><label for="kewarganegaraan" class="form-label">Kewarganegaraan</label><input type="text" class="form-control" id="kewarganegaraan" name="kewarganegaraan" value="WNI" required></div><div class="col-md-4 mb-3"><label for="anak_ke" class="form-label">Anak ke-</label><input type="number" class="form-control" id="anak_ke" name="anak_ke" placeholder="Contoh: 1"></div><div class="col-md-4 mb-3"><label for="jumlah_saudara" class="form-label">Jumlah Saudara</label><input type="number" class="form-control" id="jumlah_saudara" name="jumlah_saudara" placeholder="Contoh: 3"></div></div>
                                                <div class="mb-3"><label for="alamat_jalan" class="form-label">Alamat Jalan/Dusun</label><textarea class="form-control" id="alamat_jalan" name="alamat_jalan" rows="2" placeholder="Contoh: Jl. Hi. Ibrahim No. 59" required></textarea></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="alamat_rt_rw" class="form-label">RT/RW</label><input type="text" class="form-control" id="alamat_rt_rw" name="alamat_rt_rw" placeholder="Contoh: 001/002"></div><div class="col-md-6 mb-3"><label for="alamat_desa" class="form-label">Desa/Kelurahan</label><input type="text" class="form-control" id="alamat_desa" name="alamat_desa" placeholder="Contoh: Kasui Pasar" required></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="alamat_kecamatan" class="form-label">Kecamatan</label><input type="text" class="form-control" id="alamat_kecamatan" name="alamat_kecamatan" placeholder="Contoh: Kasui" required></div><div class="col-md-6 mb-3"><label for="alamat_kabupaten" class="form-label">Kabupaten/Kota</label><input type="text" class="form-control" id="alamat_kabupaten" name="alamat_kabupaten" placeholder="Contoh: Way Kanan" required></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="alamat_provinsi" class="form-label">Provinsi</label><input type="text" class="form-control" id="alamat_provinsi" name="alamat_provinsi" placeholder="Contoh: Lampung" required></div><div class="col-md-6 mb-3"><label for="alamat_kode_pos" class="form-label">Kode Pos</label><input type="text" class="form-control" id="alamat_kode_pos" name="alamat_kode_pos" placeholder="Contoh: 34765"></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="no_hp_siswa" class="form-label">No. HP/WA Siswa</label><input type="tel" class="form-control" id="no_hp_siswa" name="no_hp_siswa" placeholder="Contoh: 081234567890"></div><div class="col-md-6 mb-3"><label for="email_siswa" class="form-label">Email Siswa (Opsional)</label><input type="email" class="form-control" id="email_siswa" name="email_siswa" placeholder="Contoh: siswa@email.com"></div></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bagian 2: Data Sekolah Asal (Lengkap) -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                <strong>Bagian 2: Data Sekolah Asal</strong>
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#formAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-3"><label for="sekolah_asal_nama" class="form-label">Nama SD/MI Asal</label><input type="text" class="form-control" id="sekolah_asal_nama" name="sekolah_asal_nama" placeholder="Contoh: SDN 1 Kasui" required></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="sekolah_asal_npsn" class="form-label">NPSN Sekolah Asal</label><input type="text" class="form-control" id="sekolah_asal_npsn" name="sekolah_asal_npsn" placeholder="Masukkan 8 digit NPSN"></div><div class="col-md-6 mb-3"><label for="sekolah_asal_status" class="form-label">Status Sekolah</label><select class="form-select" id="sekolah_asal_status" name="sekolah_asal_status"><option value="Negeri">Negeri</option><option value="Swasta">Swasta</option></select></div></div>
                                                <div class="mb-3"><label for="sekolah_asal_alamat" class="form-label">Alamat Sekolah Asal</label><textarea class="form-control" id="sekolah_asal_alamat" name="sekolah_asal_alamat" rows="2" placeholder="Masukkan alamat lengkap sekolah asal"></textarea></div>
                                                <div class="mb-3"><label for="tahun_lulus" class="form-label">Tahun Lulus</label><input type="number" class="form-control" id="tahun_lulus" name="tahun_lulus" placeholder="Contoh: <?php echo date('Y'); ?>" required></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bagian 3: Data Orang Tua / Wali (Lengkap) -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                <strong>Bagian 3: Data Orang Tua / Wali</strong>
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#formAccordion">
                                            <div class="accordion-body">
                                                <h5 class="mt-2">Data Ayah</h5><hr>
                                                <div class="mb-3"><label for="ayah_nama" class="form-label">Nama Lengkap Ayah</label><input type="text" class="form-control" id="ayah_nama" name="ayah_nama" required></div>
                                                <div class="mb-3"><label for="ayah_nik" class="form-label">NIK Ayah</label><input type="text" class="form-control" id="ayah_nik" name="ayah_nik"></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="ayah_pendidikan" class="form-label">Pendidikan Terakhir</label><input type="text" class="form-control" id="ayah_pendidikan" name="ayah_pendidikan"></div><div class="col-md-6 mb-3"><label for="ayah_pekerjaan" class="form-label">Pekerjaan</label><input type="text" class="form-control" id="ayah_pekerjaan" name="ayah_pekerjaan"></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="ayah_penghasilan" class="form-label">Penghasilan Bulanan</label><input type="text" class="form-control" id="ayah_penghasilan" name="ayah_penghasilan"></div><div class="col-md-6 mb-3"><label for="ayah_no_hp" class="form-label">No. HP/WA Ayah</label><input type="tel" class="form-control" id="ayah_no_hp" name="ayah_no_hp"></div></div>
                                                
                                                <h5 class="mt-4">Data Ibu</h5><hr>
                                                <div class="mb-3"><label for="ibu_nama" class="form-label">Nama Lengkap Ibu</label><input type="text" class="form-control" id="ibu_nama" name="ibu_nama" required></div>
                                                <div class="mb-3"><label for="ibu_nik" class="form-label">NIK Ibu</label><input type="text" class="form-control" id="ibu_nik" name="ibu_nik"></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="ibu_pendidikan" class="form-label">Pendidikan Terakhir</label><input type="text" class="form-control" id="ibu_pendidikan" name="ibu_pendidikan"></div><div class="col-md-6 mb-3"><label for="ibu_pekerjaan" class="form-label">Pekerjaan</label><input type="text" class="form-control" id="ibu_pekerjaan" name="ibu_pekerjaan"></div></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="ibu_penghasilan" class="form-label">Penghasilan Bulanan</label><input type="text" class="form-control" id="ibu_penghasilan" name="ibu_penghasilan"></div><div class="col-md-6 mb-3"><label for="ibu_no_hp" class="form-label">No. HP/WA Ibu</label><input type="tel" class="form-control" id="ibu_no_hp" name="ibu_no_hp"></div></div>

                                                <h5 class="mt-4">Data Wali (Isi jika tinggal dengan wali)</h5><hr>
                                                <div class="mb-3"><label for="wali_nama" class="form-label">Nama Lengkap Wali</label><input type="text" class="form-control" id="wali_nama" name="wali_nama"></div>
                                                <div class="mb-3"><label for="wali_hubungan" class="form-label">Hubungan dengan Siswa</label><input type="text" class="form-control" id="wali_hubungan" name="wali_hubungan"></div>
                                                <div class="mb-3"><label for="wali_alamat" class="form-label">Alamat Wali</label><textarea class="form-control" id="wali_alamat" name="wali_alamat" rows="2"></textarea></div>
                                                <div class="row"><div class="col-md-6 mb-3"><label for="wali_pekerjaan" class="form-label">Pekerjaan Wali</label><input type="text" class="form-control" id="wali_pekerjaan" name="wali_pekerjaan"></div><div class="col-md-6 mb-3"><label for="wali_no_hp" class="form-label">No. HP/WA Wali</label><input type="tel" class="form-control" id="wali_no_hp" name="wali_no_hp"></div></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bagian 4: Upload Berkas (Lengkap) -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                                <strong>Bagian 4: Upload Berkas Digital</strong>
                                            </button>
                                        </h2>
                                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#formAccordion">
                                            <div class="accordion-body">
                                                <div class="alert alert-warning small"><strong>Perhatian:</strong> Ukuran file maksimal 2MB per berkas. Format yang diizinkan: JPG, PNG, PDF.</div>
                                                <div class="mb-3"><label for="berkas_foto" class="form-label">Pas Foto 3x4 (Latar Biru)</label><input class="form-control" type="file" name="berkas_foto" required></div>
                                                <div class="mb-3"><label for="berkas_kk" class="form-label">Scan Kartu Keluarga (KK)</label><input class="form-control" type="file" name="berkas_kk" required></div>
                                                <div class="mb-3"><label for="berkas_akta" class="form-label">Scan Akta Kelahiran</label><input class="form-control" type="file" name="berkas_akta" required></div>
                                                <div class="mb-3"><label for="berkas_ijazah_skl" class="form-label">Scan Ijazah / SKL</label><input class="form-control" type="file" name="berkas_ijazah_skl" required></div>
                                                <div class="mb-3"><label for="berkas_kartu_bantuan" class="form-label">Scan Kartu Bantuan (KIP/PKH, jika ada)</label><input class="form-control" type="file" name="berkas_kartu_bantuan"></div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Bagian 5: Jalur Pendaftaran (Lengkap) -->
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                                <strong>Bagian 5: Jalur Pendaftaran & Prestasi</strong>
                                            </button>
                                        </h2>
                                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#formAccordion">
                                            <div class="accordion-body">
                                                <div class="mb-3">
                                                    <label for="jalur_pendaftaran" class="form-label">Pilih Jalur Pendaftaran</label>
                                                    <select class="form-select" name="jalur_pendaftaran" id="jalur_pendaftaran" required>
                                                        <option value="Reguler" selected>Reguler</option>
                                                        <option value="Prestasi">Prestasi</option>
                                                        <option value="Afirmasi">Afirmasi (Kurang Mampu)</option>
                                                    </select>
                                                </div>
                                                <div id="prestasi-section" class="d-none border-top pt-3 mt-3">
                                                    <h5 class="mb-3">Data Prestasi (isi jika memilih Jalur Prestasi)</h5>
                                                    <div class="mb-3"><label for="prestasi_nama_lomba" class="form-label">Nama Lomba/Kompetisi</label><input type="text" class="form-control" id="prestasi_nama_lomba" name="prestasi_nama_lomba"></div>
                                                    <div class="row"><div class="col-md-6 mb-3"><label for="prestasi_tingkat" class="form-label">Tingkat</label><input type="text" class="form-control" id="prestasi_tingkat" name="prestasi_tingkat"></div><div class="col-md-6 mb-3"><label for="prestasi_peringkat" class="form-label">Peringkat/Juara</label><input type="text" class="form-control" id="prestasi_peringkat" name="prestasi_peringkat"></div></div>
                                                    <div class="row"><div class="col-md-6 mb-3"><label for="prestasi_tahun" class="form-label">Tahun</label><input type="number" class="form-control" id="prestasi_tahun" name="prestasi_tahun"></div><div class="col-md-6 mb-3"><label for="berkas_sertifikat_prestasi" class="form-label">Upload Sertifikat</label><input class="form-control" type="file" name="berkas_sertifikat_prestasi"></div></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                </div> <!-- Akhir Accordion -->

                                <!-- Pernyataan & Tombol Submit -->
                                <div class="mt-4 form-check">
                                    <input type="checkbox" class="form-check-input" id="pernyataan" required>
                                    <label class="form-check-label" for="pernyataan">Saya menyatakan bahwa semua data yang saya isikan adalah benar dan dapat dipertanggungjawabkan.</label>
                                </div>
                                <div class="d-grid gap-2 mt-4">
                                    <button class="btn btn-success btn-lg" type="submit" id="submitBtn">Kirim Formulir Pendaftaran</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <!-- JavaScript Libraries -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>

    <!-- Custom Script -->
    <script>
        // Script untuk menampilkan notifikasi
        <?php if ($pesan_sukses): ?>
            Swal.fire({
                icon: 'success',
                title: 'Pendaftaran Berhasil!',
                html: '<?php echo addslashes($pesan_sukses); ?>',
                confirmButtonText: 'Baik, saya mengerti'
            });
        <?php endif; ?>
        <?php if ($pesan_error): ?>
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: '<?php echo addslashes($pesan_error); ?>'
            });
        <?php endif; ?>

        // Script untuk menampilkan/menyembunyikan bagian prestasi
        document.getElementById('jalur_pendaftaran').addEventListener('change', function () {
            var prestasiSection = document.getElementById('prestasi-section');
            if (this.value === 'Prestasi') {
                prestasiSection.classList.remove('d-none');
            } else {
                prestasiSection.classList.add('d-none');
            }
        });

        // Script untuk validasi form sebelum submit
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('ppdbForm');
            const submitButton = document.getElementById('submitBtn');
            const requiredFields = form.querySelectorAll('[required]');

            function validateForm() {
                let allValid = true;
                
                requiredFields.forEach(field => {
                    if (field.type === 'checkbox') {
                        if (!field.checked) {
                            allValid = false;
                        }
                    } else {
                        if (field.value.trim() === '') {
                            allValid = false;
                        }
                    }
                });

                submitButton.disabled = !allValid;
            }

            requiredFields.forEach(field => {
                field.addEventListener('input', validateForm);
                field.addEventListener('change', validateForm);
            });

            validateForm();
        });
    </script>
</body>
</html>
