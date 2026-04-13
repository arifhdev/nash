<div class="bg-[#F3F4F6] min-h-screen py-10">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Tombol Kembali --}}
        <a href="{{ route('broadcast.index') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-[#ED1C24] transition-colors mb-6 uppercase tracking-wider">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Daftar Tayangan
        </a>

        {{-- Video Player Container --}}
        <div class="bg-black rounded-2xl overflow-hidden aspect-video shadow-2xl mb-8 border border-gray-800">
            @if($broadcast->youtube_id)
                {{-- Iframe YouTube Responsif --}}
                <iframe 
                    class="w-full h-full"
                    {{-- Autoplay otomatis menyala jika statusnya LIVE --}}
                    src="https://www.youtube.com/embed/{{ $broadcast->youtube_id }}?autoplay={{ $broadcast->status === 'live' ? '1' : '0' }}&rel=0" 
                    title="{{ $broadcast->title }}" 
                    frameborder="0" 
                    allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                    referrerpolicy="strict-origin-when-cross-origin"
                    allowfullscreen>
                </iframe>
            @else
                <div class="w-full h-full flex flex-col items-center justify-center text-gray-500">
                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    <p class="font-medium">Video tidak dapat dimuat. URL tidak valid.</p>
                </div>
            @endif
        </div>

        {{-- Info Event --}}
        <div class="bg-white rounded-2xl p-8 border border-gray-200 shadow-sm">
            <div class="flex items-center gap-3 mb-4">
                @if($broadcast->status === 'live')
                    <span class="flex items-center gap-2 px-3 py-1 bg-[#ED1C24] text-white text-xs font-black uppercase tracking-widest rounded-md animate-pulse">
                        <span class="w-2 h-2 rounded-full bg-white"></span> SEDANG LIVE
                    </span>
                @elseif($broadcast->status === 'upcoming')
                    <span class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-black uppercase tracking-widest rounded-md border border-amber-200">
                        Upcoming Event
                    </span>
                @else
                    <span class="px-3 py-1 bg-gray-100 text-gray-600 text-xs font-black uppercase tracking-widest rounded-md border border-gray-200">
                        Rekaman (Berakhir)
                    </span>
                @endif
                
                @if($broadcast->scheduled_at)
                    <span class="text-sm font-bold text-gray-400">
                        {{ $broadcast->scheduled_at->translatedFormat('d F Y • H:i') }} WIB
                    </span>
                @endif
            </div>

            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 uppercase tracking-tight mb-6">
                {{ $broadcast->title }}
            </h1>

            @if($broadcast->description)
                <div class="prose max-w-none text-gray-600">
                    {!! nl2br(e($broadcast->description)) !!}
                </div>
            @endif
        </div>

    </div>
</div>