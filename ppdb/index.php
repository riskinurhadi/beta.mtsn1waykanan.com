<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PPDB Segera Hadir - MTs Negeri 1 Way Kanan</title>
    <link rel="icon" type="image/png" href="favicon.png">
    <!-- CSS Libraries -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        /* CSS Internal untuk Halaman Segera Hadir */
        body, html {
            height: 100%;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }

        .coming-soon-container {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            height: 100%;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .coming-soon-container::before {
    height: 100vh;
    background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('img/heromts.jpg') no-repeat center center;
    background-size: cover;
    color: #ffffff;
    display: flex;
    align-items: center;
        }

        .coming-soon-container::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, rgba(25, 135, 84, 0.8), rgba(0, 105, 92, 0.85)); /* Overlay hijau */
            z-index: -1;
        }

        .coming-soon-content {
            z-index: 1;
            padding: 2rem;
        }

        .main-title {
            font-size: 3.5rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        }

        .subtitle {
            font-size: 1.5rem;
            font-weight: 400;
            margin-top: 0.5rem;
        }

        .stay-tuned {
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 2rem;
            padding: 0.5rem 1.5rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50px;
            display: inline-block;
        }

        .back-button {
                display: inline-block;
    background-color: #28a745;
    color: #fff;
    padding: 10px 20px;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background-color 0.3s ease;
        }

        .back-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }

        @media (max-width: 768px) {
            .main-title {
                font-size: 2.5rem;
            }
            .subtitle {
                font-size: 1.2rem;
            }
        }
    </style>
</head>
<body>

    <div class="coming-soon-container">
        <div class="coming-soon-content">
            <div class="stay-tuned">
                Stay Tuned
            </div>
            <h1 class="main-title">Segera Hadir</h1>
            <p class="subtitle">Website PPDB MTs Negeri 1 Way Kanan</p>
            <div>
                <a href="../index.php" class="btn btn-light btn-lg back-button mt-2">Kembali ke Beranda</a>
            </div>
        </div>
    </div>

</body>
</html>
