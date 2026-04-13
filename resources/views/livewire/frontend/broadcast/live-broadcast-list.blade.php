<div class="bg-[#F3F4F6] min-h-screen py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Tayangan Live</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Saksikan tayangan langsung dan rekaman event Honda.
            </p>
        </div>
       

        {{-- Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($broadcasts as $broadcast)
                <a href="{{ route('broadcast.detail', $broadcast->id) }}" wire:navigate class="group bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-red-200 hover:shadow-xl hover:-translate-y-1 transition-all duration-300 flex flex-col relative">
                    
                    {{-- Badge Status --}}
                    <div class="absolute top-4 left-4 z-10">
                        @if($broadcast->status === 'live')
                            <span class="flex items-center gap-1.5 px-3 py-1 bg-[#ED1C24] text-white text-[10px] font-black uppercase tracking-widest rounded-md shadow-lg animate-pulse">
                                <span class="w-2 h-2 rounded-full bg-white"></span> LIVE NOW
                            </span>
                        @elseif($broadcast->status === 'upcoming')
                            <span class="px-3 py-1 bg-amber-500 text-white text-[10px] font-black uppercase tracking-widest rounded-md shadow-lg">
                                UPCOMING
                            </span>
                        @else
                            <span class="px-3 py-1 bg-gray-800 text-white text-[10px] font-black uppercase tracking-widest rounded-md shadow-lg">
                                ENDED (VOD)
                            </span>
                        @endif
                    </div>

                    {{-- Auto Thumbnail dari YouTube --}}
<div class="aspect-video bg-gray-900 overflow-hidden relative">
    @if($broadcast->youtube_id)
        
        {{-- Kita ubah jadi hqdefault.jpg karena paling stabil untuk video Live/Upcoming --}}
        <img src="https://img.youtube.com/vi/{{ $broadcast->youtube_id }}/hqdefault.jpg" 
             alt="{{ $broadcast->title }}" 
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 opacity-90 group-hover:opacity-100">
        
        {{-- Icon Play --}}
        <div class="absolute inset-0 flex items-center justify-center">
            <div class="w-14 h-14 bg-[#ED1C24]/90 backdrop-blur-sm rounded-full flex items-center justify-center text-white transform group-hover:scale-110 transition-transform shadow-lg">
                <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
        </div>
    @else
        <div class="w-full h-full flex flex-col items-center justify-center text-gray-500">
            <svg class="w-10 h-10 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
            <span class="text-xs font-bold uppercase tracking-widest text-red-500">URL TIDAK VALID</span>
        </div>
    @endif
</div>

                    {{-- Info --}}
                    <div class="p-6 flex flex-col flex-1">
                        @if($broadcast->scheduled_at)
                            <p class="text-xs font-bold text-gray-400 mb-2 uppercase tracking-wide">
                                Jadwal: {{ $broadcast->scheduled_at->translatedFormat('d F Y, H:i') }} WIB
                            </p>
                        @endif
                        <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors leading-tight mb-3">
                            {{ $broadcast->title }}
                        </h3>
                        <p class="text-sm text-gray-500 line-clamp-2 mt-auto">
                            {{ $broadcast->description ?? 'Tidak ada deskripsi.' }}
                        </p>
                    </div>
                </a>
            @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-gray-200">
                    <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    <p class="text-gray-500 font-medium">Belum ada tayangan live.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-10">
            {{ $broadcasts->links() }}
        </div>
    </div>
</div>