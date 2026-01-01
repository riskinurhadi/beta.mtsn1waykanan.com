<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
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
    
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--danger-hover:#c0392b;--info-color:#3498db;--info-hover:#2980b9;--warning-color:#f39c12}
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Poppins',sans-serif;background-color:var(--main-bg);display:flex}
        .sidebar{width:260px;background-color:var(--sidebar-bg);color:var(--sidebar-text);height:100vh;position:fixed;left:0;top:0;display:flex;flex-direction:column;transition:width .3s ease;z-index:1000;overflow-y:auto}
        .sidebar-header{padding:20px;text-align:center;border-bottom:1px solid var(--sidebar-active);flex-shrink:0}
        .sidebar-header h3{font-weight:600;color:#fff}
        .sidebar-nav{flex-grow:1;list-style:none;padding:20px 0;padding-bottom:40px}
        .sidebar-nav li a{display:flex;align-items:center;padding:15px 20px;color:var(--sidebar-text);text-decoration:none;transition:background-color .3s ease;font-size:15px;position:relative}
        .sidebar-nav li a i{width:30px;font-size:18px;margin-right:10px;text-align:center}
        .sidebar-nav li a:hover,.sidebar-nav li.active>a{background-color:var(--sidebar-active)}
        .sidebar-nav li.active>a::before{content:'';position:absolute;left:0;top:0;height:100%;width:4px;background-color:var(--primary-color);border-radius:0 4px 4px 0}
        .main-content{margin-left:260px;width:calc(100% - 260px);padding:20px;transition:all .3s ease}
        .page-header{display:flex;justify-content:space-between;align-items:center;margin-bottom:30px;flex-wrap:wrap;gap:15px}
        .page-header h1{font-size:24px;font-weight:600}
        .header-actions{display:flex;gap:15px;align-items:center}
        .search-container{position:relative}
        .search-container input{width:250px;padding:10px 15px 10px 40px;border-radius:8px;border:1px solid #ddd;font-family:'Poppins',sans-serif;font-size:15px;transition:all .3s ease}
        .search-container input:focus{outline:0;border-color:var(--primary-color);box-shadow:0 0 0 2px rgba(40,167,69,.2)}
        .search-container i{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#aaa}
        .btn-add{background-color:var(--primary-color);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;transition:background-color .3s ease;display:inline-flex;align-items:center;gap:8px}
        .btn-add:hover{background-color:var(--primary-hover)}
        .gallery-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:25px}
        .gallery-card{background-color:#fff;border-radius:12px;box-shadow:var(--card-shadow);overflow:hidden;display:flex;flex-direction:column;transition:all .3s ease}
        .gallery-card:hover{transform:translateY(-5px);box-shadow:0 8px 25px rgba(0,0,0,.12)}
        .gallery-card .card-image{width:100%;height:200px;object-fit:cover;border-bottom:1px solid #f0f0f0}
        .gallery-card .card-body{padding:15px;flex-grow:1;display:flex;flex-direction:column}
        .gallery-card .card-category{background-color:rgba(40,167,69,.1);color:var(--primary-hover);padding:4px 10px;border-radius:20px;font-size:12px;font-weight:500;margin-bottom:10px;align-self:flex-start}
        .gallery-card .card-description{font-size:15px;color:#555;line-height:1.5;flex-grow:1}
        .gallery-card .card-actions{padding:15px;border-top:1px solid #f0f0f0;display:flex;gap:10px;justify-content:flex-end}
        .btn-action{padding:6px 14px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:6px}
        .btn-edit{background-color:var(--info-color)}.btn-edit:hover{background-color:var(--info-hover)}.btn-delete{background-color:var(--danger-color)}.btn-delete:hover{background-color:var(--danger-hover)}
        .alert-info{background-color:#e9f5ff;color:#0c5460;padding:20px;border:1px solid #bee5eb;border-radius:8px;text-align:center;font-size:16px}
        .sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}
        .sidebar-dropdown.open .dropdown-menu{display:block}
        .dropdown-menu li a{padding-left:65px}
        .dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}
        .sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}
        .notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
        @media (max-width:992px){.sidebar{width:70px}.sidebar-header h3,.sidebar-nav li a span,.dropdown-icon{display:none}.main-content{margin-left:70px;width:calc(100% - 70px)}.sidebar-dropdown .dropdown-menu{position:absolute;left:70px;top:0;background-color:var(--sidebar-bg);width:200px;box-shadow:5px 0 15px rgba(0,0,0,.2);border-left:1px solid var(--sidebar-active)}.dropdown-menu li a{padding-left:20px}.dropdown-menu li a span{display:inline-block!important}}
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li><a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <!--<li><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>-->
            <li>
                        <a href="kelola_kontak_pesan.php">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Masuk</span>
                            <?php if ($jumlah_pesan_baru > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </a>
                    </li>
            <li><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
            <li><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
            <li><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li class="active"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <!--<li>-->
                    <!--    <a href="kelola_kontak_pesan.php">-->
                    <!--        <i class="fas fa-envelope"></i>-->
                    <!--        <span>Pesan Masuk</span>-->
                    <!--        <?php if ($jumlah_pesan_baru > 0): ?>-->
                    <!--            <span class="notification-dot"></span>-->
                    <!--        <?php endif; ?>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <!--<li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>-->
                    <li class="<?php echo ($halaman_aktif == 'akun') ? 'active' : ''; ?>"><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Galeri</h1>
            <div class="header-actions">
                <div class="search-container">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchInput" onkeyup="searchGallery()" placeholder="Cari kategori atau deskripsi...">
                </div>
                <a href="tambah_galeri.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Galeri</a>
            </div>
        </header>

        <section class="content">
            <?php if (empty($galleries)): ?>
                <div class="alert alert-info">
                    🖼️ Belum ada data galeri. Silakan tambahkan foto baru.
                </div>
            <?php else: ?>
                <div class="gallery-grid" id="galleryGrid">
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
        function searchGallery() {
            var input, filter, grid, cards, card, category, description, i, txtValueCategory, txtValueDescription;
            input = document.getElementById("searchInput");
            filter = input.value.toUpperCase();
            grid = document.getElementById("galleryGrid");
            cards = grid.getElementsByClassName("gallery-card");

            for (i = 0; i < cards.length; i++) {
                card = cards[i];
                category = card.querySelector(".card-category");
                description = card.querySelector(".card-description");
                
                txtValueCategory = category ? (category.textContent || category.innerText) : "";
                txtValueDescription = description ? (description.textContent || description.innerText) : "";
                
                if (txtValueCategory.toUpperCase().indexOf(filter) > -1 || txtValueDescription.toUpperCase().indexOf(filter) > -1) {
                    card.style.display = "";
                } else {
                    card.style.display = "none";
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Foto ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_galeri.php?id=' + id;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const dropdownToggles = document.querySelectorAll('.sidebar-dropdown > a');
            dropdownToggles.forEach(function(toggle) {
                toggle.addEventListener('click', function(event) {
                    event.preventDefault();
                    const parent = this.parentElement;
                    parent.classList.toggle('open');
                });
            });
        });

        <?php
        if (isset($_SESSION['success_message'])) {
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2500, showConfirmButton: false });";
            unset($_SESSION['success_message']);
        }
        if (isset($_SESSION['error_message'])) {
            echo "Swal.fire({ title: 'Gagal!', text: '" . addslashes($_SESSION['error_message']) . "', icon: 'error', confirmButtonColor: '#d33' });";
            unset($_SESSION['error_message']);
        }
        ?>
    </script>
</body>
</html>
