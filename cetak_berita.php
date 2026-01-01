<?php
// 1. SERTAKAN FILE YANG DIPERLUKAN
require('fpdf/fpdf.php');
require('phpqrcode/qrlib.php');
require_once 'koneksi.php';

define('QR_TEMP_DIR', 'temp/');

// 2. AMBIL DATA BERITA DARI DATABASE
if (!isset($_GET['slug'])) {
    die('Error: Slug berita tidak ditemukan.');
}
$slug = $_GET['slug'];

$sql = "SELECT judul, slug, isi, penulis, kategori, gambar_utama, tanggal_publikasi FROM berita WHERE slug = ? LIMIT 1";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die('Error: Berita tidak ditemukan.');
}
$berita = $result->fetch_assoc();
$koneksi->close();


// 3. CLASS PDF KUSTOM DENGAN DESAIN EFISIEN
class PDF extends FPDF
{
    protected $col = 0;
    protected $y0;

    function Header()
    {
        $this->Image('https://lulusku.kemusukkidul.com/img/kemenag.png', 10, 8, 15);
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 6, 'MTs NEGERI 1 WAY KANAN', 0, 1, 'R');
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(120, 120, 120);
        $this->Cell(0, 5, 'e-Koran & Arsip Berita Digital Sekolah', 0, 1, 'R');
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, 25, 200, 25);
        $this->y0 = $this->GetY() + 5;
        $this->Ln(10);// Mengurangi spasi setelah header
    }

    function Footer()
    {
        // Akses variabel $berita dari luar class
        global $berita;

        // 1. BUAT QR CODE
        $url = 'https://www.mtsn1waykanan.com/detail_berita.php?slug=' . $berita['slug'];
        $qrCodeFile = QR_TEMP_DIR . $berita['slug'] . '.png';
        QRcode::png($url, $qrCodeFile, QR_ECLEVEL_L, 3, 2);

        // 2. TAMPILKAN DI PDF
        // Posisikan footer -20mm dari bawah untuk ruang yang cukup
        $this->SetY(-20);
        
        // Garis pemisah
        $this->SetDrawColor(200, 200, 200);
        $this->Line(10, $this->GetY(), 200, $this->GetY());
        $this->Ln(2);
        
        // Simpan posisi Y awal setelah garis
        $y_pos = $this->GetY();

        // --- BLOK KIRI: QR CODE & TEKS SCAN ---

        // Tampilkan gambar QR di kiri (X, Y, Lebar, Tinggi)
        $this->Image($qrCodeFile, 10, $y_pos, 15, 15);

        // Teks "Scan" di samping QR menggunakan MultiCell
        $this->SetFont('Arial', '', 9);
        $this->SetTextColor(150, 150, 150);
        
        // Atur posisi kursor di samping kanan QR, sedikit ke bawah untuk rata tengah
        $this->SetXY(27, $y_pos + 3); // X = 10(posisiQR) + 15(lebarQR) + 2(spasi)
        
        // MultiCell(lebar, tinggi_baris, teks, border, perataan)
        $this->MultiCell(35, 3.5, "Scan QR untuk melihat\nberita ini", 0, 'L');


        // --- BLOK KANAN: INFO DOKUMEN ---
        
        // Atur font untuk info dokumen
        $this->SetFont('Arial', 'I', 8);
        $this->SetTextColor(150, 150, 150);

        // Atur posisi Y untuk baris pertama info dokumen
        $this->SetY($y_pos + 2);

        // Baris pertama (rata kanan)
        $line1 = 'Dokumen Berita MTsN 1 Way Kanan';
        $this->Cell(0, 5, $line1, 0, 1, 'R'); 

        // Baris kedua (rata kanan)
        $line2 = 'Dicetak di: www.mtsn1waykanan.com/berita.php';
        $this->Cell(0, 5, $line2, 0, 0, 'R');
    }
    
    
    function SetCol($col)
    {
        $this->col = $col;
        if ($this->col == 0) { // Kolom Pertama (Kiri)
            $this->SetLeftMargin(10);
            $this->SetRightMargin(110); // Batasi sisi kanannya di 110mm
        } else { // Kolom Kedua (Kanan)
            $this->SetLeftMargin(110);
            $this->SetRightMargin(10); // Batasi sisi kanannya di 10mm (margin halaman)
        }
        $this->SetX($this->lMargin);
    }


    function AcceptPageBreak()
    {
        if ($this->col == 0) {
            $this->SetCol(1);
            $this->SetY($this->y0);
            return false;
        } else {
            $this->SetCol(0);
            return true;
        }
    }

    function PrintCover($judul, $penulis, $tanggal, $gambar)
    {
        // Simpan posisi Y awal sebelum cover
        $y_awal = $this->GetY();

        // --- GAMBAR DICETAK PERTAMA SEBAGAI LATAR ---
        $gambar_path = 'admin/uploads/berita/' . $gambar;
        if (!empty($gambar) && file_exists($gambar_path)) {
            // Cetak gambar utama
            $this->Image($gambar_path, 10, $y_awal, 190, 80); 
            $this->SetDrawColor(220, 220, 220);
            $this->Rect(10, $y_awal, 190, 80);

            // --- JUDUL SEBAGAI OVERLAY DI ATAS GAMBAR ---
            // Hitung posisi Y untuk kotak judul (misal: 25mm dari bawah gambar)
            $posisi_y_judul = $y_awal + 80 - 18; 
            $this->SetXY(15, $posisi_y_judul);

            // Siapkan warna untuk latar belakang dan teks judul
            $this->SetFillColor(255, 255, 255); // Latar belakang putih solid
            $this->SetTextColor(25, 135, 84);   // Warna teks hijau
            $this->SetFont('Arial', 'B', 18);   // Font judul sedikit lebih kecil agar pas

            $judul_teks = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', $judul);
            
            // Cetak judul dengan MultiCell dan aktifkan latar belakang (parameter terakhir 'true')
            $this->MultiCell(180, 8, $judul_teks, 0, 'L', true);
        }

        // --- INFO PENULIS & TANGGAL DI BAWAH GAMBAR ---
        // Pindahkan kursor ke posisi di bawah area gambar
        $this->SetY($y_awal + 85); // 80mm (tinggi gambar) + 5mm (spasi)

        // Atur font untuk info penulis
        $this->SetFont('Arial', '', 10);
        $this->SetTextColor(120, 120, 120);
        
        $meta_text = 'Oleh: ' . $penulis . '  |  Dipublikasikan: ' . date('d F Y', strtotime($tanggal));
        $this->Cell(0, 5, $meta_text, 0, 1, 'C');
        
        // Beri spasi terakhir sebelum masuk ke isi berita
        $this->Ln(5);
    }
    
    function PrintChapterBody($body)
    {
        // Atur Font dan Warna Teks
        $this->SetFont('Arial', '', 10.5);
        $this->SetTextColor(50, 50, 50);

        // --- LOGIKA PEMBERSIHAN HTML ---
        // Proses ini akan mengubah HTML kotor dari database menjadi teks bersih

        // 1. Ganti tag penutup paragraf </p> dengan pemisah unik.
        $isi_bersih = str_replace('</p>', "---PEMISAH_PARAGRAF---", $body);
        
        // 2. Ganti tag <br> (jika ada) dengan spasi agar kata tidak menempel.
        $isi_bersih = preg_replace('/<br\s*\/?>/i', " ", $isi_bersih);
        
        // 3. Hapus SEMUA sisa tag HTML, termasuk <p class="..." style="..."> dan <strong>.
        $isi_bersih = strip_tags($isi_bersih);
        
        // 4. Decode entitas HTML seperti &mdash; atau &rsquo; menjadi karakter aslinya.
        $isi_bersih = html_entity_decode($isi_bersih, ENT_QUOTES, 'UTF-8');
        
        // 5. Pecah teks menjadi array berdasarkan pemisah paragraf.
        $paragraf_array = explode("---PEMISAH_PARAGRAF---", $isi_bersih);

        // Simpan posisi Y awal untuk kolom
        $this->y0 = $this->GetY();
        // Mulai dari kolom pertama
        $this->SetCol(0);

        // --- Logika Cetak ---
        foreach ($paragraf_array as $paragraf) {
            $teks_paragraf = iconv('UTF-8', 'ISO-8859-1//TRANSLIT', trim($paragraf));
            
            if (strlen($teks_paragraf) > 0) {
                // Teks yang masuk ke sini sudah bersih total, sehingga 'J' akan berfungsi.
                $this->MultiCell(0, 5, $teks_paragraf, 0, 'J');
                
                $this->Ln(2); 
            }
        }
    }
}


// 4. PEMBUATAN DOKUMEN PDF
$pdf = new PDF('P', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
// MARGIN BAWAH DIPERSEMPIT
$pdf->SetAutoPageBreak(true, 25); 

// Cetak Cover (Judul, Meta, Gambar)
$pdf->PrintCover($berita['judul'], $berita['penulis'], $berita['tanggal_publikasi'], $berita['gambar_utama']);

// Cetak Isi Berita dalam Dua Kolom
$pdf->PrintChapterBody($berita['isi']);

// 5. OUTPUT PDF
$nama_file = 'Berita - ' . $berita['slug'] . '.pdf';
$pdf->Output('I', $nama_file);

// 5. OUTPUT PDF
$nama_file = 'Berita - ' . $berita['slug'] . '.pdf';
$pdf->Output('I', $nama_file);

// HAPUS FILE QR CODE SEMENTARA SETELAH PDF DIBUAT
$qrCodeFile = QR_TEMP_DIR . $berita['slug'] . '.png';
if (file_exists($qrCodeFile)) {
    unlink($qrCodeFile);
}
?>