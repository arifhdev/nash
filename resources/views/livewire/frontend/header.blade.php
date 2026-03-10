<div x-data="{ scrolled: false }" 
     @scroll.window="scrolled = (window.pageYOffset > 40)" 
     class="w-full font-sans sticky top-0 z-50 shadow-sm transition-all duration-300">
    
    {{-- TAMBAHKAN x-cloak DISINI --}}
    <div x-show="!scrolled" 
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
                {{-- FIX: Tambahkan width="20" height="20" --}}
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" class="h-5 w-5" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91C2.13 13.66 2.59 15.36 3.45 16.86L2.05 22L7.3 20.62C8.75 21.41 10.38 21.83 12.04 21.83C17.5 21.83 21.95 17.38 21.95 11.92C21.95 9.27 20.92 6.78 19.05 4.91C17.18 3.03 14.69 2 12.04 2ZM12.05 20.16C10.55 20.16 9.07 19.76 7.76 18.99L7.45 18.8L4.32 19.62L5.16 16.56L4.96 16.24C4.12 14.9 3.68 13.38 3.68 11.91C3.68 7.3 7.43 3.55 12.05 3.55C14.31 3.55 16.44 4.43 18.04 6.03C19.65 7.64 20.53 9.77 20.53 12.03C20.53 16.63 16.73 20.16 12.05 20.16Z"/>
                    <path d="M16.64 14.6C16.39 14.47 15.15 13.86 14.92 13.78C14.69 13.69 14.52 13.65 14.35 13.9C14.18 14.15 13.7 14.71 13.55 14.88C13.4 15.05 13.25 15.07 13 14.94C12.75 14.82 11.94 14.55 10.98 13.7C10.23 13.03 9.72 12.21 9.6 11.98C9.47 11.75 9.58 11.64 9.7 11.51C9.81 11.4 9.95 11.22 10.07 11.08C10.2 10.94 10.24 10.84 10.32 10.67C10.4 10.5 10.36 10.36 10.3 10.23C10.24 10.1 9.74 8.87 9.53 8.37C9.33 7.89 9.13 7.95 8.98 7.95C8.84 7.95 8.68 7.95 8.52 7.95C8.36 7.95 8.1 8.01 7.88 8.25C7.66 8.49 7.04 9.07 7.04 10.25C7.04 11.43 7.9 12.57 8.02 12.73C8.14 12.89 9.71 15.31 12.11 16.35C12.68 16.6 13.13 16.75 13.48 16.86C14.05 17.04 14.57 17.02 14.98 16.96C15.44 16.89 16.39 16.38 16.59 15.82C16.79 15.26 16.79 14.78 16.73 14.68C16.67 14.58 16.5 14.52 16.25 14.4H16.64V14.6Z"/>
                </svg>
                <span class="font-medium tracking-wide">Butuh Bantuan?</span>
            </a>
        
            <div class="flex gap-6 font-medium">
                <a href="#" class="hover:underline">Infografik</a>
                <a href="#" class="hover:underline">Blog</a>
            </div>
        </div>
    </div>

    <div :class="scrolled ? 'bg-white/90 backdrop-blur-md shadow-md' : 'bg-gray-50 border-b border-gray-100'" 
         class="transition-all duration-300">
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-[72px]">

                <div class="flex-shrink-0 flex items-center">
                    <a href="/" wire:navigate>
                        {{-- FIX: Tambahkan style="height: 60px" agar logo tidak meledak ukurannya --}}
                        <img class="h-15 w-auto" style="height: 60px; width: auto;" src="{{ asset('images/logo.png') }}" alt="Akademi Satu Hati">
                    </a>
                </div>

                <nav class="hidden md:flex space-x-8 items-center text-gray-800 text-[15px] font-medium">
                    
                    <div x-data="{ open: false }" class="relative group">
                        <button @click="open = !open" @click.outside="open = false" 
                                class="flex items-center gap-1 hover:text-[#ED1C24] transition">
                            <span class="text-[#ED1C24]">Konten</span>
                            {{-- FIX: Tambahkan width="16" height="16" --}}
                            <svg width="16" height="16" :class="{'rotate-180': open}" class="w-4 h-4 text-[#ED1C24] transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        
                        {{-- TAMBAHKAN x-cloak DISINI JUGA --}}
                        <div x-show="open" x-cloak x-transition
                             class="absolute top-full left-0 mt-2 w-48 bg-white shadow-xl rounded-lg py-2 border border-gray-100 z-50">
                            <a href="#" class="block px-4 py-2 hover:bg-red-50 hover:text-[#ED1C24]">Artikel</a>
                            <a href="#" class="block px-4 py-2 hover:bg-red-50 hover:text-[#ED1C24]">Video</a>
                        </div>
                    </div>

                    <a href="#" class="hover:text-[#ED1C24] transition">Tanya Jawab</a>
                    <a href="#" class="hover:text-[#ED1C24] transition">Kontak Kami</a>
                    <a href="#" class="hover:text-[#ED1C24] transition">KLHN</a>
                    <a href="#" class="hover:text-[#ED1C24] transition">Panduan</a>
                    <a href="#" class="hover:text-[#ED1C24] transition">CRM</a>
                </nav>

                <div class="flex items-center gap-5 text-gray-800">
                    <button class="hover:text-[#ED1C24] transition">
                         {{-- FIX: Tambahkan width="24" height="24" --}}
                        <svg width="24" height="24" class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                    
                    @include('livewire.frontend.partials.profile-dropdown')

                    <button class="md:hidden hover:text-[#ED1C24] transition">
                         {{-- FIX: Tambahkan width="28" height="28" --}}
                        <svg width="28" height="28" class="w-7 h-7" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>