<div class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">
        
        {{-- Header --}}
        <div class="text-center mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4 tracking-tight">
                Katalog <span class="text-[#ED1C24]">Booklet</span>
            </h1>
            <p class="text-gray-500 max-w-2xl mx-auto text-base md:text-lg">
                Kumpulan materi bacaan digital, panduan, dan modul pembelajaran eksklusif dari Akademi Satu Hati.
            </p>
        </div>

        {{-- Grid Booklet --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" wire:loading.class="opacity-50 transition-opacity">
            @forelse($booklets as $booklet)
                <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300 group h-full flex flex-col hover:-translate-y-1 border border-gray-100">
                    
                    {{-- Cover Image (Added Link) --}}
                    <a href="{{ route('booklet.show', $booklet->slug) }}" wire:navigate class="relative aspect-[4/3] bg-gray-100 overflow-hidden block">
                        @if($booklet->cover_image)
                            <img src="{{ Storage::url($booklet->cover_image) }}" 
                                 alt="{{ $booklet->title }}" 
                                 class="w-full h-full object-cover group-hover:scale-105 transition duration-700 ease-in-out">
                        @else
                            <div class="w-full h-full flex flex-col items-center justify-center bg-gray-50 text-gray-400">
                                <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-[10px] font-bold uppercase tracking-widest mt-2 opacity-40">No Preview</span>
                            </div>
                        @endif
                        
                        {{-- Tipe Badge --}}
                        <div class="absolute top-4 right-4 bg-[#ED1C24] text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider shadow-md z-10">
                            PDF
                        </div>
                    </a>

                    {{-- Body Content --}}
                    <div class="p-6 flex-1 flex flex-col">
                        <div class="flex-1">
                            {{-- Judul (Added Link) --}}
                            <a href="{{ route('booklet.show', $booklet->slug) }}" wire:navigate class="block">
                                <h3 class="text-xl font-bold text-gray-900 mb-3 group-hover:text-[#ED1C24] transition-colors line-clamp-2 leading-snug">
                                    {{ $booklet->title }}
                                </h3>
                            </a>
                            <p class="text-gray-500 text-sm mb-6 line-clamp-3 leading-relaxed">
                                {{ $booklet->description ?? 'Pelajari lebih lanjut mengenai materi ini melalui dokumen panduan resmi Akademi Satu Hati.' }}
                            </p>
                        </div>
                        
                        <div class="mt-auto pt-5 border-t border-gray-50 flex justify-between items-center">
                            {{-- Action Button --}}
                            <a href="{{ route('booklet.show', $booklet->slug) }}" 
                               wire:navigate
                               class="text-sm font-bold text-[#ED1C24] hover:text-red-700 flex items-center gap-1.5 transition-colors group/btn">
                                Baca Materi 
                                <svg class="w-4 h-4 transform group-hover/btn:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                            <div class="flex items-center gap-1 text-gray-400">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                <span class="text-[10px] font-bold uppercase tracking-tighter">{{ $booklet->created_at->translatedFormat('d M Y') }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border-2 border-dashed border-gray-100">
                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900">Belum Ada Booklet</h3>
                    <p class="text-gray-500 mt-1 max-w-xs mx-auto">Kami sedang mempersiapkan materi bacaan menarik untuk Anda. Silakan cek kembali nanti.</p>
                </div>
            @endforelse
        </div>

        {{-- Pagination --}}
        <div class="mt-16">
            {{ $booklets->links() }}
        </div>
    </div>
</div>