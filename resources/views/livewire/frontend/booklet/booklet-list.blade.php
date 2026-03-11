<div class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">Katalog Booklet</h1>
            <p class="text-gray-500 max-w-2xl mx-auto">
                Kumpulan materi bacaan digital, panduan, dan modul pembelajaran dari Akademi Satu Hati.
            </p>
        </div>

        {{-- Grid Booklet --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
            @forelse($booklets as $booklet)
                <a href="{{ route('booklet.show', $booklet->slug) }}" wire:navigate class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all duration-300 group h-full flex flex-col hover:-translate-y-1 border border-gray-100">
                    
                    {{-- Cover Image --}}
                    <div class="relative aspect-[4/3] bg-gray-200 overflow-hidden">
                        @if($booklet->cover_image)
                            <img src="{{ asset('storage/' . $booklet->cover_image) }}" alt="{{ $booklet->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                        @endif
                        
                        {{-- Tipe Badge --}}
                        <div class="absolute top-4 right-4 bg-[#ED1C24] text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-sm">
                            PDF
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#ED1C24] transition-colors line-clamp-2">
                            {{ $booklet->title }}
                        </h3>
                        <p class="text-gray-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                            {{ $booklet->description ?? 'Tidak ada deskripsi untuk booklet ini.' }}
                        </p>
                        
                        <div class="mt-auto pt-4 border-t border-gray-100 flex justify-between items-center">
                            <span class="text-sm font-bold text-[#ED1C24] flex items-center gap-1">
                                Baca Sekarang 
                                <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                            </span>
                            <span class="text-xs text-gray-400 font-medium">{{ $booklet->created_at->format('d M Y') }}</span>
                        </div>
                    </div>
                </a>
            @empty
                <div class="col-span-full py-16 text-center text-gray-500 bg-white rounded-2xl border border-gray-100">
                    <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    <p class="text-lg font-medium">Belum ada booklet yang dipublikasikan.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-12">
            {{ $booklets->links() }}
        </div>
    </div>
</div>