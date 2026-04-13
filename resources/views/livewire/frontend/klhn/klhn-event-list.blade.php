<div class="bg-[#F3F4F6] min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Galeri KLHN</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Kumpulan dokumentasi event Kontes Layanan Honda Nasional.
            </p>
        </div>

        {{-- Grid Event --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($events as $event)
                <a href="{{ route('klhn.detail', $event->id) }}" wire:navigate class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-red-200 hover:shadow-lg hover:-translate-y-1 transition-all duration-300 flex flex-col">
                    
                    {{-- Cover Image --}}
                    <div class="aspect-video bg-gray-100 overflow-hidden relative">
                        @if($event->photos->isNotEmpty() && $event->photos->first()->image)
                            <img src="{{ Storage::url($event->photos->first()->image) }}" alt="{{ $event->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                <span class="text-xs font-medium uppercase">Belum ada foto</span>
                            </div>
                        @endif
                        <div class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></div>
                    </div>

                    {{-- Event Info --}}
                    <div class="p-6 flex flex-col flex-1">
                        <div class="flex items-center justify-between mb-3">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-red-50 text-[#ED1C24] border border-red-100">
                                {{ $event->event_date ? $event->event_date->translatedFormat('d F Y') : 'Tanggal TBA' }}
                            </span>
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">
                                {{ $event->photos->count() }} Foto
                            </span>
                        </div>
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors line-clamp-2 mb-2">
                            {{ $event->title }}
                        </h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mt-auto">
                            {{ $event->description ?? 'Tidak ada deskripsi event.' }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    <p class="text-gray-500 font-medium">Belum ada dokumentasi event KLHN.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $events->links() }}
        </div>
        
    </div>
</div>