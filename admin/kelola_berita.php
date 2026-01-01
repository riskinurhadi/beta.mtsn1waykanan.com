<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'berita';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

// --- AMBIL DATA BERITA DARI DATABASE ---
$berita_list = [];
// OPTIMASI: Hanya ambil kolom yang diperlukan untuk tabel (tidak ambil isi yang panjang)
$sql = "SELECT b.id, b.judul, b.kategori, b.penulis, b.gambar_utama, b.tanggal_publikasi, b.slug, b.status_berita, k.nama_lengkap as nama_kontributor 
        FROM berita b
        LEFT JOIN kontributor k ON b.id_kontributor = k.id
        ORDER BY b.tanggal_publikasi DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $berita_list[] = $row;
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
    <title>Kelola Berita - Admin MTsN 1 Way Kanan</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--warning-color:#f39c12;--secondary-color:#6c757d;--info-color:#3498db;--success-color:#198754}
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
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .berita-thumb{width:100px;height:60px;border-radius:8px;object-fit:cover;border:2px solid #eee}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-view{background-color:var(--info-color)}
        .btn-edit{background-color:var(--warning-color)}
        .btn-delete{background-color:var(--danger-color)}
        .btn-approve{background-color:var(--success-color)}
        .btn-reject{background-color:var(--danger-color)}
        .badge{padding:5px 12px;border-radius:20px;font-size:12px;font-weight:600;color:#fff}
        .badge-diterbitkan{background-color:var(--primary-color)}
        .badge-pending{background-color:var(--warning-color)}
        .badge-ditolak{background-color:var(--secondary-color)}
        .modal-body .content{max-height:60vh;overflow-y:auto;line-height:1.8;}
        .modal-body .content img{max-width:100%;height:auto;border-radius:8px;margin:15px 0;}
        /* DataTables Custom Styling */
        .dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter{margin-bottom:15px}
        .dataTables_wrapper .dataTables_length select{padding:6px 10px;border-radius:6px;border:1px solid #ddd;font-family:'Poppins',sans-serif;font-size:14px}
        .dataTables_wrapper .dataTables_filter input{padding:6px 12px;border-radius:6px;border:1px solid #ddd;font-family:'Poppins',sans-serif;margin-left:10px;font-size:14px}
        .dataTables_wrapper .dataTables_filter input:focus{outline:0;border-color:var(--primary-color);box-shadow:0 0 0 2px rgba(40,167,69,.2)}
        .dataTables_wrapper .dataTables_info{padding-top:12px;color:#666;font-family:'Poppins',sans-serif;font-size:13px;line-height:1.5}
        .dataTables_wrapper .dataTables_paginate{padding-top:12px;float:right}
        .dataTables_wrapper .dataTables_paginate .paginate_button{padding:5px 10px;margin:0 1px;border-radius:5px;border:1px solid #ddd;color:#333 !important;font-family:'Poppins',sans-serif;font-size:13px;cursor:pointer;min-width:32px;text-align:center;display:inline-block;transition:all 0.2s ease}
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover:not(.disabled){background-color:var(--primary-color) !important;color:#fff !important;border-color:var(--primary-color) !important;transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,0.1)}
        .dataTables_wrapper .dataTables_paginate .paginate_button.current{background-color:var(--primary-color) !important;color:#fff !important;border-color:var(--primary-color) !important;font-weight:600;box-shadow:0 2px 4px rgba(40,167,69,0.3)}
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled{opacity:0.4;cursor:not-allowed;background-color:#f5f5f5 !important}
        .dataTables_wrapper .dataTables_paginate .paginate_button.disabled:hover{transform:none;box-shadow:none}
        .dataTables_wrapper .dataTables_length label, .dataTables_wrapper .dataTables_filter label{font-family:'Poppins',sans-serif;font-weight:500;font-size:14px;margin-bottom:0}
        .dataTables_wrapper .dataTables_processing{font-family:'Poppins',sans-serif}
        .dataTables_wrapper .dataTables_paginate .ellipsis{padding:5px 8px;margin:0 1px;color:#999;cursor:default}
        .dataTables_wrapper .row{margin-left:0;margin-right:0}
        .dataTables_wrapper .row > [class*="col-"]{padding-left:15px;padding-right:15px}
        .dataTables_wrapper .dataTables_paginate ul.pagination{margin:0;justify-content:flex-end}
        .dataTables_wrapper .dataTables_paginate ul.pagination .page-item .page-link{padding:5px 10px;font-size:13px;border-radius:5px;margin:0 1px;border:1px solid #ddd;color:#333;min-width:32px;text-align:center}
        .dataTables_wrapper .dataTables_paginate ul.pagination .page-item.active .page-link{background-color:var(--primary-color);border-color:var(--primary-color);color:#fff;font-weight:600;box-shadow:0 2px 4px rgba(40,167,69,0.3)}
        .dataTables_wrapper .dataTables_paginate ul.pagination .page-item:not(.disabled) .page-link:hover{background-color:var(--primary-color);border-color:var(--primary-color);color:#fff;transform:translateY(-1px);box-shadow:0 2px 4px rgba(0,0,0,0.1)}
        .dataTables_wrapper .dataTables_paginate ul.pagination .page-item.disabled .page-link{opacity:0.4;cursor:not-allowed;background-color:#f5f5f5}
        @media (max-width: 768px) {
            .dataTables_wrapper .dataTables_paginate{float:none;text-align:center;margin-top:10px}
            .dataTables_wrapper .dataTables_info{text-align:center;margin-bottom:10px}
            .dataTables_wrapper .dataTables_paginate ul.pagination{justify-content:center}
        }
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
            <li class="active"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, ['pesan', 'operator', 'pengaturan', 'performa', 'target_hafalan']) ? 'active open' : ''; ?>">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <!--<li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">-->
                    <!--    <a href="kelola_kontak_pesan.php">-->
                    <!--        <i class="fas fa-envelope"></i>-->
                    <!--        <span>Pesan Masuk</span>-->
                    <!--        <?php if ($jumlah_pesan_baru > 0): ?>-->
                    <!--            <span class="notification-dot"></span>-->
                    <!--        <?php endif; ?>-->
                    <!--    </a>-->
                    <!--</li>-->
                    <li class="<?php echo ($halaman_aktif == 'target_hafalan') ? 'active' : ''; ?>"><a href="kelola_target_hafalan.php"><i class="fas fa-bullseye"></i><span>Target Hafalan</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'akun') ? 'active' : ''; ?>"><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'performa') ? 'active' : ''; ?>"><a href="performa.php"><i class="fas fa-cog"></i><span>Site Monitoring</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="developer.php"><i class="fas fa-code"></i><span>Developer Page</span></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Berita</h1>
            <div class="header-actions">
                <a href="tambah_berita.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Berita</a>
            </div>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table" id="beritaTable">
                    <thead>
                        <tr>
                            <th>Gambar</th>
                            <th>Judul</th>
                            <th>Penulis</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($berita_list as $berita): ?>
                        <tr>
                            <td><img src="uploads/berita/<?php echo htmlspecialchars($berita['gambar_utama'] ?? 'default-berita.png'); ?>" alt="Gambar Utama" class="berita-thumb" loading="lazy"></td>
                            <td><?php echo htmlspecialchars($berita['judul']); ?></td>
                            <td><?php echo htmlspecialchars($berita['nama_kontributor'] ?? $berita['penulis']); ?></td>
                            <td>
                                <span class="badge badge-<?php echo strtolower($berita['status_berita']); ?>">
                                    <?php echo $berita['status_berita']; ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if ($berita['status_berita'] == 'Pending'): ?>
                                        <button type="button" class="btn-action btn-view" title="Review Berita" 
                                            data-judul="<?php echo htmlspecialchars($berita['judul']); ?>" 
                                            data-id="<?php echo $berita['id']; ?>" 
                                            onclick="showReviewModal(this)">
                                            <i class="fas fa-search"></i> Review
                                        </button>
                                        <a href="aksi_berita.php?id=<?php echo $berita['id']; ?>&aksi=terbitkan" class="btn-action btn-approve" title="Terbitkan"><i class="fas fa-check"></i></a>
                                        <a href="aksi_berita.php?id=<?php echo $berita['id']; ?>&aksi=tolak" class="btn-action btn-reject" title="Tolak"><i class="fas fa-times"></i></a>
                                    <?php else: ?>
                                        <a href="../detail_berita.php?slug=<?php echo $berita['slug']; ?>" class="btn-action btn-view" target="_blank" title="Lihat Berita"><i class="fas fa-eye"></i></a>
                                        <a href="edit_berita.php?id=<?php echo $berita['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                        <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $berita['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- Modal untuk Review Berita -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="reviewModalLabel">Review Berita</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content" id="reviewModalContent">
                        <!-- Isi berita akan dimuat di sini oleh JavaScript -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function showReviewModal(element) {
            const judul = element.getAttribute('data-judul');
            const id = element.getAttribute('data-id');
            
            document.getElementById('reviewModalLabel').innerText = judul;
            document.getElementById('reviewModalContent').innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin"></i> Memuat konten...</div>';

            var myModal = new bootstrap.Modal(document.getElementById('reviewModal'));
            myModal.show();

            // Load isi berita via AJAX (lazy loading)
            $.ajax({
                url: 'get_berita_isi.php',
                method: 'GET',
                data: { id: id },
                success: function(response) {
                    document.getElementById('reviewModalContent').innerHTML = response.isi || '<p>Konten tidak ditemukan.</p>';
                },
                error: function() {
                    document.getElementById('reviewModalContent').innerHTML = '<p class="text-danger">Gagal memuat konten.</p>';
                }
            });
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Berita ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, Hapus Saja!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_berita.php?id=' + id;
                }
            });
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi DataTables
            $('#beritaTable').DataTable({
                language: {
                    processing: "Memproses...",
                    lengthMenu: "Tampilkan _MENU_ data per halaman",
                    zeroRecords: "Tidak ada data yang ditemukan",
                    info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ data",
                    infoEmpty: "Menampilkan 0 sampai 0 dari 0 data",
                    infoFiltered: "(disaring dari _MAX_ total data)",
                    search: "Cari:",
                    paginate: {
                        first: "Pertama",
                        last: "Terakhir",
                        next: "Selanjutnya",
                        previous: "Sebelumnya"
                    }
                },
                pageLength: 5,
                lengthMenu: [[5, 10, 20, -1], [5, 10, 20, "Semua"]],
                order: [], // Tidak ada default sorting, menggunakan urutan dari database
                columnDefs: [
                    { orderable: false, targets: [0, 4] }, // Kolom Gambar dan Aksi tidak bisa diurutkan
                    { searchable: false, targets: [0, 4] }, // Kolom Gambar dan Aksi tidak bisa dicari
                    { searchable: true, targets: [1, 2, 3] }, // Kolom Judul, Penulis, dan Status bisa dicari
                    { orderable: true, targets: [1, 2, 3] } // Kolom Judul, Penulis, dan Status bisa diurutkan
                ],
                responsive: true,
                processing: true,
                deferRender: true, // OPTIMASI: Render row hanya saat diperlukan (lazy rendering)
                dom: '<"row mb-3"<"col-sm-12 col-md-6"l><"col-sm-12 col-md-6"f>>rt<"row mt-3"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
                drawCallback: function() {
                    // Memastikan pagination tetap rapi setelah draw
                    $('.dataTables_paginate').css('float', 'right');
                }
            });

            // Dropdown sidebar
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
