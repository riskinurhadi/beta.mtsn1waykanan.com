<?php
session_start();
require 'koneksi.php';

// --- FUNGSI BANTUAN & VALIDASI AWAL ---
function set_alert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    set_alert('error', 'ID Anggaran tidak valid.');
    redirect('kelola_anggaran.php');
}

$id_anggaran = (int)$_GET['id'];
$stmt = $koneksi->prepare("SELECT tahun FROM tb_anggaran WHERE id_anggaran = ?");
$stmt->bind_param("i", $id_anggaran);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    set_alert('error', 'Data Anggaran tidak ditemukan.');
    redirect('kelola_anggaran.php');
}
$anggaran = $result->fetch_assoc();
$tahun_anggaran = $anggaran['tahun'];
$stmt->close();

// --- LOGIKA CRUD RINCIAN ---

// Proses TAMBAH Rincian
if (isset($_POST['tambah_rincian'])) {
    $tipe = $_POST['tipe'];
    $bidang = $_POST['bidang'];
    $kegiatan = $_POST['kegiatan'];
    $sumber_dana = $_POST['sumber_dana'];
    $jumlah_anggaran = ($tipe == 'Belanja') ? $_POST['jumlah_anggaran'] : 0;
    $jumlah_realisasi = $_POST['jumlah_realisasi'];

    $stmt = $koneksi->prepare("INSERT INTO tb_anggaran_rincian (id_anggaran, tipe, bidang, kegiatan, jumlah_anggaran, jumlah_realisasi, sumber_dana) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssdds", $id_anggaran, $tipe, $bidang, $kegiatan, $jumlah_anggaran, $jumlah_realisasi, $sumber_dana);
    if ($stmt->execute()) {
        set_alert('success', 'Rincian berhasil ditambahkan.');
    } else {
        set_alert('error', 'Gagal menambahkan rincian.');
    }
    $stmt->close();
    redirect("kelola_anggaran_rincian.php?id=$id_anggaran");
}

// Proses EDIT Rincian
if (isset($_POST['edit_rincian'])) {
    $id_rincian = $_POST['id_rincian'];
    $tipe = $_POST['tipe'];
    $bidang = $_POST['bidang'];
    $kegiatan = $_POST['kegiatan'];
    $sumber_dana = $_POST['sumber_dana'];
    $jumlah_anggaran = ($tipe == 'Belanja') ? $_POST['jumlah_anggaran'] : 0;
    $jumlah_realisasi = $_POST['jumlah_realisasi'];

    $stmt = $koneksi->prepare("UPDATE tb_anggaran_rincian SET bidang=?, kegiatan=?, jumlah_anggaran=?, jumlah_realisasi=?, sumber_dana=? WHERE id_rincian=? AND id_anggaran=?");
    $stmt->bind_param("ssddsii", $bidang, $kegiatan, $jumlah_anggaran, $jumlah_realisasi, $sumber_dana, $id_rincian, $id_anggaran);
     if ($stmt->execute()) {
        set_alert('success', 'Rincian berhasil diperbarui.');
    } else {
        set_alert('error', 'Gagal memperbarui rincian.');
    }
    $stmt->close();
    redirect("kelola_anggaran_rincian.php?id=$id_anggaran");
}

// Proses HAPUS Rincian
if (isset($_GET['hapus_rincian'])) {
    $id_rincian = (int)$_GET['hapus_rincian'];
    $stmt = $koneksi->prepare("DELETE FROM tb_anggaran_rincian WHERE id_rincian = ? AND id_anggaran = ?");
    $stmt->bind_param("ii", $id_rincian, $id_anggaran);
    if ($stmt->execute()) {
        set_alert('success', 'Rincian berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal menghapus rincian.');
    }
    $stmt->close();
    redirect("kelola_anggaran_rincian.php?id=$id_anggaran");
}

require 'sidebar.php';

// --- AMBIL DATA UNTUK DITAMPILKAN ---
$pendapatan = $koneksi->query("SELECT * FROM tb_anggaran_rincian WHERE id_anggaran = $id_anggaran AND tipe = 'Pendapatan' ORDER BY bidang");
$belanja = $koneksi->query("SELECT * FROM tb_anggaran_rincian WHERE id_anggaran = $id_anggaran AND tipe = 'Belanja' ORDER BY bidang");

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Anggaran Tahun <?php echo $tahun_anggaran; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-page h5 { margin: 0; font-weight: 600; font-size: 1.5rem; }
        .content-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead th { font-weight: 600; }
        .btn-action { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } #menu-toggle { display: block !important; } }
    </style>
