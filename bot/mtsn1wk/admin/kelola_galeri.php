<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- AMBIL SEMUA DATA GALERI DARI DATABASE ---
$galleries = [];
$sql = "SELECT id, foto_url, kategori, deskripsi FROM galeri ORDER BY tanggal_upload DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $galleries[] = $row;
    }
    $result->free();
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - Admin MTsN 1 Way Kanan</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        /* CSS Lengkap (tidak perlu diubah, sama seperti sebelumnya) */
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--danger-hover:#c0392b;--info-color:#3498db;--info-hover:#2980b9}*{margin:0;padding:0;box-sizing:border-box}body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}.sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column}.sidebar-header{padding:20px;text-align:center;border-bottom:1px solid #34495e}.sidebar-header h3{font-weight:600}.sidebar-nav{flex-grow:1;list-style:none;padding-top:20px}.sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px}.sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px}.sidebar-nav li a:hover,.sidebar-nav li.active a{background-color:var(--sidebar-active)}.main-content{margin-left:260px;width:calc(100% - 260px);padding:20px}.page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;flex-wrap:wrap;gap:15px}.page-header h1{font-size:24px;font-weight:600;color:var(--text-color)}.btn-add{background-color:var(--primary-color);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;transition:background-color .3s ease;display:inline-flex;align-items:center}.btn-add:hover{background-color:var(--primary-hover)}.btn-add i{margin-right:8px}.gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:25px}.gallery-card{background-color:#fff;border-radius:12px;box-shadow:var(--card-shadow);overflow:hidden;display:flex;flex-direction:column;transition:all .3s ease}.gallery-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.12)}.gallery-card .card-image{width:100%;height:200px;object-fit:cover;border-bottom:1px solid #f0f0f0}.gallery-card .card-body{padding:15px;flex-grow:1;display:flex;flex-direction:column}.gallery-card .card-category{background-color:rgba(40,167,69,.1);color:var(--primary-hover);padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;margin-bottom:10px;align-self:flex-start}.gallery-card .card-description{font-size:15px;color:#555;line-height:1.5;flex-grow:1}.gallery-card .card-actions{padding:15px;border-top:1px solid #f0f0f0;display:flex;gap:10px;justify-content:flex-end}.btn-action{padding:6px 14px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px}.btn-edit{background-color:var(--info-color)}.btn-edit:hover{background-color:var(--info-hover)}.btn-delete{background-color:var(--danger-color)}.btn-delete:hover{background-color:var(--danger-hover)}.alert-info{background-color:#e9f5ff;color:#0c5460;padding:20px;border:1px solid #bee5eb;border-radius:8px;text-align:center;font-size:16px}
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Kelola Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Kelola Prestasi</span></a></li>
            <li  class="active"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Kelola Galeri</span></a></li>
            <li><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Kelola Operator</span></a></li>
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Galeri</h1>
            <a href="tambah_galeri.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Galeri</a>
        </header>

        <section class="content">
            <?php if (empty($galleries)): ?>
                <div class="alert alert-info">
                    🖼️ Belum ada data galeri. Silakan tambahkan foto baru dengan menekan tombol "Tambah Galeri".
                </div>
            <?php else: ?>
                <div class="gallery-grid">
                    <?php foreach ($galleries as $item): ?>
                    <div class="gallery-card">
                        <img src="uploads/galeri/<?php echo htmlspecialchars($item['foto_url']); ?>" alt="<?php echo htmlspecialchars($item['deskripsi']); ?>" class="card-image">
                        <div class="card-body">
                            <span class="card-category"><?php echo htmlspecialchars($item['kategori']); ?></span>
                            <p class="card-description"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                        </div>
                        <div class="card-actions">
                            <a href="edit_galeri.php?id=<?php echo $item['id']; ?>" class="btn-action btn-edit"><i class="fas fa-pencil-alt"></i> Edit</a>
                            <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $item['id']; ?>)"><i class="fas fa-trash"></i> Hapus</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // BARU: Fungsi untuk konfirmasi hapus dengan SweetAlert2
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Foto ini akan dihapus secara permanen dan tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Jika pengguna menekan "Ya", arahkan ke skrip hapus
                    window.location.href = 'hapus_galeri.php?id=' + id;
                }
            });
        }

        <?php
        // Script untuk notifikasi sukses/gagal (tidak berubah)
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({
                title: 'Berhasil!',
                text: '" . addslashes($_SESSION['success_message']) . "',
                icon: 'success',
                timer: 2500,
                showConfirmButton: false
            });";
            unset($_SESSION['success_message']);
        }

        if (isset($_SESSION['error_message'])) {
            echo "Swal.fire({
                title: 'Gagal!',
                text: '" . addslashes($_SESSION['error_message']) . "',
                icon: 'error',
                confirmButtonColor: '#d33'
            });";
            unset($_SESSION['error_message']);
        }
        ?>
    </script>

</body>
</html>