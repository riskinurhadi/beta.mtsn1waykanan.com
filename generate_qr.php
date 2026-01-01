<?php
// Pastikan path ke library phpqrcode benar
require('phpqrcode/qrlib.php');

// Validasi apakah parameter slug ada di URL
if (!isset($_GET['slug']) || empty($_GET['slug'])) {
    // Jika tidak ada slug, buat gambar QR code kosong atau gambar error
    // Ini untuk mencegah error jika file diakses langsung
    header('Content-type: image/png');
    $im = imagecreatetruecolor(200, 200);
    $white = imagecolorallocate($im, 255, 255, 255);
    imagefill($im, 0, 0, $white);
    imagestring($im, 5, 50, 90, 'QR Error', imagecolorallocate($im, 0, 0, 0));
    imagepng($im);
    imagedestroy($im);
    exit();
}

// Ambil slug dan buat URL lengkap berita
$slug = $_GET['slug'];
// Pastikan Anda menggunakan https:// jika website Anda mendukungnya
$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
$url_berita = $protocol . $_SERVER['HTTP_HOST'] . '/detail_berita.php?slug=' . urlencode($slug);

// Hasilkan gambar QR code langsung ke browser
// Parameter: (data, output_file=false, error_correction_level, matrix_pixel_size, margin)
QRcode::png($url_berita, false, QR_ECLEVEL_L, 10, 2);
?>
