<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    
    <title>{{ $title ?? 'Akademi Satu Hati' }}</title>
    
    <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <style>
        [x-cloak] { display: none !important; }
    </style>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-gray-50 font-sans antialiased text-gray-900 flex flex-col min-h-screen">

    {{-- 1. HEADER --}}
    {{-- Memanggil file: resources/views/livewire/frontend/header.blade.php --}}
    @include('livewire.frontend.header')

    {{-- 2. MAIN CONTENT (Slot untuk Home Page) --}}
    <main class="flex-grow">
        {{ $slot }}
    </main>

    {{-- 3. FOOTER --}}
    {{-- Memanggil file: resources/views/livewire/frontend/partials/footer.blade.php --}}
    @include('livewire.frontend.partials.footer')

    @livewireScripts
</body>
</html>