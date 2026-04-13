{{-- Menggunakan Alpine.js untuk fitur Fullscreen PDF & Modal Video --}}
<div x-data="{ 
        isFullscreen: false,
        isModalOpen: false,
        activeVideoUrl: '',
        
        toggleFullscreen() {
            if (!document.fullscreenElement) {
                $refs.pdfContainer.requestFullscreen().catch(err => {
                    console.log(`Error attempting to enable fullscreen: ${err.message}`);
                });
                this.isFullscreen = true;
            } else {
                document.exitFullscreen();
                this.isFullscreen = false;
            }
        },
        
        openModal(url) {
            this.activeVideoUrl = url;
            this.isModalOpen = true;
            document.body.style.overflow = 'hidden'; // Mencegah background bisa di-scroll saat modal terbuka
        },
        
        closeModal() {
            this.isModalOpen = false;
            setTimeout(() => {
                this.activeVideoUrl = ''; // Hapus URL setelah animasi selesai agar video stop putar
            }, 300);
            document.body.style.overflow = 'auto';
        }
    }" 
    @fullscreenchange.window="isFullscreen = !!document.fullscreenElement"
    @keydown.escape.window="closeModal()"
    class="py-6 bg-gray-50 min-h-screen flex flex-col relative">
    
    <div class="container mx-auto px-4 w-full flex-grow flex flex-col h-full">
        
        {{-- Header & Breadcrumb (Akan sembunyi saat Fullscreen) --}}
        <div x-show="!isFullscreen" class="flex flex-col md:flex-row md:items-end justify-between gap-4 mb-6 transition-all duration-300">
            <div>
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-widest flex items-center gap-2 mb-2">
                    <a href="{{ route('home') }}" class="hover:text-[#ED1C24] transition-colors">Beranda</a> 
                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/></svg>
                    <a href="{{ route('booklets.index') }}" class="hover:text-[#ED1C24] transition-colors">Booklet</a> 
                </div>
                <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 leading-tight tracking-tight">
                    {{ $booklet->title }}
                </h1>
            </div>

            {{-- Tombol Kembali --}}
            <a href="{{ route('booklets.index') }}" wire:navigate class="inline-flex items-center justify-center px-5 py-2.5 bg-white border border-gray-200 text-gray-700 hover:bg-gray-100 hover:text-[#ED1C24] font-black rounded-xl shadow-sm transition-all text-xs uppercase tracking-widest gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali
            </a>
        </div>

        {{-- Full PDF Viewer Wrapper --}}
        <div x-ref="pdfContainer" 
             class="flex-grow w-full bg-gray-800 rounded-2xl shadow-2xl border border-gray-200 overflow-hidden relative group" 
             style="min-height: 80vh;">
            
            {{-- Tombol Fullscreen Mengambang (Floating Button) --}}
            <button 
                @click="toggleFullscreen()"
                class="absolute bottom-8 right-8 z-40 p-4 bg-[#ED1C24] hover:bg-red-700 text-white rounded-full shadow-[0_10px_25px_rgba(237,28,36,0.5)] transition-all hover:scale-110 flex items-center justify-center border-2 border-white/20 active:scale-95"
                :title="isFullscreen ? 'Keluar Fullscreen' : 'Layar Penuh'"
            >
                <svg x-show="!isFullscreen" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4"></path></svg>
                <svg x-show="isFullscreen" x-cloak class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 15L3 21m0 0l6-6M3 21h6m-6 0v-6m12-6l6-6m0 0l-6 6m6-6h-6m6 0v6M21 21l-6-6m0 0l6-6m-6 6h6m-6 0v-6M3 3l6 6m0 0L3 9m6-6H3m0 0v6"></path></svg>
            </button>

            {{-- Overlay Loading --}}
            <div x-data="{ loading: true }" class="absolute inset-0 bg-gray-800 flex items-center justify-center z-0">
                <div x-show="loading" class="flex flex-col items-center gap-4">
                    <svg class="animate-spin h-10 w-10 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-gray-400 text-xs font-bold uppercase tracking-widest">Memuat Dokumen...</span>
                </div>

                <div class="w-full h-full px-4 md:px-12 lg:px-32 py-4 flex justify-center">
                    <iframe 
                        src="{{ Storage::url($booklet->pdf_file) }}#toolbar=0&navpanes=0&view=FitH" 
                        class="w-full h-full border-none z-10 bg-white shadow-2xl"
                        title="{{ $booklet->title }}"
                        @load="loading = false"
                        oncontextmenu="return false;"
                    ></iframe>
                </div>
            </div>
        </div>

        {{-- Section Video YouTube Terkait --}}
        @if(!empty($booklet->youtube_videos))
            <div x-show="!isFullscreen" class="mt-10 transition-all duration-300">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-2 h-8 bg-[#ED1C24] rounded-full"></div>
                    <h2 class="text-xl md:text-2xl font-extrabold text-gray-900 tracking-tight">Video Terkait</h2>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($booklet->youtube_videos as $video)
                        @php
                            // Mengambil Video ID langsung via PHP untuk membuat Thumbnail Image
                            preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $video['url'], $match);
                            $videoId = $match[1] ?? null;
                        @endphp
                        
                        @if($videoId)
                            @php
                                $thumbnailUrl = "https://img.youtube.com/vi/{$videoId}/hqdefault.jpg";
                                $embedUrlWithAutoplay = "https://www.youtube.com/embed/{$videoId}?autoplay=1&rel=0";
                            @endphp

                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-lg transition-all duration-300 group cursor-pointer"
                                 @click="openModal('{{ $embedUrlWithAutoplay }}')">
                                
                                {{-- Thumbnail Wrapper (16:9) --}}
                                <div class="relative w-full bg-gray-900 overflow-hidden" style="padding-bottom: 56.25%;">
                                    <img src="{{ $thumbnailUrl }}" alt="{{ $video['title'] ?? 'Video' }}" class="absolute top-0 left-0 w-full h-full object-cover opacity-80 group-hover:opacity-100 group-hover:scale-105 transition-all duration-500">
                                    
                                    {{-- Play Button Overlay --}}
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-14 h-14 bg-black/60 backdrop-blur-sm rounded-full flex items-center justify-center border-2 border-white/30 group-hover:bg-[#ED1C24] group-hover:border-[#ED1C24] transition-colors duration-300 shadow-xl">
                                            <svg class="w-6 h-6 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Judul Video --}}
                                @if(!empty($video['title']))
                                    <div class="p-4 border-t border-gray-50">
                                        <h4 class="font-bold text-gray-800 text-sm md:text-base line-clamp-2 group-hover:text-[#ED1C24] transition-colors">
                                            {{ $video['title'] }}
                                        </h4>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Footer Detail --}}
        <div x-show="!isFullscreen" class="mt-10 pt-6 border-t border-gray-200 flex flex-col md:flex-row justify-between items-center text-gray-400 gap-4 transition-all duration-300">
            <p class="text-[10px] font-bold uppercase tracking-widest">
                Dipublikasikan: {{ $booklet->created_at->translatedFormat('d F Y') }}
            </p>
            <p class="text-[10px] font-bold uppercase tracking-widest">
                &copy; {{ date('Y') }} Akademi Satu Hati
            </p>
        </div>

    </div>

    {{-- ================= MODAL VIDEO YOUTUBE ================= --}}
    <div x-show="isModalOpen" 
         style="display: none;" 
         class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6 md:p-12">
        
        {{-- Background Gelap (Backdrop) --}}
        <div x-show="isModalOpen" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="absolute inset-0 bg-black/90 backdrop-blur-sm"
             @click="closeModal()">
        </div>

        {{-- Kontainer Iframe Video --}}
        <div x-show="isModalOpen"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 scale-95 translate-y-8"
             x-transition:enter-end="opacity-100 scale-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200 transform"
             x-transition:leave-start="opacity-100 scale-100 translate-y-0"
             x-transition:leave-end="opacity-0 scale-95 translate-y-8"
             class="relative w-full max-w-5xl mx-auto bg-black rounded-2xl shadow-2xl overflow-hidden border border-gray-800 z-10">
            
            {{-- Tombol Close X di sudut kanan atas video --}}
            <button @click="closeModal()" class="absolute top-4 right-4 z-20 w-10 h-10 bg-black/50 hover:bg-[#ED1C24] rounded-full flex items-center justify-center text-white transition-colors backdrop-blur-md border border-white/20">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>

            {{-- Rasio Aspek 16:9 untuk Iframe --}}
            <div class="relative w-full" style="padding-bottom: 56.25%;">
                {{-- Gunakan <template x-if> agar Iframe HANCUR saat ditutup, memastikan suara video berhenti total --}}
                <template x-if="activeVideoUrl">
                    <iframe 
                        class="absolute top-0 left-0 w-full h-full"
                        :src="activeVideoUrl" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                        allowfullscreen>
                    </iframe>
                </template>
            </div>
        </div>
    </div>
    {{-- ================= END MODAL VIDEO ================= --}}

</div>