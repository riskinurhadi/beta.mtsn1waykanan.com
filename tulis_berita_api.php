<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contoh Tulis Berita via API</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #f4f7f6; }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4 p-md-5">
                        <h2 class="card-title text-center mb-4">Publikasikan Berita Baru via API</h2>
                        
                        <!-- Form untuk mengirim berita -->
                        <form id="newsForm">
                            <div class="mb-3">
                                <label for="apiKey" class="form-label">Kode Izin Menulis Berita</label>
                                <input type="text" class="form-control" id="apiKey" required>
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label for="judul" class="form-label">Judul Berita</label>
                                <input type="text" class="form-control" id="judul" required>
                            </div>
                            <div class="mb-3">
                                <label for="kategori" class="form-label">Kategori</label>
                                <input type="text" class="form-control" id="kategori" required>
                            </div>
                            <div class="mb-3">
                                <label for="penulis" class="form-label">Nama Penulis</label>
                                <input type="text" class="form-control" id="penulis" required>
                            </div>
                            <div class="mb-3">
                                <label for="isi" class="form-label">Isi Berita</label>
                                <textarea class="form-control" id="isi" rows="6" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="gambarUtama" class="form-label">Gambar Utama</label>
                                <input class="form-control" type="file" id="gambarUtama" accept="image/*" required>
                            </div>
                            <div class="d-grid">
                                <button type="submit" class="btn btn-success btn-lg">Kirim Berita</button>
                            </div>
                        </form>
                        
                        <!-- Container untuk pesan status -->
                        <div id="status-message" class="mt-4"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('newsForm').addEventListener('submit', function(event) {
            event.preventDefault(); // Mencegah form dikirim secara normal

            const statusMessage = document.getElementById('status-message');
            statusMessage.innerHTML = `<div class="alert alert-info">Mengirim data...</div>`;

            // Kumpulkan data dari form
            const apiKey = document.getElementById('apiKey').value;
            const judul = document.getElementById('judul').value;
            const kategori = document.getElementById('kategori').value;
            const penulis = document.getElementById('penulis').value;
            const isi = document.getElementById('isi').value;
            const gambarFile = document.getElementById('gambarUtama').files[0];

            // Buat objek FormData untuk mengirim file
            const formData = new FormData();
            formData.append('judul', judul);
            formData.append('kategori', kategori);
            formData.append('penulis', penulis);
            formData.append('isi', isi);
            formData.append('gambar_utama', gambarFile);

            // Buat URL API lengkap
            const apiUrl = `/api/berita.php?api_key=${apiKey}`;

            // Kirim data menggunakan Fetch dengan metode POST
            fetch(apiUrl, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    statusMessage.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
                    document.getElementById('newsForm').reset(); // Kosongkan form jika berhasil
                } else {
                    // Jika ada error dari API
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
