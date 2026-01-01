<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contoh Penggunaan API Berita MTsN 1 Way Kanan</title>
    
    <!-- Menggunakan Bootstrap untuk styling cepat -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f4f7f6;
        }
        .news-card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
        }
        .news-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        }
        .news-card .card-img-top {
            border-top-left-radius: 12px;
            border-top-right-radius: 12px;
            height: 200px;
            object-fit: cover;
        }
        .news-card .card-title a {
            text-decoration: none;
            color: #333;
            transition: color 0.3s ease;
        }
        .news-card .card-title a:hover {
            color: #28a745;
        }
        .news-card .card-meta {
            font-size: 0.85rem;
            color: #777;
        }
    </style>
</head>
<body>

    <div class="container my-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 text-center">
                <h1 class="mb-3">Berita Terbaru dari MTsN 1 Way Kanan</h1>
                <p class="lead text-muted">Halaman ini mengambil data secara dinamis menggunakan API.</p>
            </div>
        </div>

        <!-- Form untuk memasukkan API Key -->
        <div class="row justify-content-center mt-4">
            <div class="col-lg-6">
                <div class="input-group mb-3">
                    <input type="text" class="form-control" id="apiKeyInput" placeholder="Masukkan API Key Anda di sini...">
                    <button class="btn btn-success" type="button" id="loadNewsBtn">Muat Berita</button>
                </div>
            </div>
        </div>

        <!-- Container untuk menampilkan berita -->
        <div id="news-container" class="row g-4 mt-4">
            <!-- Berita akan dimuat di sini oleh JavaScript -->
        </div>
        
        <!-- Container untuk pesan status (loading/error) -->
        <div id="status-message" class="text-center mt-5"></div>

    </div>

    <script>
        // Menambahkan event listener ke tombol 'Muat Berita'
        document.getElementById('loadNewsBtn').addEventListener('click', function() {
            const apiKey = document.getElementById('apiKeyInput').value;
            const newsContainer = document.getElementById('news-container');
            const statusMessage = document.getElementById('status-message');

            // Validasi sederhana
            if (!apiKey) {
                statusMessage.innerHTML = `<div class="alert alert-warning">Silakan masukkan API Key terlebih dahulu.</div>`;
                return;
            }

            // Tampilkan pesan loading
            newsContainer.innerHTML = ''; // Kosongkan kontainer berita
            statusMessage.innerHTML = `<div class="spinner-border text-success" role="status"><span class="visually-hidden">Loading...</span></div>`;

            // Buat URL API lengkap
            const apiUrl = `/api/berita.php?api_key=${apiKey}`;

            // Panggil API menggunakan Fetch
            fetch(apiUrl)
                .then(response => {
                    // Cek apakah response dari server OK
                    if (!response.ok) {
                        // Jika tidak OK, coba baca pesan error dari API
                        return response.json().then(err => { throw new Error(err.error || `Error ${response.status}: ${response.statusText}`); });
                    }
                    return response.json();
                })
                .then(data => {
                    // Hapus pesan loading
                    statusMessage.innerHTML = '';

                    if (data.length === 0) {
                        statusMessage.innerHTML = `<div class="alert alert-info">Tidak ada berita untuk ditampilkan.</div>`;
                        return;
                    }

                    // Loop melalui setiap item berita dan buat kartu HTML-nya
                    data.forEach(berita => {
                        // Siapkan gambar, gunakan placeholder jika tidak ada
                        const imageUrl = berita.gambar_utama_url || `https://placehold.co/600x400/E0F2F1/198754?text=Berita`;
                        
                        // Format tanggal
                        const tanggal = new Date(berita.tanggal_publikasi).toLocaleDateString('id-ID', {
                            day: 'numeric', month: 'long', year: 'numeric'
                        });

                        const newsCard = `
                            <div class="col-lg-4 col-md-6">
                                <div class="card h-100 news-card">
                                    <img src="${imageUrl}" class="card-img-top" alt="${berita.judul}">
                                    <div class="card-body d-flex flex-column">
                                        <h5 class="card-title">
                                            <a href="#">${berita.judul}</a>
                                        </h5>
                                        <p class="card-meta">
                                            Oleh: ${berita.penulis} | ${tanggal}
                                        </p>
                                        <p class="card-text small text-muted">${berita.kategori}</p>
                                    </div>
                                </div>
                            </div>
                        `;
                        newsContainer.innerHTML += newsCard;
                    });
                })
                .catch(error => {
                    // Jika terjadi error (misal: API key salah, server down)
                    console.error('Error:', error);
                    statusMessage.innerHTML = `<div class="alert alert-danger"><strong>Gagal Memuat:</strong> ${error.message}</div>`;
                });
        });
    </script>
</body>
</html>
