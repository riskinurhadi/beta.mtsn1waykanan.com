<?php
// Memanggil file koneksi untuk mengakses database
require_once 'koneksi.php';

// --- VALIDASI & PENGAMBILAN DATA ---
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    // Jika ID tidak valid, arahkan kembali ke halaman ringkasan
    header("Location: anggaran_desa.php");
    exit();
}
$id_anggaran = (int)$_GET['id'];

// Ambil tahun anggaran berdasarkan ID
$stmt_tahun = $koneksi->prepare("SELECT tahun FROM tb_anggaran WHERE id_anggaran = ?");
$stmt_tahun->bind_param("i", $id_anggaran);
$stmt_tahun->execute();
$result_tahun = $stmt_tahun->get_result();
if ($result_tahun->num_rows === 0) {
    header("Location: anggaran_desa.php");
    exit();
}
$selected_tahun = $result_tahun->fetch_assoc()['tahun'];
$stmt_tahun->close();

// Variabel untuk menampung data
$rincian_pendapatan = [];
$rincian_belanja = [];
$chart_data = [];
$totals = [
    'pendapatan' => 0,
    'belanja_dianggarkan' => 0,
    'belanja_realisasi' => 0,
    'sisa_anggaran' => 0
];

// Ambil data rincian dari database
$stmt_rincian = $koneksi->prepare("SELECT * FROM tb_anggaran_rincian WHERE id_anggaran = ? ORDER BY bidang, kegiatan");
$stmt_rincian->bind_param("i", $id_anggaran);
$stmt_rincian->execute();
$result_rincian = $stmt_rincian->get_result();

