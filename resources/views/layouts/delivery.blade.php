<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $title ?? 'MBM Delivery' }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f5f5f5; /* Material Design generic background */
        }
        .material-shadow-1 { box-shadow: 0 1px 3px rgba(0,0,0,0.12), 0 1px 2px rgba(0,0,0,0.24); }
        .material-shadow-2 { box-shadow: 0 3px 6px rgba(0,0,0,0.16), 0 3px 6px rgba(0,0,0,0.23); }
        .material-btn {
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-weight: 500;
            transition: all 0.3s cubic-bezier(.25,.8,.25,1);
        }
        .material-btn:active { box-shadow: none; }
        .material-input:focus ~ label,
        .material-input:not(:placeholder-shown) ~ label {
            top: -0.5rem;
            font-size: 0.75rem;
            color: #6200ee;
        }
        .material-input:focus { border-bottom-color: #6200ee; }
    </style>
</head>
<body class="antialiased text-gray-800">

    <div class="min-h-screen flex flex-col">
        <!-- App Bar -->
        <header class="bg-primary hover:bg-primary-dark transition-colors duration-300 text-white shadow-md z-10 sticky top-0">
            <div class="container mx-auto px-4 h-14 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    @if(request()->routeIs('delivery.dashboard'))
                        <span class="material-icons">local_shipping</span>
                    @endif
                    <h1 class="text-xl font-medium tracking-wide">Pengantaran MBM</h1>
                </div>
                
                @auth
                    <form method="POST" action="{{ route('delivery.logout') }}">
                        @csrf
                        <button type="submit" class="flex items-center gap-1 text-sm font-medium opacity-90 hover:opacity-100 transition">
                            <span class="material-icons text-lg">logout</span>
                            <span class="hidden sm:inline">Keluar</span>
                        </button>
                    </form>
                @endauth
            </div>
        </header>

        <!-- Main Content -->
        <main class="flex-grow container mx-auto px-4 py-6 max-w-lg">
            {{ $slot }}
        </main>
        
        <!-- Simple Footer -->
        <footer class="py-4 text-center text-xs text-gray-500">
            &copy; {{ date('Y') }} MBM Universal
        </footer>
    </div>

    @livewireScripts
</body>
</html>
