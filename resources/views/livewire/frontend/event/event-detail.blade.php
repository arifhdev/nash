<div class="py-12 bg-white min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        
        {{-- Breadcrumb Simple --}}
        <div class="mb-6 text-sm text-gray-500">
            <a href="{{ route('home') }}" class="hover:text-red-600">Beranda</a> / 
            <a href="{{ route('events.index') }}" class="hover:text-red-600">Event</a> / 
            <span class="text-gray-900">{{ $this->event->title }}</span>
        </div>

        {{-- Main Content --}}
        <article>
            {{-- Image Banner --}}
            <div class="rounded-3xl overflow-hidden shadow-lg mb-8 aspect-video relative">
                <img src="{{ Storage::url($event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-full object-cover">
            </div>

            {{-- Header Title & Meta --}}
            <header class="mb-8 border-b pb-8">
                <h1 class="text-3xl md:text-5xl font-bold text-gray-900 mb-6 leading-tight">
                    {{ $event->title }}
                </h1>

                <div class="flex flex-wrap gap-4 md:gap-8">
                    {{-- Date --}}
                    <div class="flex items-center text-gray-700">
                        <div class="bg-red-50 p-2 rounded-lg mr-3 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Waktu</p>
                            <p class="font-medium">{{ $event->start_date->format('d M Y, H:i') }} WIB</p>
                        </div>
                    </div>

                    {{-- Location --}}
                    <div class="flex items-center text-gray-700">
                        <div class="bg-red-50 p-2 rounded-lg mr-3 text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500 font-semibold uppercase tracking-wider">Lokasi</p>
                            <p class="font-medium">{{ $event->location ?? 'Online via Zoom' }}</p>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Body Content --}}
            {{-- UPDATED: Menggunakan Arbitrary Variants untuk memaksa styling tanpa Plugin Typography --}}
            <div class="text-gray-600 leading-relaxed max-w-none 
                [&>p]:mb-6 
                [&>p]:leading-7
                [&>h2]:text-2xl [&>h2]:font-bold [&>h2]:mt-8 [&>h2]:mb-4 [&>h2]:text-gray-900
                [&>h3]:text-xl [&>h3]:font-bold [&>h3]:mt-6 [&>h3]:mb-3 [&>h3]:text-gray-900
                [&>ul]:list-disc [&>ul]:ml-6 [&>ul]:mb-6
                [&>ol]:list-decimal [&>ol]:ml-6 [&>ol]:mb-6
                [&>li]:mb-2
                [&>strong]:font-bold [&>strong]:text-gray-800
                [&>a]:text-red-600 [&>a]:underline">
                
                {!! $event->content !!}
                
            </div>

        </article>
    </div>
</div>