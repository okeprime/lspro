<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" style="scroll-behavior: smooth;">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ config('app.name', 'LSPro BBPM SDLP') }}</title>
        
        <!-- Favicon -->
        <link rel="icon" href="{{ asset('favicon.ico') }}?v={{ time() }}" type="image/x-icon">
        <link rel="shortcut icon" href="{{ asset('favicon.ico') }}?v={{ time() }}" type="image/x-icon">

        <!-- Google Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Bootstrap 5 CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">

        <!-- Scripts -->
        @viteReactRefresh
        @vite(['resources/js/app.jsx'])
        @inertiaHead
    </head>
    <body>
        <div id="global-loader" style="position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: #ffffff; z-index: 999999; display: flex; justify-content: center; align-items: center; transition: opacity 0.4s ease-out;">
            <div style="position: relative; width: 100px; height: 100px; display: flex; justify-content: center; align-items: center;">
                <img src="/assets/kementan.png" alt="Loading..." style="width: 55px; height: auto; position: absolute; z-index: 1;" />
                <svg width="100" height="100" viewBox="0 0 100 100" style="position: absolute; z-index: 2; animation: spin-circle 1.2s linear infinite;">
                    <circle cx="50" cy="50" r="44" fill="none" stroke="#f1f5f9" stroke-width="6" />
                    <circle cx="50" cy="50" r="44" fill="none" stroke="#16a34a" stroke-width="6" stroke-linecap="round" stroke-dasharray="80 200" />
                </svg>
            </div>
        </div>
        <style>
            @keyframes spin-circle { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
            body.loaded #global-loader { opacity: 0; pointer-events: none; }
        </style>
        @inertia
        
        <!-- Bootstrap 5 JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>
