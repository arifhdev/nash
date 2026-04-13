<div x-data="{ 
        scrolled: false, 
        mobileMenuOpen: false, 
        searchOpen: false,
        searchQuery: ''
     }" 
     @scroll.window="scrolled = (window.pageYOffset > 40)" 
     class="w-full font-sans sticky top-0 z-50 shadow-sm transition-all duration-300 relative">
    
    {{-- Top Bar Merah --}}
    <div x-show="!scrolled && !searchOpen" 
         x-cloak 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 -translate-y-full"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-full"
         class="bg-[#ED1C24] text-white text-sm py-2 px-4 origin-top">
         <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 flex justify-between items-center">
            <a href="#" class="flex items-center gap-2 hover:opacity-90 transition">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 20.16C10.55 20.16 9.07 19.76 7.76 18.99L7.45 18.8L4.32 19.62L5.16 16.56L4.96 16.24C4.12 14.9 3.68 13.38 3.68 11.91C3.68 7.3 7.43 3.55 12.05 3.55C14.31 3.55 16.44 4.43 18.04 6.03C19.65 7.64 20.53 9.77 20.53 12.03C20.53 16.63 16.73 20.16 12.05 20.16Z"/>
                    <path d="M16.64 14.6C16.39 14.47 15.15 13.86 14.92 13.78C14.69 13.69 14.52 13.65 14.35 13.9C14.18 14.15 13.7 14.71 13.55 14.88C13.4 15.05 13.25 15.07 13 14.94C12.75 14.82 11.94 14.55 10.98 13.7C10.23 13.03 9.72 12.21 9.6 11.98C9.47 11.75 9.58 11.64 9.7 11.51C9.81 11.4 9.95 11.22 10.07 11.08C10.2 10.94 10.24 10.84 10.32 10.67C10.4 10.5 10.36 10.36 10.3 10.23C10.24 10.1 9.74 8.87 9.53 8.37C9.33 7.89 9.13 7.95 8.98 7.95C8.84 7.95 8.68 7.95 8.52 7.95C8.36 7.95 8.1 8.01 7.88 8.25C7.66 8.49 7.04 9.07 7.04 10.25C7.04 11.43 7.9 12.57 8.02 12.73C8.14 12.89 9.71 15.31 12.11 16.35C12.68 16.6 13.13 16.75 13.48 16.86C14.05 17.04 14.57 17.02 14.98 16.96C15.44 16.89 16.39 16.38 16.59 15.82C16.79 15.26 16.79 14.78 16.73 14.68C16.67 14.58 16.5 14.52 16.25 14.4H16.64V14.6Z"/>
                </svg>
                <span class="font-medium tracking-wide">Butuh Bantuan?</span>
            </a>
            <div class="flex gap-6 font-medium">
                <a href="{{ route('certificate.verify') }}" class="hover:underline">Certificate Verification</a>
            </div>
        </div>
    </div>

    {{-- Main Navbar --}}
    <div :class="scrolled ? 'bg-white/95 backdrop-blur-md shadow-md' : 'bg-gray-50 border-b border-gray-100'" 
         class="transition-all duration-300 relative z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[72px]">
                <div class="flex-shrink-0 flex items-center">
                    <a href="/" wire:navigate>
                        <img class="h-15 w-auto" style="height: 60px; width: auto;" src="{{ asset('images/logo.png') }}" alt="Akademi Satu Hati">
                    </a>
                </div>

                {{-- Desktop Nav --}}
                <nav class="hidden md:flex space-x-8 items-center text-gray-800 text-[15px] font-bold h-full">
                    <a href="{{ route('courses.index') }}" wire:navigate class="hover:text-[#ED1C24] transition-colors {{ request()->routeIs('courses.*') || request()->routeIs('course.*') ? 'text-[#ED1C24]' : '' }}">Courses</a>
                    <a href="{{ route('events.index') }}" wire:navigate class="hover:text-[#ED1C24] transition-colors {{ request()->routeIs('events.*') || request()->routeIs('event.*') ? 'text-[#ED1C24]' : '' }}">Events</a>
                    <a href="{{ route('booklets.index') }}" wire:navigate class="hover:text-[#ED1C24] transition-colors {{ request()->routeIs('booklets.*') || request()->routeIs('booklet.*') ? 'text-[#ED1C24]' : '' }}">Booklets</a>
                    
                    {{-- DROPDOWN KLHN DESKTOP --}}
                    <div class="relative h-full flex items-center" x-data="{ klhnOpen: false }" @mouseenter="klhnOpen = true" @mouseleave="klhnOpen = false">
                        <a href="#" class="hover:text-[#ED1C24] transition-colors flex items-center gap-1 {{ request()->routeIs('klhn.*') ? 'text-[#ED1C24]' : '' }}">
                            KLHN
                            <svg class="w-4 h-4 transition-transform duration-200" :class="klhnOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                        </a>
                        
                        {{-- Dropdown Menu --}}
                        <div x-show="klhnOpen" 
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute top-[60px] left-1/2 -translate-x-1/2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-2 z-50">
                            
                            {{-- Panah penunjuk ke atas --}}
                            <div class="absolute -top-2 left-1/2 -translate-x-1/2 w-4 h-4 bg-white border-t border-l border-gray-100 rotate-45"></div>
                            
                            <div class="relative bg-white z-10 rounded-xl overflow-hidden">
                                <a href="{{ route('klhn.index') }}" wire:navigate class="block px-5 py-3 text-sm hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('klhn.index') ? 'text-[#ED1C24] bg-red-50' : 'text-gray-700' }}">
                                    Galeri
                                </a>
                                <a href="https://konteslayananhondanasional.com/" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-5 py-3 text-sm hover:bg-red-50 hover:text-[#ED1C24] transition-colors text-gray-700 group">
                                    <span>Submission</span>
                                    <svg class="w-3.5 h-3.5 text-gray-400 group-hover:text-[#ED1C24] transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('broadcast.index') }}" wire:navigate class="hover:text-[#ED1C24] transition-colors {{ request()->routeIs('broadcast.*') ? 'text-[#ED1C24]' : '' }}">Live Broadcast</a>
                   </nav>

                <div class="flex items-center gap-5 text-gray-800">
                    {{-- Tombol Search Desktop --}}
                    <button @click="searchOpen = !searchOpen; if(searchOpen) $nextTick(() => $refs.searchInput.focus())" 
                            class="hover:text-[#ED1C24] transition hidden md:block">
                        <svg x-show="!searchOpen" width="24" height="24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        <svg x-show="searchOpen" x-cloak width="24" height="24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    
                    @include('livewire.frontend.partials.profile-dropdown')

                    {{-- Tombol Hamburger (Mobile) --}}
                    <button @click="mobileMenuOpen = true" class="md:hidden hover:text-[#ED1C24] transition focus:outline-none">
                        <svg width="28" height="28" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Input Search Bar (Slide Down) --}}
    <div x-show="searchOpen" 
         x-cloak
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 -translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 translate-y-0"
         x-transition:leave-end="opacity-0 -translate-y-4"
         class="absolute inset-x-0 top-full bg-white border-b border-gray-200 shadow-xl z-40 py-4 px-4">
        <div class="max-w-3xl mx-auto relative">
            <input x-ref="searchInput"
                   type="text" 
                   x-model="searchQuery"
                   @keydown.enter="window.location.href = '/courses?search=' + searchQuery"
                   placeholder="Cari course atau modul materi..." 
                   class="w-full pl-12 pr-24 py-3 rounded-xl border border-gray-200 focus:ring-2 focus:ring-red-100 focus:border-[#ED1C24] outline-none transition-all">
            <div class="absolute left-4 top-3.5 text-gray-400">
                <svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button @click="window.location.href = '/courses?search=' + searchQuery"
                    class="absolute right-2 top-2 bottom-2 bg-[#ED1C24] text-white px-5 rounded-lg font-bold text-sm hover:bg-red-700 transition">
                Cari
            </button>
        </div>
    </div>

    {{-- =====================================
         MOBILE SIDEBAR MENU (SLIDE DARI KANAN)
         ===================================== --}}
    
    {{-- 1. Overlay Blur Gelap --}}
    <div x-show="mobileMenuOpen" 
         x-cloak
         @click="mobileMenuOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm md:hidden">
    </div>

    {{-- 2. Sidebar Panel --}}
    <div x-show="mobileMenuOpen"
         x-cloak
         x-transition:enter="transition ease-in-out duration-300 transform"
         x-transition:enter-start="translate-x-full"
         x-transition:enter-end="translate-x-0"
         x-transition:leave="transition ease-in-out duration-300 transform"
         x-transition:leave-start="translate-x-0"
         x-transition:leave-end="translate-x-full"
         class="fixed inset-y-0 right-0 z-[70] w-64 bg-white shadow-2xl md:hidden overflow-y-auto flex flex-col">
         
        {{-- Header Sidebar (Logo & Tombol X) --}}
        <div class="flex items-center justify-between p-4 border-b border-gray-100">
            <span class="font-extrabold text-gray-900 text-lg">Menu Navigasi</span>
            <button @click="mobileMenuOpen = false" class="text-gray-400 hover:text-[#ED1C24] p-2 bg-gray-50 rounded-full transition-colors focus:outline-none">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Search Mobile --}}
        <div class="p-4 border-b border-gray-50">
            <div class="relative">
                <input type="text" 
                       x-model="searchQuery"
                       @keydown.enter="window.location.href = '/courses?search=' + searchQuery"
                       placeholder="Cari..." 
                       class="w-full pl-10 pr-4 py-2 bg-gray-100 border-transparent rounded-lg focus:bg-white focus:ring-1 focus:ring-red-500 text-sm">
                <div class="absolute left-3 top-2.5 text-gray-400">
                    <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Daftar Menu --}}
        <div class="p-4 space-y-2 font-bold text-gray-800 flex-1">
            <a href="{{ route('courses.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-3.5 rounded-xl hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('courses.*') ? 'text-[#ED1C24] bg-red-50' : '' }}">Courses</a>
            
            <a href="{{ route('events.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-3.5 rounded-xl hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('events.*') ? 'text-[#ED1C24] bg-red-50' : '' }}">Events</a>
            
            <a href="{{ route('booklets.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-3.5 rounded-xl hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('booklets.*') ? 'text-[#ED1C24] bg-red-50' : '' }}">Booklets</a>

            {{-- DROPDOWN KLHN MOBILE --}}
            <div x-data="{ klhnMobileOpen: false }" class="relative">
                <button @click="klhnMobileOpen = !klhnMobileOpen" class="w-full flex items-center justify-between px-4 py-3.5 rounded-xl hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('klhn.*') ? 'text-[#ED1C24] bg-red-50' : '' }}">
                    <span>KLHN</span>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="klhnMobileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path></svg>
                </button>
                
                <div x-show="klhnMobileOpen" 
                     x-collapse 
                     class="mt-1 ml-4 border-l-2 border-red-100 pl-2 space-y-1">
                    <a href="{{ route('klhn.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-2.5 text-sm font-semibold rounded-lg hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('klhn.index') ? 'text-[#ED1C24]' : 'text-gray-600' }}">
                        Galeri
                    </a>
                    <a href="https://konteslayananhondanasional.com/" target="_blank" rel="noopener noreferrer" class="flex items-center justify-between px-4 py-2.5 text-sm font-semibold rounded-lg hover:bg-red-50 hover:text-[#ED1C24] transition-colors text-gray-600">
                        <span>Submission</span>
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    </a>
                </div>
            </div>
            
            <a href="{{ route('broadcast.index') }}" wire:navigate @click="mobileMenuOpen = false" class="block px-4 py-3.5 rounded-xl hover:bg-red-50 hover:text-[#ED1C24] transition-colors {{ request()->routeIs('broadcast.*') ? 'text-[#ED1C24] bg-red-50' : '' }}">Live Broadcast</a>
 </div>
        
        {{-- Footer Sidebar (Bantuan) --}}
        <div class="p-6 bg-gray-50 border-t border-gray-100">
            <a href="#" class="flex items-center justify-center gap-2 text-[#ED1C24] font-bold">
                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 20.16C10.55 20.16 9.07 19.76 7.76 18.99L7.45 18.8L4.32 19.62L5.16 16.56L4.96 16.24C4.12 14.9 3.68 13.38 3.68 11.91C3.68 7.3 7.43 3.55 12.05 3.55C14.31 3.55 16.44 4.43 18.04 6.03C19.65 7.64 20.53 9.77 20.53 12.03C20.53 16.63 16.73 20.16 12.05 20.16Z"/></svg>
                Pusat Bantuan
            </a>
        </div>
    </div>
</div>