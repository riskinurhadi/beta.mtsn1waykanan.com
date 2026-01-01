<?php
include 'koneksi.php';
// PERUBAHAN: Query disesuaikan dengan struktur tabel baru
$sql = "SELECT id, nama, guru_mapel, jabatan, no_hp, foto_guru FROM data_guru ORDER BY nama ASC";
$result = $koneksi->query($sql);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Guru - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <style>
        .teacher-page-section{padding:80px 0;background-color:#f8f9fa}.teacher-card{background-color:#fff;border-radius:15px;box-shadow:0 5px 20px rgba(0,0,0,.07);text-align:center;padding:25px 15px;transition:all .3s ease;height:100%;cursor:pointer}.teacher-card:hover{transform:translateY(-8px);box-shadow:0 10px 25px rgba(0,0,0,.1)}.teacher-card-img{width:140px;height:140px;border-radius:50%;overflow:hidden;margin:0 auto 20px;border:5px solid #e9ecef}.teacher-card-img img{width:100%;height:100%;object-fit:cover}.teacher-card-body{flex-grow:1}.teacher-name{font-size:1.1rem;font-weight:600;color:#212529;margin-bottom:5px}.teacher-position{font-size:.9rem;color:#198754;font-weight:500;margin-bottom:10px}#guruDetailModal .modal-lg{max-width:800px}#guruDetailModal .modal-content{border:none;border-radius:15px}#guruDetailModal .modal-body{padding:0}.detail-guru-img-container{background-color:#e9ecef;display:flex;align-items:center;justify-content:center;border-top-left-radius:15px;border-bottom-left-radius:15px}.detail-guru-img-container img{width:100%;height:100%;object-fit:cover;border-top-left-radius:15px;border-bottom-left-radius:15px}.detail-guru-name{font-weight:700;color:#333;margin-bottom:.25rem}.detail-guru-position{font-size:1.1rem;color:#198754;font-weight:500}.detail-guru-table{font-size:.95rem}.detail-guru-table th{color:#6c757d;font-weight:500;padding-left:0;white-space:nowrap}.detail-guru-table td{color:#212529;font-weight:500}@media (max-width:767px){.detail-guru-img-container{border-radius:15px 15px 0 0;max-height:300px}.detail-guru-img-container img{border-radius:15px 15px 0 0}.detail-guru-name,.detail-guru-position{text-align:center}}
    </style>

    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://lulusku.kemusukkidul.com/img/kemenag.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
                <img src="img/mtsn1logo.png" alt="Logo MTsN 1 Way Kanan" style="height: 50px;">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-lg-center">
                    <li class="nav-item"><a class="nav-link" href="index.php">Beranda</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" aria-current="page">Profil</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profil.php">Profil Madrasah</a></li>
                            <li><a class="dropdown-item" href="struktur.php">Struktur Madrasah</a></li>
                            <li><a class="dropdown-item active" href="guru.php">Profil Guru</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="index.php#berita">Berita</a></li>
                    <li class="nav-item"><a class="nav-link" href="index.php#galeri">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="layanan.php">Layanan</a></li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">Lainnya</a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="tata-tertib.php">Tata Tertib Madrasah</a></li>
                        </ul>
                    </li>
                    <li class="nav-item"><a class="nav-link" href="index.php#kontak">Kontak</a></li>
                    <li class="nav-item ms-lg-3 mt-3 mt-lg-0"><a class="btn btn-ppdb" href="https://mtsn1waykanan.com/ppdb">PPDB Online</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <header class="page-header mt-5">
        <div class="container">
            <h1 class="page-title">Profil Guru & Staf</h1>
            <p class="page-subtitle">Tenaga Pendidik dan Kependidikan MTs Negeri 1 Way Kanan</p>
        </div>
    </header>

    <main class="teacher-page-section">
        <div class="container">
            <div class="row g-4">
                <?php
                if ($result && $result->num_rows > 0):
                    while ($guru = $result->fetch_assoc()):
                        $foto = (!empty($guru['foto_guru'])) ? 'admin/uploads/guru/' . htmlspecialchars($guru['foto_guru']) : 'https://placehold.co/400x400/E0F2F1/198754?text=Foto';
                ?>
                        <div class="col-lg-3 col-md-4 col-sm-6">
                            <div class="teacher-card" data-bs-toggle="modal" data-bs-target="#guruDetailModal" data-id="<?php echo $guru['id']; ?>">
                                <div class="teacher-card-img">
                                    <img src="<?php echo $foto; ?>" alt="Foto <?php echo htmlspecialchars($guru['nama']); ?>">
                                </div>
                                <div class="teacher-card-body">
                                    <!-- PERUBAHAN: Menampilkan 'nama' dan 'guru_mapel' -->
                                    <h5 class="teacher-name"><?php echo htmlspecialchars($guru['nama']); ?></h5>
                                    <p class="teacher-position"><?php echo htmlspecialchars($guru['guru_mapel']); ?></p>
                                </div>
                            </div>
                        </div>
                <?php
                    endwhile;
                else:
                    echo '<div class="col-12"><p class="text-center">Data guru belum tersedia.</p></div>';
                endif;
                ?>
            </div>
        </div>
    </main>

    <div class="modal fade" id="guruDetailModal" tabindex="-1" aria-labelledby="guruDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="guruDetailModalLabel">Profil Detail</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="guruDetailContent">
                    <div class="text-center p-5">
                        <div class="spinner-border text-success" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2">Memuat data...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-dark text-white text-center p-3">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> MTs Negeri 1 Way Kanan. All Rights Reserved.</p>
    </footer>

    <?php $koneksi->close(); ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const navbar = document.querySelector('.navbar');
            navbar.classList.add('scrolled'); // Selalu scrolled di halaman internal

            const guruDetailModal = document.getElementById('guruDetailModal');
            guruDetailModal.addEventListener('show.bs.modal', event => {
                const button = event.relatedTarget;
                const guruId = button.getAttribute('data-id');
                const modalBody = document.getElementById('guruDetailContent');

                modalBody.innerHTML = `<div class="text-center p-5"><div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Memuat data...</p></div>`;

                fetch(`get_guru_detail.php?id=${guruId}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.error) {
                            modalBody.innerHTML = `<p class="text-center text-danger p-4">${data.error}</p>`;
                            return;
                        }

                        const fotoUrl = data.foto_guru ? `admin/uploads/guru/${data.foto_guru}` : 'https://placehold.co/500x500/E0F2F1/198754?text=Foto';
                        
                        // PERUBAHAN: Menyesuaikan dengan struktur data baru
                        const contentHtml = `
                            <div class="row g-0">
                                <div class="col-md-4 detail-guru-img-container">
                                    <img src="${fotoUrl}" alt="Foto ${data.nama}">
                                </div>
                                <div class="col-md-8">
                                    <div class="card-body p-4">
                                        <h3 class="detail-guru-name">${data.nama}</h3>
                                        <p class="detail-guru-position">${data.guru_mapel || '-'}</p>
                                        <hr>
                                        <table class="table table-borderless detail-guru-table">
                                            <tbody>
                                                <tr><th style="width: 35%;">NIP</th><td>: ${data.nip || '-'}</td></tr>
                                                <tr><th>Pangkat/Golongan</th><td>: ${data.pangkat_golongan || '-'}</td></tr>
                                                <tr><th>Jabatan</th><td>: ${data.jabatan || '-'}</td></tr>
                                                <tr><th>No. Handphone</th><td>: ${data.no_hp || '-'}</td></tr>
                                                <tr><th>Alamat</th><td>: ${data.alamat || '-'}</td></tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        `;
                        
                        modalBody.innerHTML = contentHtml;
                    })
                    .catch(error => {
                        modalBody.innerHTML = `<p class="text-center text-danger p-4">Gagal memuat data. Silakan coba lagi.</p>`;
                        console.error('Error:', error);
                    });
            });
        });
    </script>
</body>
</html>