while ($row = $result_rincian->fetch_assoc()) {
    if ($row['tipe'] == 'Pendapatan') {
        $rincian_pendapatan[] = $row;
        $totals['pendapatan'] += $row['jumlah_realisasi'];
    } else {
        $rincian_belanja[] = $row;
        $totals['belanja_dianggarkan'] += $row['jumlah_anggaran'];
        $totals['belanja_realisasi'] += $row['jumlah_realisasi'];
        $bidang = $row['bidang'];
        if (!isset($chart_data[$bidang])) {
            $chart_data[$bidang] = 0;
        }
        $chart_data[$bidang] += $row['jumlah_anggaran'];
    }
}
$totals['sisa_anggaran'] = $totals['pendapatan'] - $totals['belanja_realisasi'];
$stmt_rincian->close();
$koneksi->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rincian Anggaran Tahun <?php echo $selected_tahun; ?> - Desa Tampingan</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary-color: #0d47a1;
            --light-gray: #f8f9fa;
            --dark-text: #333;
            --success-color: #28a745;
            --danger-color: #dc3545;
        }
        body { font-family: 'Poppins', sans-serif; background-color: var(--light-gray); }
        .navbar { background-color: var(--primary-color); box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        .navbar-brand { display: flex; align-items: center; }
        .navbar-brand img { height: 50px; margin-right: 15px; }
        .navbar-brand .logo-text { color: white; line-height: 1.2; }
        .navbar-brand .logo-text .title { font-size: 0.9rem; font-weight: 300; display: block; }
        .navbar-brand .logo-text .subtitle { font-size: 1.1rem; font-weight: 600; display: block; }
        .navbar-nav .nav-link { color: white; font-weight: 500; }
        .page-header { background: linear-gradient(rgba(0,0,0,0.6), rgba(0,0,0,0.6)), url('/img/fotobersama.png') no-repeat center center; background-size: cover; padding: 6rem 0; color: white; text-align: center; }
        .page-header h1 { font-weight: 700; }
        .content-section { padding: 3rem 0; }
        .summary-card { background-color: #fff; border-radius: 12px; padding: 1.5rem; margin-bottom: 1.5rem; box-shadow: 0 4px 15px rgba(0,0,0,0.05); border-left: 5px solid; display: flex; align-items: center; }
        .summary-card .icon { font-size: 2.5rem; margin-right: 1.5rem; opacity: 0.7; }
        .summary-card .info h6 { color: #777; margin-bottom: 0.25rem; }
        .summary-card .info h4 { margin: 0; font-weight: 700; }
        .summary-card.pendapatan { border-color: var(--success-color); } .summary-card.pendapatan .icon { color: var(--success-color); }
        .summary-card.belanja { border-color: var(--danger-color); } .summary-card.belanja .icon { color: var(--danger-color); }
        .summary-card.sisa { border-color: var(--primary-color); } .summary-card.sisa .icon { color: var(--primary-color); }
        .chart-container { background: #fff; padding: 2rem; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.05); }
        .accordion-item, .accordion-button { border-radius: 12px !important; }
        .accordion-button:not(.collapsed) { background-color: var(--primary-light); color: var(--primary-color); box-shadow: none; }
    </style>
</head>
<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <img src="https://boja.kendalkab.go.id/upload/umum/Logo.png" alt="Logo Kendal">
                <div class="logo-text">
                    <span class="title">Pemerintah Kabupaten Kendal</span>
                    <span class="subtitle">Desa Tampingan</span>
                </div>
            </a>
            <div class="ms-auto">
                <a class="btn btn-outline-light" href="anggaran_desa.php">
                    <i class="bi bi-arrow-left-circle-fill me-1"></i> Kembali ke Ringkasan
                </a>
            </div>
        </div>
    </nav>
</header>
<main style="padding-top: 80px;">
    <section class="page-header">
        <div class="container">
            <h1 class="display-5">Rincian Anggaran Tahun <?php echo $selected_tahun; ?></h1>
            <p class="lead">Detail alokasi dan realisasi APBDes Tampingan.</p>
        </div>
    </section>
    <section class="content-section">
        <div class="container">
            <?php if(!empty($rincian_pendapatan) || !empty($rincian_belanja)): ?>
            <div class="row">
                <div class="col-lg-3 col-md-6"><div class="summary-card pendapatan"><i class="bi bi-box-arrow-in-down icon"></i><div class="info"><h6>Total Pendapatan</h6><h4><?php echo "Rp " . number_format($totals['pendapatan'], 0, ',', '.'); ?></h4></div></div></div>
                <div class="col-lg-3 col-md-6"><div class="summary-card belanja"><i class="bi bi-box-arrow-up icon"></i><div class="info"><h6>Anggaran Belanja</h6><h4><?php echo "Rp " . number_format($totals['belanja_dianggarkan'], 0, ',', '.'); ?></h4></div></div></div>
                <div class="col-lg-3 col-md-6"><div class="summary-card belanja"><i class="bi bi-cash-coin icon"></i><div class="info"><h6>Realisasi Belanja</h6><h4><?php echo "Rp " . number_format($totals['belanja_realisasi'], 0, ',', '.'); ?></h4></div></div></div>
                <div class="col-lg-3 col-md-6"><div class="summary-card sisa"><i class="bi bi-wallet2 icon"></i><div class="info"><h6>Sisa Anggaran</h6><h4><?php echo "Rp " . number_format($totals['sisa_anggaran'], 0, ',', '.'); ?></h4></div></div></div>
            </div>
            <div class="row g-4 mt-3">
                <div class="col-lg-5"><div class="chart-container h-100"><h5 class="mb-3 text-center">Alokasi Anggaran Belanja</h5><canvas id="anggaranChart"></canvas></div></div>
                <div class="col-lg-7">
                    <div class="accordion" id="rincianAccordion">
                        <div class="accordion-item"><h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapsePendapatan">Rincian Pendapatan</button></h2><div id="collapsePendapatan" class="accordion-collapse collapse show" data-bs-parent="#rincianAccordion"><div class="accordion-body p-0"><table class="table table-striped mb-0"><thead><tr><th>Sumber Pendapatan</th><th class="text-end">Jumlah</th></tr></thead><tbody><?php foreach($rincian_pendapatan as $item): ?><tr><td><?php echo htmlspecialchars($item['kegiatan']); ?></td><td class="text-end"><?php echo "Rp " . number_format($item['jumlah_realisasi'], 0, ',', '.'); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div>
                        <div class="accordion-item mt-3"><h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseBelanja">Rincian Belanja</button></h2><div id="collapseBelanja" class="accordion-collapse collapse" data-bs-parent="#rincianAccordion"><div class="accordion-body p-0"><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Kegiatan</th><th class="text-end">Anggaran</th><th class="text-end">Realisasi</th></tr></thead><tbody><?php foreach($rincian_belanja as $item): ?><tr><td><b><?php echo htmlspecialchars($item['bidang']); ?></b><br><small><?php echo htmlspecialchars($item['kegiatan']); ?></small></td><td class="text-end"><?php echo "Rp " . number_format($item['jumlah_anggaran'], 0, ',', '.'); ?></td><td class="text-end"><?php echo "Rp " . number_format($item['jumlah_realisasi'], 0, ',', '.'); ?></td></tr><?php endforeach; ?></tbody></table></div></div></div></div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="alert alert-info text-center mt-4" role="alert"><h4 class="alert-heading">Rincian Belum Tersedia</h4><p>Data rincian anggaran untuk tahun <?php echo $selected_tahun; ?> belum dipublikasikan.</p></div>
            <?php endif; ?>
        </div>
    </section>
</main>
<footer class="bg-dark text-white text-center p-4 mt-4"><div class="container"><p class="mb-0">&copy; <?php echo date("Y"); ?> Pemerintah Desa Tampingan.</p></div></footer>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.2/dist/chart.umd.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('anggaranChart');
    if (ctx && <?php echo json_encode(!empty($chart_data)); ?>) {
        const chartData = <?php echo json_encode($chart_data); ?>;
        new Chart(ctx, {
            type: 'doughnut',
            data: { labels: Object.keys(chartData), datasets: [{ data: Object.values(chartData), backgroundColor: ['rgba(13, 71, 161, 0.8)', 'rgba(255, 152, 0, 0.8)', 'rgba(76, 175, 80, 0.8)', 'rgba(244, 67, 54, 0.8)'], borderColor: '#fff', borderWidth: 2 }] },
            options: { responsive: true, plugins: { legend: { position: 'bottom' }, tooltip: { callbacks: { label: (c) => new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR' }).format(c.parsed) } } } }
        });
    }
});
</script>
</body>
</html>

