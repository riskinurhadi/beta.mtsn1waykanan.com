<!-- admin/sidebar.php -->
<ul class="sidebar-nav">
    <li class="<?php echo ($halaman_aktif == 'dashboard') ? 'active' : ''; ?>">
        <a href="index.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'calon_siswa') ? 'active' : ''; ?>">
        <a href="kelola_calon_siswa.php"><i class="fas fa-user-graduate"></i><span>Calon Siswa</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'berita') ? 'active' : ''; ?>">
        <a href="kelola_berita.php"><i class="fas fa-newspaper"></i><span>Berita</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'prestasi') ? 'active' : ''; ?>">
        <a href="kelola_prestasi.php"><i class="fas fa-trophy"></i><span>Prestasi</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'tata_tertib') ? 'active' : ''; ?>">
        <a href="kelola_tata_tertib.php"><i class="fas fa-gavel"></i><span>Tata Tertib</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'struktural') ? 'active' : ''; ?>">
        <a href="kelola_struktural.php"><i class="fas fa-sitemap"></i><span>Struktur Organisasi</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'galeri') ? 'active' : ''; ?>">
        <a href="kelola_galeri.php"><i class="fas fa-images"></i><span>Galeri</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'pesan') ? 'active' : ''; ?>">
        <a href="kelola_kontak_pesan.php"><i class="fas fa-envelope"></i><span>Pesan Masuk</span></a>
    </li>
    <li class="<?php echo ($halaman_aktif == 'operator') ? 'active' : ''; ?>">
        <a href="kelola_operator.php"><i class="fas fa-user-shield"></i><span>Operator</span></a>
    </li>

    <!-- Untuk dropdown, kita cek apakah halaman aktif ada di dalam dropdown tersebut -->
    <li class="sidebar-dropdown <?php echo in_array($halaman_aktif, ['pengaturan', 'akun']) ? 'open active' : ''; ?>">
        <a href="#">
            <i class="fas fa-ellipsis-h"></i>
            <span>Lainnya</span>
            <i class="fas fa-chevron-down dropdown-icon"></i>
        </a>
        <ul class="dropdown-menu">
            <li><a href="pengaturan.php"><i class="fas fa-cog"></i><span>Pengaturan</span></a></li>
            <li><a href="akun.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
        </ul>
    </li>
</ul>