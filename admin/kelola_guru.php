<?php
// Panggil config untuk memulai session & koneksi database
require_once 'config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['operator_id'])) {
    header("Location: login.php");
    exit();
}

// Menentukan halaman aktif untuk sidebar
$halaman_aktif = 'guru';

// --- Hitung jumlah pesan baru untuk notifikasi ---
$sql_count = "SELECT COUNT(id) as total_baru FROM pesan_kontak WHERE status = 'Baru'";
$result_count = $conn->query($sql_count);
$jumlah_pesan_baru = 0;
if ($result_count && $result_count->num_rows > 0) {
    $jumlah_pesan_baru = $result_count->fetch_assoc()['total_baru'];
}

// --- AMBIL DATA GURU DARI DATABASE (SESUAI STRUKTUR BARU) ---
$guru_list = [];
$sql = "SELECT id, nama, guru_mapel, nip, jabatan, foto_guru FROM data_guru ORDER BY nama ASC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $guru_list[] = $row;
    }
    $result->free();
}
// Koneksi akan ditutup di akhir file
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Guru & Staf - Admin</title>
    
    <!-- Google Fonts, Font Awesome, SweetAlert2, DataTables -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
    
    <style>
        :root{--primary-color:#28a745;--primary-hover:#218838;--sidebar-bg:#2c3e50;--sidebar-text:#ecf0f1;--sidebar-active:#34495e;--main-bg:#f4f7f6;--text-color:#333;--card-shadow:0 4px 15px rgba(0,0,0,.08);--danger-color:#e74c3c;--warning-color:#f39c12;--secondary-color:#6c757d}
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
        .header-actions{display:flex;gap:15px;align-items:center;}
        .search-container{position:relative;}
        .search-container input{width:250px;padding:10px 15px 10px 40px;border-radius:8px;border:1px solid #ddd;font-family:'Poppins',sans-serif;font-size:15px;transition:all .3s ease}
        .search-container input:focus{outline:none;border-color:var(--primary-color);box-shadow:0 0 0 2px rgba(40,167,69,.2)}
        .search-container i{position:absolute;left:15px;top:50%;transform:translateY(-50%);color:#aaa}
        .btn-add{background-color:var(--primary-color);color:#fff;padding:10px 20px;border-radius:8px;text-decoration:none;font-weight:500;transition:background-color .3s ease;display:inline-flex;align-items:center;gap:8px}
        .btn-add:hover{background-color:var(--primary-hover)}
        .table-container{background-color:#fff;padding:20px;border-radius:12px;box-shadow:var(--card-shadow);overflow-x:auto}
        .data-table{width:100%;border-collapse:collapse}
        .data-table th,.data-table td{padding:12px 15px;text-align:left;border-bottom:1px solid #f0f0f0;vertical-align:middle}
        .data-table th{font-weight:600;background-color:#f9fafb}
        .data-table tbody tr:hover{background-color:#f5f5f5}
        .guru-thumb{width:50px;height:50px;border-radius:50%;object-fit:cover;border:2px solid #eee}
        .action-buttons{display:flex;gap:8px}
        .btn-action{padding:6px 10px;border-radius:6px;text-decoration:none;font-size:14px;color:#fff;border:none;cursor:pointer;display:inline-flex;align-items:center;gap:5px}
        .btn-edit{background-color:var(--warning-color)}
        .btn-delete{background-color:var(--danger-color)}
        .sidebar-dropdown .dropdown-menu{display:none;list-style:none;padding:0;margin:0;background-color:#253545}
        .sidebar-dropdown.open .dropdown-menu{display:block}
        .dropdown-menu li a{padding-left:65px}
        .dropdown-icon{position:absolute;right:20px;transition:transform .3s ease}
        .sidebar-dropdown.open>a .dropdown-icon{transform:rotate(180deg)}
        .notification-dot{position:absolute;right:15px;top:50%;transform:translateY(-50%);width:10px;height:10px;background-color:var(--warning-color);border-radius:50%;border:2px solid var(--sidebar-bg)}
        
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
        
        @media (max-width:992px){.sidebar{width:70px}.sidebar-header h3,.sidebar-nav li a span,.dropdown-icon{display:none}.main-content{margin-left:70px;width:calc(100% - 70px)}.sidebar-dropdown .dropdown-menu{position:absolute;left:70px;top:0;background-color:var(--sidebar-bg);width:200px;box-shadow:5px 0 15px rgba(0,0,0,.2);border-left:1px solid var(--sidebar-active)}.dropdown-menu li a{padding-left:20px}.dropdown-menu li a span{display:inline-block!important}}
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h3>Admin MTsN 1</h3>
        </div>
        <ul class="sidebar-nav">
            <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>"><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'calon_siswa') ? 'active' : ''; ?>"><a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'guru') ? 'active' : ''; ?>"><a href="kelola_guru.php"><i class="fas fa-chalkboard-teacher"></i><span>Guru & Staf</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'testimoni') ? 'active' : ''; ?>"><a href="kelola_testimoni.php"><i class="fas fa-comment-dots"></i><span>Testimoni</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'berita') ? 'active' : ''; ?>"><a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'prestasi') ? 'active' : ''; ?>"><a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'tata_tertib') ? 'active' : ''; ?>"><a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'struktural') ? 'active' : ''; ?>"><a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a></li>
            <li class="<?php echo ($halaman_aktif == 'galeri') ? 'active' : ''; ?>"><a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a></li>
            <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, ['pesan', 'operator', 'pengaturan', 'akun', 'performa', 'api_keys']) ? 'active open' : ''; ?>">
                <a href="#">
                    <i class="fas fa-ellipsis-h"></i>
                    <span>Lainnya</span>
                    <i class="fas fa-chevron-down dropdown-icon"></i>
                </a>
                <ul class="dropdown-menu">
                    <li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">
                        <a href="kelola_kontak_pesan.php">
                            <i class="fas fa-envelope"></i>
                            <span>Pesan Masuk</span>
                            <?php if ($jumlah_pesan_baru > 0): ?>
                                <span class="notification-dot"></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>"><a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'akun') ? 'active' : ''; ?>"><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'pengaturan') ? 'active' : ''; ?>"><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan Web</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'performa') ? 'active' : ''; ?>"><a href="performa.php"><i class="fas fa-server"></i><span>Performa Web</span></a></li>
                    <li class="<?php echo ($halaman_aktif == 'api_keys') ? 'active' : ''; ?>"><a href="kelola_api_keys.php"><i class="fas fa-key"></i><span>API Keys</span></a></li>
                </ul>
            </li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="page-header">
            <h1>Kelola Guru & Staf</h1>
            <div class="header-actions">
                <a href="tambah_guru.php" class="btn-add"><i class="fas fa-plus"></i> Tambah Data</a>
            </div>
        </header>

        <section class="content">
            <div class="table-container">
                <table class="data-table" id="guruTable">
                    <thead>
                        <tr>
                            <th>Foto</th>
                            <th>Nama</th>
                            <th>Guru Mapel</th>
                            <th>NIP</th>
                            <th>Jabatan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($guru_list as $guru): ?>
                        <tr>
                            <td><img src="uploads/guru/<?php echo htmlspecialchars($guru['foto_guru'] ?? 'default-profile.png'); ?>" alt="Foto <?php echo htmlspecialchars($guru['nama']); ?>" class="guru-thumb" loading="lazy"></td>
                            <td><strong><?php echo htmlspecialchars($guru['nama']); ?></strong></td>
                            <td><?php echo htmlspecialchars($guru['guru_mapel']); ?></td>
                            <td><?php echo htmlspecialchars($guru['nip'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($guru['jabatan']); ?></td>
                            <td>
                                <div class="action-buttons">
                                    <a href="edit_guru.php?id=<?php echo $guru['id']; ?>" class="btn-action btn-edit" title="Edit"><i class="fas fa-pencil-alt"></i></a>
                                    <button type="button" class="btn-action btn-delete" onclick="confirmDelete(<?php echo $guru['id']; ?>)" title="Hapus"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
    
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(id) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Data ini akan dihapus secara permanen!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'hapus_guru.php?id=' + id;
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Inisialisasi DataTables
            $('#guruTable').DataTable({
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
                    { orderable: false, targets: [0, 5] }, // Kolom Foto dan Aksi tidak bisa diurutkan
                    { searchable: false, targets: [0, 5] }, // Kolom Foto dan Aksi tidak bisa dicari
                    { searchable: true, targets: [1, 2, 3, 4] }, // Kolom Nama, Guru Mapel, NIP, dan Jabatan bisa dicari
                    { orderable: true, targets: [1, 2, 3, 4] } // Kolom Nama, Guru Mapel, NIP, dan Jabatan bisa diurutkan
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
            echo "Swal.fire({ title: 'Berhasil!', text: '" . addslashes($_SESSION['success_message']) . "', icon: 'success', timer: 2000, showConfirmButton: false });";
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
