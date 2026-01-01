<?php
// Sertakan file koneksi database.
include 'koneksi.php';

// Query untuk mengambil semua data dari tabel struktur_organisasi, diurutkan berdasarkan kolom 'urutan'.
$sql = "SELECT * FROM struktur_organisasi ORDER BY urutan ASC";
$result = $koneksi->query($sql);

// Siapkan array kosong untuk mengelompokkan data berdasarkan kategori.
$struktur_by_kategori = [];
if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Masukkan setiap baris data ke dalam array sesuai kategorinya.
        $struktur_by_kategori[$row['kategori_jabatan']][] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struktur Organisasi - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top scrolled">
        <!-- ... (Salin kode navbar lengkap dari index.php ke sini, pastikan link aktifnya disesuaikan) ... -->
    </nav>

    <!-- Page Header -->
    <header class="page-header">
        <div class="container">
            <h1 class="page-title">Struktur Organisasi</h1>
            <p class="page-subtitle">Pimpinan, Staf Pengajar, dan Tenaga Kependidikan MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <!-- Konten Utama Halaman Struktur -->
    <main class="structure-page-section">
        <div class="container">
            <?php
            // Periksa apakah ada data untuk ditampilkan.
            if (!empty($struktur_by_kategori)):
                // Lakukan perulangan untuk setiap kategori jabatan (e.g., 'Pimpinan', 'Tata Usaha').
                foreach ($struktur_by_kategori as $kategori => $anggota):
            ?>
                    <!-- Judul Kategori -->
                    <div class="row">
                        <div class="col-12 mt-5">
                            <h2 class="structure-category-title"><?php echo htmlspecialchars($kategori); ?></h2>
                        </div>
                    </div>
                    
                    <!-- Daftar Anggota per Kategori -->
                    <div class="row g-4 justify-content-center">
                        <?php
                        // Lakukan perulangan untuk setiap anggota dalam kategori ini.
                        foreach ($anggota as $orang):
                            // Siapkan path gambar.
                            $foto = (!empty($orang['foto_url'])) ? 'admin/uploads/struktur/' . htmlspecialchars($orang['foto_url']) : 'https://placehold.co/400x400/E0F2F1/198754?text=Foto';
                        ?>
                            <div class="col-lg-3 col-md-4 col-sm-6">
                                <div class="structure-card">
                                    <div class="structure-card-img">
                                        <img src="<?php echo $foto; ?>" alt=" <?php echo htmlspecialchars($orang['nama_lengkap']); ?>">
                                    </div>
                                    <div class="structure-card-body">
                                        <h5 class="structure-name"><?php echo htmlspecialchars($orang['nama_lengkap']); ?></h5>
                                        <p class="structure-position"><?php echo htmlspecialchars($orang['jabatan']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; // Akhir loop anggota ?>
                    </div>
            <?php
                endforeach; // Akhir loop kategori
            else:
                // Tampilkan pesan jika tidak ada data sama sekali.
                echo '<p class="text-center">Data struktur organisasi belum tersedia.</p>';
            endif;
            ?>
        </div>
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php
    // Tutup koneksi database.
    $koneksi->close();
    ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
