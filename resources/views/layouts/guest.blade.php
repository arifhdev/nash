<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Akademi Satu Hati') }}</title>
        <link rel="icon" href="{{ asset('images/favicon.png') }}" type="image/png">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700,800&display=swap" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])

        @livewireStyles
        <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-GWZ3PCDXX3"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-GWZ3PCDXX3');
</script>
    </head>
    <body class="font-sans text-gray-900 antialiased bg-[#F3F4F6]">
        
        {{-- 
            Wrapper Utama:
            - min-h-screen: Agar tinggi minimal setinggi layar (full height).
            - flex-col & justify-center: Agar konten (Form) selalu di tengah vertikal & horizontal.
        --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
            
            {{-- Slot: Area ini akan diisi oleh Component Register/Login --}}
            {{ $slot }}

        </div>

        @livewireScripts
    </body>
</html>