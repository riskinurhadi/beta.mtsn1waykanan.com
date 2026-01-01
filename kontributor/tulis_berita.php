<?php
// Memanggil file konfigurasi dari direktori utama
require_once '../admin/config.php';

// --- SECURITY CHECK ---
if (!isset($_SESSION['kontributor_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data kontributor dari session
$kontributor_id = $_SESSION['kontributor_id'];
$kontributor_nama = $_SESSION['kontributor_nama'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tulis Berita Baru - Portal Kontributor</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- PERUBAHAN: Menambahkan library untuk Summernote Text Editor -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <style>
        :root { --primary-color: #28a745; --sidebar-bg: #2c3e50; --sidebar-text: #ecf0f1; --sidebar-active: #34495e; --main-bg: #f4f7f6; --text-color: #333; --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08); }
        body { font-family: 'Poppins', sans-serif; background-color: var(--main-bg); }
        .sidebar { width: 260px; background-color: var(--sidebar-bg); color: var(--sidebar-text); height: 100vh; position: fixed; left: 0; top: 0; display: flex; flex-direction: column; z-index: 1000; }
        .sidebar-header { padding: 20px; text-align: center; border-bottom: 1px solid var(--sidebar-active); }
        .sidebar-header h5 { margin: 0; font-weight: 600; }
        .sidebar-nav { list-style: none; padding: 20px 0; }
        .sidebar-nav li a { display: flex; align-items: center; padding: 15px 20px; color: var(--sidebar-text); text-decoration: none; font-size: 15px; transition: background-color 0.2s ease; }
        .sidebar-nav li a i { width: 30px; font-size: 18px; margin-right: 10px; }
        .sidebar-nav li a:hover, .sidebar-nav li.active a { background-color: var(--sidebar-active); }
        .main-content { margin-left: 260px; padding: 20px; }
        .header { display: flex; justify-content: space-between; align-items: center; background-color: #fff; padding: 15px 20px; border-radius: 12px; box-shadow: var(--card-shadow); margin-bottom: 30px; }
        .header h1 { font-size: 24px; font-weight: 600; margin: 0; }
        .card-custom { border: none; border-radius: 12px; box-shadow: var(--card-shadow); }
        /* PERUBAHAN: Style override untuk Summernote agar sesuai tema */
        .note-editor.note-frame { border-radius: .375rem; border-color: #dee2e6; }
        .note-editor.note-frame .note-editing-area .note-editable { background-color: #fff; }
    </style>
</head>
<body>

    <aside class="sidebar">
        <div class="sidebar-header">
            <h5>Portal Kontributor</h5>
        </div>
        <ul class="sidebar-nav">
            <li><a href="dashboard.php"><i class="fas fa-tachometer-alt"></i><span>Dashboard</span></a></li>
            <li class="active"><a href="tulis_berita.php"><i class="fas fa-plus-circle"></i><span>Tulis Berita</span></a></li>
            <li><a href="akun_kontributor.php"><i class="fas fa-user-circle"></i><span>Akun Saya</span></a></li>
            <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i><span>Logout</span></a></li>
        </ul>
    </aside>

    <main class="main-content">
        <header class="header">
            <h1>Tulis Berita Baru</h1>
            <div class="d-flex align-items-center">
                <span class="navbar-text me-3">
                    Halo, <?php echo htmlspecialchars($kontributor_nama); ?>
                </span>
            </div>
        </header>

        <div class="card card-custom">
            <div class="card-body p-4 p-md-5">
                <form id="newsForm">
                    <div class="mb-3">
                        <label for="apiKey" class="form-label fw-bold">Kode Izin Anda (API Key)</label>
                        <input type="text" class="form-control" id="apiKey" placeholder="Salin dan tempel Kode Izin Anda di sini" required>
                        <div class="form-text">Anda bisa menemukan Kode Izin di halaman "Akun Saya".</div>
                    </div>
                    <hr class="my-4">
                    <div class="mb-3">
                        <label for="judul" class="form-label">Judul Berita</label>
                        <input type="text" class="form-control" id="judul" required>
                    </div>
                    <div class="mb-3">
                        <label for="kategori" class="form-label">Kategori</label>
                        <input type="text" class="form-control" id="kategori" required>
                    </div>
                    <div class="mb-3">
                        <label for="isi" class="form-label">Isi Berita</label>
                        <!-- PERUBAHAN: Textarea ini akan diubah menjadi Summernote -->
                        <textarea class="form-control" id="isi" rows="10" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="gambarUtama" class="form-label">Gambar Utama (Wajib)</label>
                        <input class="form-control" type="file" id="gambarUtama" accept="image/*" required>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Kirim Berita untuk Ditinjau</button>
                    </div>
                </form>
                <div id="status-message" class="mt-4"></div>
            </div>
        </div>
    </main>

    <!-- PERUBAHAN: Menambahkan library JS untuk Summernote -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // PERUBAHAN: Inisialisasi Summernote
        $(document).ready(function() {
            $('#isi').summernote({
                placeholder: 'Tulis isi berita Anda di sini...',
                tabsize: 2,
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'italic', 'underline', 'clear']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['insert', ['link', 'picture']],
                    ['view', ['fullscreen', 'codeview']]
                ]
            });
        });

        document.getElementById('newsForm').addEventListener('submit', function(event) {
            event.preventDefault();

            const statusMessage = document.getElementById('status-message');
            statusMessage.innerHTML = `<div class="alert alert-info">Mengirim data...</div>`;

            const apiKey = document.getElementById('apiKey').value;
            const judul = document.getElementById('judul').value;
            const kategori = document.getElementById('kategori').value;
            const penulis = '<?php echo addslashes($kontributor_nama); ?>';
            // PERUBAHAN: Mengambil isi dari Summernote
            const isi = $('#isi').summernote('code');
            const gambarFile = document.getElementById('gambarUtama').files[0];

            // PERUBAHAN: Validasi isi dari Summernote
            if ($('#isi').summernote('isEmpty')) {
                statusMessage.innerHTML = `<div class="alert alert-danger"><strong>Gagal:</strong> Isi berita tidak boleh kosong.</div>`;
                return;
            }

            const formData = new FormData();
            formData.append('judul', judul);
            formData.append('kategori', kategori);
            formData.append('penulis', penulis);
            formData.append('isi', isi);
            formData.append('gambar_utama', gambarFile);
            formData.append('id_kontributor', <?php echo $kontributor_id; ?>);

            const apiUrl = `../api/berita.php?api_key=${apiKey}`;

            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        title: 'Berhasil!',
                        text: 'Berita Anda telah berhasil dikirim dan akan segera ditinjau oleh admin.',
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        window.location.href = 'dashboard.php';
                    });
                } else {
                    throw new Error(data.error || 'Terjadi kesalahan yang tidak diketahui.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                statusMessage.innerHTML = `<div class="alert alert-danger"><strong>Gagal:</strong> ${error.message}</div>`;
            });
        });
    </script>
</body>
</html>
