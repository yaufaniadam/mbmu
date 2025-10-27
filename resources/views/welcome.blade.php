<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Selamat Datang di MBM App</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        xintegrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">

    <!-- Google Fonts - Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            background-color: #f8f9fa;
        }

        .navbar {
            box-shadow: 0 2px 4px rgba(0, 0, 0, .05);
        }

        .navbar-brand {
            font-weight: 700;
            font-size: 1.5rem;
            color: #0d6efd;
            /* Bootstrap primary color */
        }

        .hero-section {
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            flex-grow: 1;
            /* Ini akan membuat hero section mengisi sisa ruang */
            background-color: #ffffff;
            padding: 2rem;
        }

        .hero-section h1 {
            font-weight: 700;
            font-size: 3rem;
            /* max-width: 600px; */ /* Dihapus agar bisa menyesuaikan kolom */
            color: #343a40;
        }

        @media (max-width: 768px) {
            .hero-section h1 {
                font-size: 2.25rem;
            }
            .hero-section {
                text-align: center; /* Memastikan text center di mobile */
            }
        }
    </style>
</head>

<body>

    <!-- Header / Navbar -->
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid mx-lg-5">
                <!-- Logo Teks -->
                <a class="navbar-brand" href="/">MBM App</a>

                <!-- Tombol Login di Kanan -->
                <div class="ms-auto">
                    {{-- 
                        Ini adalah helper Blade untuk rute. 
                        Akan mengarah ke rute 'login' jika ada, 
                        jika tidak, fallback ke '/login' 
                    --}}
                    <a href="{{ route('login') ?? url('/login') }}" class="btn btn-primary rounded-pill px-4">
                        Login
                    </a>
                </div>
            </div>
        </nav>
    </header>

    <!-- Main Content - Hero Section -->
    <main class="hero-section">
        <div class="container">
            <!-- Modifikasi: Tambahkan align-items-center untuk menyejajarkan kolom secara vertikal -->
            <div class="row align-items-center">

                <!-- Kolom Teks -->
                <!-- Dibuat rata kiri di layar medium ke atas (text-md-start) -->
                <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
                    <h3>Selamat datang di </h3>
                    <h1 class="display-4">
                        Makan Bergizi Muhammadiyah
                    </h1>
                </div>


            </div>
        </div>
    </main>

    <!-- Bootstrap JS Bundle (diperlukan untuk beberapa komponen, opsional untuk halaman ini) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        xintegrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>