</head>
<body>

<main class="main-content" id="main-content">
    <header class="header-page">
        <div class="d-flex align-items: center">
            <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
            <div>
                <a href="kelola_anggaran.php" class="text-decoration-none text-muted d-block mb-1"><i class="bi bi-arrow-left"></i> Kembali</a>
                <h5>Rincian Anggaran Tahun <?php echo $tahun_anggaran; ?></h5>
            </div>
        </div>
    </header>

    <!-- RINCIAN PENDAPATAN -->
    <div class="content-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-bold">Rincian Pendapatan</h6>
            <button class="btn btn-sm btn-success" data-bs-toggle="modal" data-bs-target="#tambahRincianModal" data-tipe="Pendapatan">
                <i class="bi bi-plus-circle"></i> Tambah Pendapatan
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead><tr><th>Bidang</th><th>Kegiatan</th><th>Sumber Dana</th><th class="text-end">Realisasi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if($pendapatan->num_rows > 0): while($p = $pendapatan->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($p['bidang']); ?></td>
                        <td><?php echo htmlspecialchars($p['kegiatan']); ?></td>
                        <td><?php echo htmlspecialchars($p['sumber_dana']); ?></td>
                        <td class="text-end fw-bold"><?php echo "Rp " . number_format($p['jumlah_realisasi'], 0, ',', '.'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-action rounded-circle edit-btn" data-bs-toggle="modal" data-bs-target="#editRincianModal" data-rincian='<?php echo json_encode($p); ?>'><i class="bi bi-pencil-fill"></i></button>
                            <a href="kelola_anggaran_rincian.php?id=<?php echo $id_anggaran; ?>&hapus_rincian=<?php echo $p['id_rincian']; ?>" class="btn btn-sm btn-outline-danger btn-action rounded-circle delete-btn"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada rincian pendapatan.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- RINCIAN BELANJA -->
    <div class="content-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="mb-0 fw-bold">Rincian Belanja</h6>
            <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#tambahRincianModal" data-tipe="Belanja">
                <i class="bi bi-plus-circle"></i> Tambah Belanja
            </button>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped">
                <thead><tr><th>Bidang</th><th>Kegiatan</th><th>Sumber Dana</th><th class="text-end">Anggaran</th><th class="text-end">Realisasi</th><th>Aksi</th></tr></thead>
                <tbody>
                    <?php if($belanja->num_rows > 0): while($b = $belanja->fetch_assoc()): ?>
                     <tr>
                        <td><?php echo htmlspecialchars($b['bidang']); ?></td>
                        <td><?php echo htmlspecialchars($b['kegiatan']); ?></td>
                        <td><?php echo htmlspecialchars($b['sumber_dana']); ?></td>
                        <td class="text-end"><?php echo "Rp " . number_format($b['jumlah_anggaran'], 0, ',', '.'); ?></td>
                        <td class="text-end fw-bold"><?php echo "Rp " . number_format($b['jumlah_realisasi'], 0, ',', '.'); ?></td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary btn-action rounded-circle edit-btn" data-bs-toggle="modal" data-bs-target="#editRincianModal" data-rincian='<?php echo json_encode($b); ?>'><i class="bi bi-pencil-fill"></i></button>
                            <a href="kelola_anggaran_rincian.php?id=<?php echo $id_anggaran; ?>&hapus_rincian=<?php echo $b['id_rincian']; ?>" class="btn btn-sm btn-outline-danger btn-action rounded-circle delete-btn"><i class="bi bi-trash-fill"></i></a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="6" class="text-center">Belum ada rincian belanja.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal TAMBAH Rincian -->
<div class="modal fade" id="tambahRincianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="kelola_anggaran_rincian.php?id=<?php echo $id_anggaran; ?>">
                <div class="modal-header"><h5 class="modal-title" id="tambahRincianModalLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="tipe" id="tambah_tipe">
                    <div class="mb-3"><label for="tambah_bidang" class="form-label">Bidang</label><input type="text" class="form-control" name="bidang" id="tambah_bidang" required></div>
                    <div class="mb-3"><label for="tambah_kegiatan" class="form-label">Kegiatan / Uraian</label><input type="text" class="form-control" name="kegiatan" id="tambah_kegiatan" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="tambah_anggaran_wrapper"><label for="tambah_jumlah_anggaran" class="form-label">Jumlah Anggaran</label><input type="number" class="form-control" name="jumlah_anggaran" id="tambah_jumlah_anggaran"></div>
                        <div class="col-md-6 mb-3"><label for="tambah_jumlah_realisasi" class="form-label">Jumlah Realisasi</label><input type="number" class="form-control" name="jumlah_realisasi" id="tambah_jumlah_realisasi" required></div>
                    </div>
                    <div class="mb-3"><label for="tambah_sumber_dana" class="form-label">Sumber Dana</label><input type="text" class="form-control" name="sumber_dana" id="tambah_sumber_dana"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" name="tambah_rincian" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal EDIT Rincian -->
<div class="modal fade" id="editRincianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form method="POST" action="kelola_anggaran_rincian.php?id=<?php echo $id_anggaran; ?>">
                 <div class="modal-header"><h5 class="modal-title" id="editRincianModalLabel"></h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
                <div class="modal-body">
                    <input type="hidden" name="id_rincian" id="edit_id_rincian">
                    <input type="hidden" name="tipe" id="edit_tipe">
                    <div class="mb-3"><label for="edit_bidang" class="form-label">Bidang</label><input type="text" class="form-control" name="bidang" id="edit_bidang" required></div>
                    <div class="mb-3"><label for="edit_kegiatan" class="form-label">Kegiatan / Uraian</label><input type="text" class="form-control" name="kegiatan" id="edit_kegiatan" required></div>
                    <div class="row">
                        <div class="col-md-6 mb-3" id="edit_anggaran_wrapper"><label for="edit_jumlah_anggaran" class="form-label">Jumlah Anggaran</label><input type="number" class="form-control" name="jumlah_anggaran" id="edit_jumlah_anggaran"></div>
                        <div class="col-md-6 mb-3"><label for="edit_jumlah_realisasi" class="form-label">Jumlah Realisasi</label><input type="number" class="form-control" name="jumlah_realisasi" id="edit_jumlah_realisasi" required></div>
                    </div>
                    <div class="mb-3"><label for="edit_sumber_dana" class="form-label">Sumber Dana</label><input type="text" class="form-control" name="sumber_dana" id="edit_sumber_dana"></div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" name="edit_rincian" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    <?php if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true }); Toast.fire({ icon: '{$alert['type']}', title: '{$alert['message']}' });";
        unset($_SESSION['alert']);
    } ?>

    $('#menu-toggle').on('click', function() { $('#sidebar').toggleClass('active'); });

    $('.delete-btn').on('click', function(e) {
        e.preventDefault(); const href = $(this).attr('href');
        Swal.fire({
            title: 'Anda yakin?', text: "Rincian yang dihapus tidak dapat dikembalikan!",
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) { document.location.href = href; } });
    });

    // Modal logic for TAMBAH
    $('#tambahRincianModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var tipe = button.data('tipe');
        var modal = $(this);
        modal.find('.modal-title').text('Tambah Rincian ' + tipe);
        modal.find('#tambah_tipe').val(tipe);
        if (tipe === 'Pendapatan') {
            modal.find('#tambah_anggaran_wrapper').hide();
        } else {
            modal.find('#tambah_anggaran_wrapper').show();
        }
    });

     // Modal logic for EDIT
    $('#editRincianModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var rincian = button.data('rincian');
        var modal = $(this);
        
        modal.find('.modal-title').text('Edit Rincian ' + rincian.tipe);
        modal.find('#edit_id_rincian').val(rincian.id_rincian);
        modal.find('#edit_tipe').val(rincian.tipe);
        modal.find('#edit_bidang').val(rincian.bidang);
        modal.find('#edit_kegiatan').val(rincian.kegiatan);
        modal.find('#edit_jumlah_realisasi').val(rincian.jumlah_realisasi);
        modal.find('#edit_sumber_dana').val(rincian.sumber_dana);

        if (rincian.tipe === 'Pendapatan') {
            modal.find('#edit_anggaran_wrapper').hide();
        } else {
            modal.find('#edit_anggaran_wrapper').show();
            modal.find('#edit_jumlah_anggaran').val(rincian.jumlah_anggaran);
        }
    });
});
</script>

</body>
</html>
