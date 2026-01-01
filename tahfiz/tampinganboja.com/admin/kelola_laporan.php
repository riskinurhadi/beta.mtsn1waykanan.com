<?php
session_start();
require 'koneksi.php';

// --- FUNGSI BANTUAN ---
function set_alert($type, $message) {
    $_SESSION['alert'] = [
        'type' => $type,
        'message' => $message
    ];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// --- LOGIKA PROSES DATA ---
$upload_dir = 'uploads/laporan/';

// Proses Update Status Laporan
if (isset($_POST['update_status'])) {
    $id_laporan = (int)$_POST['id_laporan'];
    $status_baru = $_POST['status'];
    $valid_statuses = ['Menunggu', 'Diproses', 'Selesai', 'Ditolak'];

    // Validasi status
    if (in_array($status_baru, $valid_statuses)) {
        $stmt = $koneksi->prepare("UPDATE tb_laporan SET status = ? WHERE id_laporan = ?");
        $stmt->bind_param("si", $status_baru, $id_laporan);
        if ($stmt->execute()) {
            set_alert('success', 'Status laporan berhasil diperbarui.');
        } else {
            set_alert('error', 'Gagal memperbarui status laporan.');
        }
        $stmt->close();
    } else {
        set_alert('error', 'Status yang dipilih tidak valid.');
    }
    redirect('kelola_laporan.php');
}

// Proses Hapus Laporan
else if (isset($_GET['hapus'])) {
    $id_laporan = (int)$_GET['hapus'];
    
    // 1. Ambil nama file lampiran sebelum menghapus record
    $stmt_select = $koneksi->prepare("SELECT lampiran FROM tb_laporan WHERE id_laporan = ?");
    $stmt_select->bind_param("i", $id_laporan);
    $stmt_select->execute();
    $result_select = $stmt_select->get_result();
    $row = $result_select->fetch_assoc();
    $nama_lampiran = $row['lampiran'] ?? null;
    $stmt_select->close();

    // 2. Hapus record dari database
    $stmt_delete = $koneksi->prepare("DELETE FROM tb_laporan WHERE id_laporan = ?");
    $stmt_delete->bind_param("i", $id_laporan);
    if ($stmt_delete->execute()) {
        // 3. Jika record berhasil dihapus & ada lampiran, hapus file fisik
        if (!empty($nama_lampiran) && file_exists($upload_dir . $nama_lampiran)) {
            unlink($upload_dir . $nama_lampiran);
        }
        set_alert('success', 'Laporan berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal menghapus laporan.');
    }
    $stmt_delete->close();
    redirect('kelola_laporan.php');
}

require 'sidebar.php';

// Mengambil semua data laporan, diurutkan dari yang terbaru
$query_laporan = "SELECT * FROM tb_laporan ORDER BY tanggal_laporan DESC";
$result_laporan = $koneksi->query($query_laporan);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Laporan Warga</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-page h5 { margin: 0; font-weight: 600; font-size: 1.5rem; }
        .content-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .status-badge { font-size: 0.8rem; padding: 0.4em 0.7em; }
        .modal-body strong { display: block; margin-bottom: 0.25rem; color: #555; }
        .lampiran-link { font-weight: 500; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } #menu-toggle { display: block !important; } }
    </style>
</head>
<body>

<main class="main-content" id="main-content">
    <header class="header-page">
        <div class="d-flex align-items-center">
            <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
            <h5>Kelola Laporan Warga</h5>
        </div>
    </header>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th scope="col">Judul Laporan</th>
                        <th scope="col">Pelapor</th>
                        <th scope="col">Tanggal</th>
                        <th scope="col">Status</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result_laporan && $result_laporan->num_rows > 0): ?>
                    <?php while($laporan = $result_laporan->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($laporan['judul_laporan']); ?></td>
                        <td><?php echo htmlspecialchars($laporan['nama_pelapor']); ?></td>
                        <td><?php echo date('d M Y, H:i', strtotime($laporan['tanggal_laporan'])); ?></td>
                        <td>
                            <?php
                                $status = $laporan['status'];
                                $badge_class = 'bg-secondary';
                                if ($status == 'Menunggu') $badge_class = 'bg-warning text-dark';
                                if ($status == 'Diproses') $badge_class = 'bg-info text-dark';
                                if ($status == 'Selesai') $badge_class = 'bg-success';
                                if ($status == 'Ditolak') $badge_class = 'bg-danger';
                            ?>
                            <span class="badge status-badge <?php echo $badge_class; ?>"><?php echo $status; ?></span>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-primary detail-btn" 
                                    data-bs-toggle="modal" 
                                    data-bs-target="#detailLaporanModal"
                                    data-id="<?php echo $laporan['id_laporan']; ?>"
                                    data-nama="<?php echo htmlspecialchars($laporan['nama_pelapor']); ?>"
                                    data-nohp="<?php echo htmlspecialchars($laporan['no_hp_pelapor']); ?>"
                                    data-judul="<?php echo htmlspecialchars($laporan['judul_laporan']); ?>"
                                    data-isi="<?php echo htmlspecialchars($laporan['isi_laporan']); ?>"
                                    data-lokasi="<?php echo htmlspecialchars($laporan['lokasi_kejadian']); ?>"
                                    data-tanggal="<?php echo date('d F Y, H:i', strtotime($laporan['tanggal_laporan'])); ?>"
                                    data-status="<?php echo $laporan['status']; ?>"
                                    data-lampiran="<?php echo htmlspecialchars($laporan['lampiran']); ?>">
                                <i class="bi bi-eye-fill"></i> Detail
                            </button>
                            <a href="kelola_laporan.php?hapus=<?php echo $laporan['id_laporan']; ?>" class="btn btn-sm btn-danger delete-btn">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center">Belum ada laporan yang masuk.</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Detail Laporan -->
<div class="modal fade" id="detailLaporanModal" tabindex="-1" aria-labelledby="detailLaporanModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="detailLaporanModalLabel">Detail Laporan</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Pelapor:</strong>
                        <p id="detail_nama_pelapor"></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <strong>No. HP:</strong>
                        <p id="detail_no_hp"></p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <strong>Tanggal Laporan:</strong>
                        <p id="detail_tanggal"></p>
                    </div>
                     <div class="col-md-6 mb-3">
                        <strong>Lokasi Kejadian:</strong>
                        <p id="detail_lokasi"></p>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <strong>Judul:</strong>
                    <h5 id="detail_judul"></h5>
                </div>
                <div class="mb-3">
                    <strong>Isi Laporan:</strong>
                    <p id="detail_isi" style="white-space: pre-wrap;"></p>
                </div>
                <div class="mb-3" id="lampiran_section">
                    <strong>Lampiran:</strong>
                    <div id="detail_lampiran"></div>
                </div>
                <hr>
                <form method="POST" action="kelola_laporan.php">
                    <input type="hidden" name="id_laporan" id="edit_id_laporan">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                           <label for="edit_status" class="form-label fw-bold">Ubah Status Laporan</label>
                           <select class="form-select" id="edit_status" name="status">
                               <option value="Menunggu">Menunggu</option>
                               <option value="Diproses">Diproses</option>
                               <option value="Selesai">Selesai</option>
                               <option value="Ditolak">Ditolak</option>
                           </select>
                        </div>
                        <div class="col-md-4">
                           <button type="submit" name="update_status" class="btn btn-primary w-100">Simpan Status</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    $(document).ready(function() {
        // Tampilkan notifikasi Toast dari session
        <?php
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            echo "
            const Toast = Swal.mixin({
                toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true,
                didOpen: (toast) => { toast.addEventListener('mouseenter', Swal.stopTimer); toast.addEventListener('mouseleave', Swal.resumeTimer); }
            });
            Toast.fire({ icon: '{$alert['type']}', title: '{$alert['message']}' });
            ";
            unset($_SESSION['alert']);
        }
        ?>

        // Toggle sidebar
        $('#menu-toggle').on('click', function() {
            $('#sidebar').toggleClass('active');
        });

        // Isi data ke modal detail saat dibuka
        $('#detailLaporanModal').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var modal = $(this);
            
            modal.find('.modal-body #edit_id_laporan').val(button.data('id'));
            modal.find('.modal-body #detail_nama_pelapor').text(button.data('nama'));
            modal.find('.modal-body #detail_no_hp').text(button.data('nohp'));
            modal.find('.modal-body #detail_tanggal').text(button.data('tanggal'));
            modal.find('.modal-body #detail_lokasi').text(button.data('lokasi') || '-');
            modal.find('.modal-body #detail_judul').text(button.data('judul'));
            modal.find('.modal-body #detail_isi').text(button.data('isi'));
            modal.find('.modal-body #edit_status').val(button.data('status'));

            var lampiran = button.data('lampiran');
            var lampiranContainer = modal.find('.modal-body #detail_lampiran');
            var lampiranSection = modal.find('.modal-body #lampiran_section');
            if (lampiran) {
                var fileExtension = lampiran.split('.').pop().toLowerCase();
                var lampiranPath = '<?php echo $upload_dir; ?>' + lampiran;
                if (['jpg', 'jpeg', 'png', 'gif'].includes(fileExtension)) {
                    lampiranContainer.html(`<a href="${lampiranPath}" target="_blank"><img src="${lampiranPath}" class="img-fluid rounded" style="max-height: 250px;"></a>`);
                } else if (['mp4', 'mov', 'avi'].includes(fileExtension)) {
                    lampiranContainer.html(`<video controls class="img-fluid rounded" style="max-height: 250px;"><source src="${lampiranPath}" type="video/mp4">Browser Anda tidak mendukung tag video.</video>`);
                } else {
                    lampiranContainer.html(`<a href="${lampiranPath}" target="_blank" class="btn btn-outline-primary lampiran-link"><i class="bi bi-file-earmark-arrow-down"></i> Lihat Lampiran</a>`);
                }
                lampiranSection.show();
            } else {
                lampiranContainer.html('<p class="text-muted">Tidak ada lampiran.</p>');
            }
        });

        // Konfirmasi hapus dengan SweetAlert
        $('.delete-btn').on('click', function(e) {
            e.preventDefault();
            const href = $(this).attr('href');
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Laporan yang dihapus tidak dapat dikembalikan!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.location.href = href;
                }
            });
        });
    });
</script>
</body>
</html>
