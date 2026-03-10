<section class="py-12 px-4 md:px-8">
    <div class="container mx-auto max-w-7xl">
        
        {{-- Container Utama --}}
        <div class="bg-gray-100 rounded-[2.5rem] flex flex-col lg:grid lg:grid-cols-12 overflow-hidden relative lg:min-h-[450px]">
            
            {{-- 1. Bagian Kiri: Text & Button --}}
            <div class="p-8 md:p-12 lg:col-span-5 flex flex-col justify-center space-y-6 z-10 relative order-1">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 leading-tight">
                    Event Akademi <br> Satu Hati
                </h2>
                <p class="text-gray-500 text-base leading-relaxed">
                    Ikuti berbagai kegiatan seru dan bermanfaat untuk meningkatkan skill serta jaringan Anda di dunia industri.
                </p>
                <div>
                    {{-- Link ke Index Event --}}
                    <a href="{{ route('events.index') }}" wire:navigate class="inline-block bg-[#ED1C24] hover:bg-red-700 text-white text-sm font-semibold px-8 py-3 rounded-md transition shadow-sm">
                        Lihat Semua Event
                    </a>
                </div>
            </div>

            {{-- 2. Bagian Kanan: Image Grid Wrapper --}}
            <div class="lg:col-span-7 grid grid-cols-2 gap-4 lg:gap-6 relative p-4 lg:p-0 lg:pr-12 order-2 pb-8 lg:pb-0">
                
                @php
                    $renderEventCard = function($event) {
                        if(!$event) return '<div class="bg-gray-200 rounded-2xl aspect-video w-full animate-pulse"></div>';
                        
                        $url = route('event.show', $event->slug);
                        $img = Storage::url($event->thumbnail);
                        
                        return <<<HTML
                        <a href="{$url}" wire:navigate class="block bg-white rounded-2xl overflow-hidden shadow-sm aspect-video w-full group relative hover:ring-2 hover:ring-red-500 transition duration-300">
                            <img src="{$img}" alt="{$event->title}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent flex items-end p-4">
                                <span class="text-white font-medium text-sm md:text-base line-clamp-2 leading-snug">{$event->title}</span>
                            </div>
                        </a>
                        HTML;
                    };

                    $slideEvents = $events->slice(2, 8);
                    
                    if ($slideEvents->isEmpty()) {
                        $slideEvents = collect(array_fill(0, 4, null)); 
                    }
                @endphp

                {{-- Kolom A (Event urutan 1 & 2) - Statis --}}
                <div class="flex flex-col gap-4 lg:gap-6 lg:py-12 justify-center">
                    {!! $renderEventCard($events->get(0)) !!}
                    {!! $renderEventCard($events->get(1)) !!}
                </div>

                {{-- Kolom B (Event urutan 3 sampai 10) - INFINITE SCROLL / MARQUEE --}}
                {{-- Perubahan di sini: lg:h-[calc(100%+2rem)] untuk mentok ke bawah, rounded-b-none, dan gradasi bawah dihilangkan --}}
                <div class="relative h-[400px] lg:h-[calc(100%+2rem)] overflow-hidden rounded-t-2xl lg:-mt-8" 
                     style="-webkit-mask-image: linear-gradient(to bottom, transparent, black 15%, black 100%); mask-image: linear-gradient(to bottom, transparent, black 15%, black 100%);">
                    
                    <div class="absolute inset-x-0 top-0 flex flex-col animate-marquee-y hover:pause-animation cursor-pointer">
                        
                        {{-- Set 1 --}}
                        <div class="flex flex-col gap-4 lg:gap-6 mb-4 lg:mb-6">
                            @foreach($slideEvents as $event)
                                {!! $renderEventCard($event) !!}
                            @endforeach
                        </div>
                        
                        {{-- Set 2 (Duplikat) --}}
                        <div class="flex flex-col gap-4 lg:gap-6 mb-4 lg:mb-6">
                            @foreach($slideEvents as $event)
                                {!! $renderEventCard($event) !!}
                            @endforeach
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        @keyframes marqueeY {
            0% { transform: translateY(0); }
            100% { transform: translateY(-50%); } 
        }
        
        .animate-marquee-y {
            animation: marqueeY 25s linear infinite;
        }

        .hover\:pause-animation:hover {
            animation-play-state: paused;
        }
    </style>
</section>