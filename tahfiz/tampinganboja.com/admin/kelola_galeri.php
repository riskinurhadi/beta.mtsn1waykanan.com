<?php
session_start();
require 'koneksi.php';

// --- FUNGSI BANTUAN ---
function set_alert($type, $message) {
    $_SESSION['alert'] = ['type' => $type, 'message' => $message];
}

function redirect($url) {
    header("Location: $url");
    exit();
}

// --- LOGIKA PROSES DATA (tb_anggaran) ---

// 1. PROSES TAMBAH TAHUN ANGGARAN
if (isset($_POST['tambah_anggaran'])) {
    $tahun = $_POST['tahun'];
    $keterangan = $_POST['keterangan'];

    if (empty($tahun)) {
        set_alert('error', 'Tahun anggaran wajib diisi.');
        redirect('kelola_anggaran.php');
    }

    $stmt_check = $koneksi->prepare("SELECT id_anggaran FROM tb_anggaran WHERE tahun = ?");
    $stmt_check->bind_param("s", $tahun);
    $stmt_check->execute();
    if ($stmt_check->get_result()->num_rows > 0) {
        set_alert('error', "Data ringkasan untuk tahun $tahun sudah ada.");
    } else {
        $stmt_insert = $koneksi->prepare("INSERT INTO tb_anggaran (tahun, keterangan) VALUES (?, ?)");
        $stmt_insert->bind_param("ss", $tahun, $keterangan);
        if ($stmt_insert->execute()) {
            set_alert('success', 'Tahun anggaran berhasil ditambahkan. Silakan kelola rinciannya.');
        } else {
            set_alert('error', 'Gagal menambahkan tahun anggaran.');
        }
        $stmt_insert->close();
    }
    $stmt_check->close();
    redirect('kelola_anggaran.php');
}

// 2. PROSES EDIT KETERANGAN
if (isset($_POST['edit_anggaran'])) {
    $id_anggaran = $_POST['id_anggaran'];
    $tahun = $_POST['tahun'];
    $keterangan = $_POST['keterangan'];
    // Nilai-nilai total bisa juga diupdate di sini jika diperlukan
    // Namun, lebih baik di-handle oleh halaman rincian agar selalu sinkron

    $stmt = $koneksi->prepare("UPDATE tb_anggaran SET tahun = ?, keterangan = ? WHERE id_anggaran = ?");
    $stmt->bind_param("ssi", $tahun, $keterangan, $id_anggaran);

    if ($stmt->execute()) {
        set_alert('success', 'Data ringkasan anggaran berhasil diperbarui.');
    } else {
        set_alert('error', 'Gagal memperbarui data.');
    }
    $stmt->close();
    redirect('kelola_anggaran.php');
}

// 3. PROSES HAPUS TAHUN ANGGARAN (CASCADE)
if (isset($_GET['hapus'])) {
    $id_anggaran = (int)$_GET['hapus'];
    // Karena ada FOREIGN KEY dengan ON DELETE CASCADE,
    // menghapus data di tb_anggaran akan otomatis menghapus semua rincian terkait.
    $stmt = $koneksi->prepare("DELETE FROM tb_anggaran WHERE id_anggaran = ?");
    $stmt->bind_param("i", $id_anggaran);
    if ($stmt->execute()) {
        set_alert('success', 'Data anggaran dan seluruh rinciannya berhasil dihapus.');
    } else {
        set_alert('error', 'Gagal menghapus data anggaran.');
    }
    $stmt->close();
    redirect('kelola_anggaran.php');
}

require 'sidebar.php';

// Mengambil data ringkasan dan total realisasi untuk setiap tahun
$query = "
    SELECT 
        a.id_anggaran, 
        a.tahun,
        COALESCE(pendapatan.total_realisasi_pendapatan, 0) as total_pendapatan,
        COALESCE(belanja.total_anggaran_belanja, 0) as total_anggaran,
        COALESCE(belanja.total_realisasi_belanja, 0) as total_realisasi
    FROM 
        tb_anggaran a
    LEFT JOIN (
        SELECT id_anggaran, SUM(jumlah_realisasi) as total_realisasi_pendapatan 
        FROM tb_anggaran_rincian WHERE tipe = 'Pendapatan' GROUP BY id_anggaran
    ) pendapatan ON a.id_anggaran = pendapatan.id_anggaran
    LEFT JOIN (
        SELECT id_anggaran, SUM(jumlah_anggaran) as total_anggaran_belanja, SUM(jumlah_realisasi) as total_realisasi_belanja 
        FROM tb_anggaran_rincian WHERE tipe = 'Belanja' GROUP BY id_anggaran
    ) belanja ON a.id_anggaran = belanja.id_anggaran
    ORDER BY a.tahun DESC
