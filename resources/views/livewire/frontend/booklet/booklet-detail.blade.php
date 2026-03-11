<div class="py-12 bg-white min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">
        
        {{-- Breadcrumb Simple --}}
        <div class="mb-6 text-sm text-gray-500 font-medium flex items-center gap-2">
            <a href="{{ route('home') }}" class="hover:text-[#ED1C24] transition-colors">Beranda</a> 
            <span>/</span>
            <a href="{{ route('booklets.index') }}" class="hover:text-[#ED1C24] transition-colors">Booklet</a> 
            <span>/</span>
            <span class="text-gray-900 truncate">{{ $booklet->title }}</span>
        </div>

        {{-- Main Content --}}
        <article class="bg-white">
            
            <div class="flex flex-col md:flex-row gap-8 mb-10">
                {{-- Kiri: Cover Image --}}
                <div class="w-full md:w-1/3 flex-shrink-0">
                    <div class="rounded-2xl overflow-hidden shadow-xl border border-gray-100 bg-gray-50 aspect-[3/4] relative">
                        @if($booklet->cover_image)
                            <img src="{{ asset('storage/' . $booklet->cover_image) }}" alt="{{ $booklet->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Kanan: Detail Info & Action --}}
                <div class="flex-1 flex flex-col justify-center">
                    <div class="inline-flex mb-3 px-3 py-1 bg-red-50 text-[#ED1C24] text-[10px] font-black uppercase tracking-widest rounded-lg self-start">
                        Dokumen PDF
                    </div>
                    
                    <h1 class="text-3xl md:text-4xl font-extrabold text-gray-900 mb-4 leading-tight">
                        {{ $booklet->title }}
                    </h1>
                    
                    <div class="text-sm text-gray-500 mb-6 font-medium">
                        Dipublikasikan pada {{ $booklet->created_at->format('d F Y') }}
                    </div>

                    @if($booklet->description)
                        <div class="text-gray-600 leading-relaxed mb-8">
                            {!! nl2br(e($booklet->description)) !!}
                        </div>
                    @endif

                    {{-- Tombol Aksi --}}
                    <div class="flex flex-wrap gap-4 mt-auto">
                        <a href="{{ asset('storage/' . $booklet->pdf_file) }}" target="_blank" class="inline-flex items-center justify-center px-8 py-3.5 bg-[#ED1C24] hover:bg-red-700 text-white font-bold rounded-xl shadow-lg transition-transform transform active:scale-95 gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                            Buka / Baca PDF
                        </a>
                        <a href="{{ asset('storage/' . $booklet->pdf_file) }}" download class="inline-flex items-center justify-center px-8 py-3.5 bg-gray-100 hover:bg-gray-200 text-gray-800 font-bold rounded-xl border border-gray-200 transition-colors gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download
                        </a>
                    </div>
                </div>
            </div>

            {{-- Opsional: Iframe Preview PDF langsung di web --}}
            <div class="mt-12 border-t border-gray-100 pt-12 hidden md:block">
                <h3 class="text-xl font-bold text-gray-900 mb-6">Preview Dokumen</h3>
                <div class="w-full h-[800px] bg-gray-100 rounded-2xl overflow-hidden shadow-inner border border-gray-200">
                    <iframe src="{{ asset('storage/' . $booklet->pdf_file) }}#toolbar=0" class="w-full h-full border-none"></iframe>
                </div>
            </div>

        </article>
    </div>
</div>