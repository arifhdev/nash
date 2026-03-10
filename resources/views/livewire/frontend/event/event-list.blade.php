<div class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Agenda Kegiatan</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Temukan berbagai workshop, seminar, dan pelatihan terbaru dari Akademi Satu Hati.
            </p>
        </div>

        {{-- Grid Event --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @foreach($events as $event)
                <a href="{{ route('event.show', $event->slug) }}" wire:navigate class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition group h-full flex flex-col">
                    <div class="relative aspect-video overflow-hidden">
                        <img src="{{ Storage::url($event->thumbnail) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        {{-- Badge Tanggal --}}
                        <div class="absolute top-4 left-4 bg-white/90 backdrop-blur px-3 py-1 rounded-lg text-xs font-bold shadow-sm text-gray-800">
                            {{ $event->start_date->format('d M Y') }}
                        </div>
                    </div>
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-2 group-hover:text-red-600 transition line-clamp-2">
                            {{ $event->title }}
                        </h3>
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            {{ $event->location ?? 'Online' }}
                        </div>
                        <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-medium text-red-600">Lihat Detail &rarr;</span>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $events->links() }}
        </div>
    </div>
</div>