";
$result_anggaran = $koneksi->query($query);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Anggaran Desa</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: var(--bg-color); color: var(--text-dark); }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; transition: margin-left 0.3s ease; }
        .header-page { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header-page h5 { margin: 0; font-weight: 600; font-size: 1.5rem; }
        .content-card { background-color: white; border-radius: 12px; padding: 25px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .table thead th { font-weight: 600; background-color: #f8f9fa; }
        .btn-action { width: 38px; height: 38px; display: inline-flex; align-items: center; justify-content: center; }
        @media (max-width: 992px) { .sidebar { transform: translateX(-100%); } .sidebar.active { transform: translateX(0); } .main-content { margin-left: 0; } #menu-toggle { display: block !important; } }
    </style>
</head>
<body>

<main class="main-content" id="main-content">
    <header class="header-page">
        <div class="d-flex align-items: center">
            <button class="btn d-lg-none me-3" id="menu-toggle" type="button"><i class="bi bi-list"></i></button>
            <h5>Ringkasan Anggaran Tahunan</h5>
        </div>
        <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#tambahAnggaranModal">
            <i class="bi bi-plus-circle me-2"></i>Tambah Tahun Anggaran
        </button>
    </header>

    <div class="content-card">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th scope="col">Tahun</th>
                        <th scope="col" class="text-end">Total Pendapatan</th>
                        <th scope="col" class="text-end">Total Anggaran Belanja</th>
                        <th scope="col" class="text-end">Total Realisasi Belanja</th>
                        <th scope="col">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if($result_anggaran && $result_anggaran->num_rows > 0): ?>
                    <?php while($anggaran = $result_anggaran->fetch_assoc()): ?>
                    <tr>
                        <td class="fw-bold"><?php echo htmlspecialchars($anggaran['tahun']); ?></td>
                        <td class="text-end text-success fw-bold"><?php echo "Rp " . number_format($anggaran['total_pendapatan'], 0, ',', '.'); ?></td>
                        <td class="text-end"><?php echo "Rp " . number_format($anggaran['total_anggaran'], 0, ',', '.'); ?></td>
                        <td class="text-end text-danger fw-bold"><?php echo "Rp " . number_format($anggaran['total_realisasi'], 0, ',', '.'); ?></td>
                        <td>
                            <a href="kelola_anggaran_rincian.php?id=<?php echo $anggaran['id_anggaran']; ?>" class="btn btn-sm btn-info text-white">
                                <i class="bi bi-card-list me-1"></i> Kelola Rincian
                            </a>
                            <button class="btn btn-sm btn-outline-primary btn-action rounded-circle edit-btn"
                                data-bs-toggle="modal" data-bs-target="#editAnggaranModal"
                                data-id="<?php echo $anggaran['id_anggaran']; ?>"
                                data-tahun="<?php echo $anggaran['tahun']; ?>"
                                data-keterangan="<?php echo htmlspecialchars($koneksi->query("SELECT keterangan FROM tb_anggaran WHERE id_anggaran = ".$anggaran['id_anggaran'])->fetch_assoc()['keterangan']); ?>">
                                <i class="bi bi-pencil-fill"></i>
                            </button>
                            <a href="kelola_anggaran.php?hapus=<?php echo $anggaran['id_anggaran']; ?>" class="btn btn-sm btn-outline-danger btn-action rounded-circle delete-btn">
                                <i class="bi bi-trash-fill"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                    <?php else: ?>
                    <tr><td colspan="5" class="text-center">Belum ada data anggaran. Silakan tambahkan tahun anggaran baru.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Modal Tambah Anggaran -->
<div class="modal fade" id="tambahAnggaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Tambah Tahun Anggaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="kelola_anggaran.php">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tahun" class="form-label">Tahun Anggaran</label>
                        <input type="number" class="form-control" id="tahun" name="tahun" placeholder="Contoh: 2025" required min="2000" max="2100">
                    </div>
                    <div class="mb-3">
                        <label for="keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Catatan umum untuk anggaran tahun ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" name="tambah_anggaran" class="btn btn-primary">Simpan</button></div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Edit Anggaran -->
<div class="modal fade" id="editAnggaranModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header"><h5 class="modal-title">Edit Ringkasan Anggaran</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form method="POST" action="kelola_anggaran.php">
                <input type="hidden" id="edit_id_anggaran" name="id_anggaran">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="edit_tahun" class="form-label">Tahun Anggaran</label>
                        <input type="number" class="form-control" id="edit_tahun" name="tahun" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_keterangan" class="form-label">Keterangan (Opsional)</label>
                        <textarea class="form-control" id="edit_keterangan" name="keterangan" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button><button type="submit" name="edit_anggaran" class="btn btn-primary">Simpan Perubahan</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function() {
    // Tampilkan notifikasi
    <?php if (isset($_SESSION['alert'])) {
        $alert = $_SESSION['alert'];
        echo "const Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 3000, timerProgressBar: true }); Toast.fire({ icon: '{$alert['type']}', title: '{$alert['message']}' });";
        unset($_SESSION['alert']);
    } ?>

    // Toggle sidebar
    $('#menu-toggle').on('click', function() { $('#sidebar').toggleClass('active'); });

    // Konfirmasi Hapus
    $('.delete-btn').on('click', function(e) {
        e.preventDefault(); const href = $(this).attr('href');
        Swal.fire({
            title: 'Anda yakin?', text: "Semua rincian anggaran di tahun ini juga akan terhapus!",
            icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6', confirmButtonText: 'Ya, hapus!', cancelButtonText: 'Batal'
        }).then((result) => { if (result.isConfirmed) { document.location.href = href; } });
    });
    
    // Isi data ke Modal Edit
    $('.edit-btn').on('click', function() {
        $('#edit_id_anggaran').val($(this).data('id'));
        $('#edit_tahun').val($(this).data('tahun'));
        $('#edit_keterangan').val($(this).data('keterangan'));
    });
});
</script>
</body>
</html>

