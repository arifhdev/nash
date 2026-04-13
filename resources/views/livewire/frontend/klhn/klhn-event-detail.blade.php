@php
    // Siapkan array data foto agar mudah dan aman dibaca oleh Alpine.js
    $photosData = $event->photos->map(function($photo) {
        return [
            'url' => Storage::url($photo->image),
            'caption' => $photo->caption ?? ''
        ];
    });
@endphp

<div class="bg-[#F3F4F6] min-h-screen py-10" 
     x-data="{ 
        lightboxOpen: false, 
        activeIndex: 0,
        photos: {{ $photosData->toJson() }},
        next() { this.activeIndex = (this.activeIndex === this.photos.length - 1) ? 0 : this.activeIndex + 1; },
        prev() { this.activeIndex = (this.activeIndex === 0) ? this.photos.length - 1 : this.activeIndex - 1; }
     }"
     @keydown.escape.window="lightboxOpen = false"
     @keydown.right.window="if(lightboxOpen) next()"
     @keydown.left.window="if(lightboxOpen) prev()">
     
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Tombol Kembali --}}
        <a href="{{ route('klhn.index') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-[#ED1C24] transition-colors mb-6 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Event
        </a>

        {{-- Header Info Event --}}
        <div class="bg-white rounded-2xl p-8 border border-gray-200 mb-8 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                <span class="px-3 py-1 bg-red-50 text-[#ED1C24] rounded-md text-xs font-black uppercase tracking-widest border border-red-100">
                    {{ $event->event_date ? $event->event_date->translatedFormat('d F Y') : 'Tanggal TBA' }}
                </span>
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                    {{ $event->photos->count() }} Dokumentasi Foto
                </span>
            </div>
            <h1 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight mb-4">{{ $event->title }}</h1>
            @if($event->description)
                <p class="text-gray-600 leading-relaxed">{{ $event->description }}</p>
            @endif
        </div>

        {{-- Masonry Gallery Grid --}}
        @if($event->photos->count() > 0)
            <div class="columns-1 sm:columns-2 md:columns-3 lg:columns-4 gap-4 space-y-4">
                @foreach($event->photos as $index => $photo)
                    <div class="relative group overflow-hidden rounded-xl cursor-pointer break-inside-avoid border border-gray-200 bg-white"
                         {{-- Set activeIndex sesuai urutan foto saat di-klik --}}
                         @click="activeIndex = {{ $index }}; lightboxOpen = true">
                        
                        {{-- Gambar --}}
                        <img src="{{ Storage::url($photo->image) }}" alt="{{ $photo->caption ?? 'Dokumentasi KLHN' }}" class="w-full h-auto object-cover transform transition-transform duration-700 group-hover:scale-105" loading="lazy">
                        
                        {{-- Overlay & Caption Hover --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex flex-col justify-end p-4">
                            {{-- Ikon Expand --}}
                            <div class="absolute top-4 right-4 bg-white/20 backdrop-blur-sm p-2 rounded-lg text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"/></svg>
                            </div>
                            
                            @if($photo->caption)
                                <p class="text-white text-sm font-medium line-clamp-3">
                                    {{ $photo->caption }}
                                </p>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            {{-- Empty State jika belum ada foto --}}
            <div class="flex flex-col items-center justify-center py-24 text-center bg-white rounded-2xl border border-gray-200">
                <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                <h3 class="text-lg font-bold text-gray-900 uppercase">Belum ada dokumentasi</h3>
                <p class="text-gray-500 mt-2">Foto-foto untuk event ini akan segera diunggah.</p>
            </div>
        @endif

    </div>

    {{-- MODAL LIGHTBOX FULL GALLERY (ALPINE.JS) --}}
    <div x-show="lightboxOpen" style="display: none;" class="fixed inset-0 z-[100] flex flex-col items-center justify-center bg-black/95 backdrop-blur-sm" x-transition.opacity.duration.300ms>
        
        {{-- Area Klik untuk Menutup (Bagian Atas & Kiri Kanan Gambar) --}}
        <div class="absolute inset-0" @click="lightboxOpen = false"></div>
        
        {{-- Tombol Close (Pojok Kanan Atas) --}}
        <button @click="lightboxOpen = false" class="absolute z-10 top-4 right-4 sm:top-6 sm:right-6 text-white/70 hover:text-white transition-colors bg-white/10 hover:bg-white/20 p-2 rounded-full backdrop-blur-md">
            <svg class="w-6 h-6 sm:w-8 sm:h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
        </button>

        {{-- AREA UTAMA: Main Image & Navigasi Panah --}}
        <div class="relative z-10 flex items-center justify-center w-full flex-1 min-h-0 px-4 md:px-16 pt-12 pb-4 pointer-events-none">
            
            {{-- Tombol Prev (Kiri) --}}
            <button @click.stop="prev()" class="absolute left-2 md:left-6 p-2 md:p-3 text-white/50 hover:text-white bg-black/20 hover:bg-black/60 rounded-full backdrop-blur-md transition-all pointer-events-auto">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            </button>

            {{-- Gambar Utama --}}
            <div class="flex flex-col items-center max-w-5xl max-h-full">
                <img :src="photos[activeIndex].url" alt="Gallery Lightbox" class="max-w-full max-h-[60vh] md:max-h-[70vh] object-contain rounded-lg shadow-2xl pointer-events-auto" @click.stop>
                
                {{-- Caption --}}
                <div x-show="photos[activeIndex].caption" class="mt-4 text-center pointer-events-auto">
                    <p class="text-white/90 text-sm md:text-base font-medium max-w-3xl px-4" x-text="photos[activeIndex].caption"></p>
                </div>
            </div>

            {{-- Tombol Next (Kanan) --}}
            <button @click.stop="next()" class="absolute right-2 md:right-6 p-2 md:p-3 text-white/50 hover:text-white bg-black/20 hover:bg-black/60 rounded-full backdrop-blur-md transition-all pointer-events-auto">
                <svg class="w-8 h-8 md:w-10 md:h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
            </button>

        </div>

        {{-- AREA BAWAH: List Thumbnail Slider --}}
        <div class="relative z-10 w-full bg-black/50 border-t border-white/10 p-4 pb-6 mt-auto">
            <div class="max-w-7xl mx-auto flex gap-3 overflow-x-auto snap-x py-2 custom-scrollbar justify-start md:justify-center">
                
                <template x-for="(photo, index) in photos" :key="index">
                    <button @click.stop="activeIndex = index" 
                            class="relative flex-shrink-0 w-20 h-14 md:w-24 md:h-16 rounded-md overflow-hidden transition-all duration-300 snap-center focus:outline-none"
                            :class="activeIndex === index ? 'ring-2 ring-[#ED1C24] ring-offset-2 ring-offset-black opacity-100 scale-105' : 'opacity-40 hover:opacity-100'">
                        <img :src="photo.url" class="w-full h-full object-cover">
                        {{-- Overlay merah transparan saat aktif (Opsional, dihapus jika mengganggu) --}}
                        <div x-show="activeIndex === index" class="absolute inset-0 bg-black/10"></div>
                    </button>
                </template>

            </div>
            
            {{-- Counter Foto --}}
            <div class="text-center mt-2">
                <span class="text-white/50 text-xs font-bold tracking-widest uppercase">
                    Foto <span x-text="activeIndex + 1"></span> dari <span x-text="photos.length"></span>
                </span>
            </div>
        </div>

    </div>

    {{-- Style CSS kecil untuk menyembunyikan scrollbar bawaan di area Thumbnail agar rapi --}}
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            height: 6px;
        }
        .custom-scrollbar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 10px;
        }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: rgba(255, 255, 255, 0.4);
        }
    </style>
</div>