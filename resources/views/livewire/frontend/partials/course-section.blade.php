<section class="py-16 lg:py-24 bg-white" id="courses">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Jelajahi Kelas & <span class="text-[#ED1C24]">Program Pelatihan</span>
            </h2>
            <p class="text-gray-500 text-lg">
                Temukan beragam modul pembelajaran yang dirancang untuk mempertajam keahlian teknis dan memperluas wawasan profesional Anda
            </p>
        </div>

        <div class="flex flex-wrap justify-center gap-3 mb-12">
            {{-- Tombol "Semua" --}}
            <button wire:click="setCategory(null)"
                class="px-6 py-2 rounded-full text-sm font-medium border transition shadow-sm
                {{ is_null($selectedCategoryId) 
                    ? 'bg-[#ED1C24] text-white border-[#ED1C24]' 
                    : 'bg-white text-gray-600 border-gray-200 hover:border-[#ED1C24] hover:text-[#ED1C24]' 
                }}">
                Semua
            </button>
            
            {{-- Loop Tombol Kategori dari Database --}}
            @foreach($categories as $category)
                <button wire:click="setCategory({{ $category->id }})"
                    class="px-6 py-2 rounded-full text-sm font-medium border transition shadow-sm
                    {{ $selectedCategoryId === $category->id 
                        ? 'bg-[#ED1C24] text-white border-[#ED1C24]' 
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#ED1C24] hover:text-[#ED1C24]' 
                    }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 transition-opacity duration-300" 
             wire:loading.class="opacity-50 pointer-events-none">
            
            @forelse($courses as $course)
                <div class="group bg-white rounded-2xl border border-gray-100 shadow-[0_4px_20px_rgb(0,0,0,0.05)] hover:shadow-[0_10px_25px_rgb(0,0,0,0.1)] transition-all duration-300 overflow-hidden flex flex-col h-full">
                    
                <div class="relative h-48 overflow-hidden bg-gray-100">
    {{-- Tambahkan link pembungkus gambar di sini --}}
    <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="block w-full h-full">
        <img src="{{ Storage::url($course->image) }}" 
             alt="{{ $course->title }}" 
             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
    </a>
    
    <button class="absolute top-3 right-3 w-8 h-8 bg-white rounded-full flex items-center justify-center shadow-sm hover:text-[#ED1C24] transition">
        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
        </svg>
    </button>

    @if($course->category)
        {{-- Label kategori juga bisa diberi link jika perlu --}}
        <div class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-[#ED1C24] shadow-sm">
            {{ $course->category->name }}
        </div>
    @endif
</div>
                    
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-4 text-xs font-medium text-red-500 mb-3">
                            {{-- Tanggal Mulai --}}
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ \Carbon\Carbon::parse($course->start_date)->translatedFormat('d M Y') }}</span>
                            </div>
                            
                            {{-- Jumlah Modul (SUDAH DIGANTI) --}}
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span>{{ $course->modules_count }} Modul</span>
                            </div>
                        </div>

                        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug group-hover:text-[#ED1C24] transition-colors">
                        <a href="{{ route('course.detail', $course->slug) }}" wire:navigate>{{ $course->title }}</a>
                        </h3>
                        
                        <p class="text-gray-500 text-sm leading-relaxed mb-4 line-clamp-3">
                            {{ $course->description }}
                        </p>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 text-center py-12">
                    <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-100 mb-4">
                        <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900">Belum ada kursus</h3>
                    <p class="text-gray-500">Silakan pilih kategori lain atau cek kembali nanti.</p>
                </div>
            @endforelse

        </div>

        <div class="mt-12 text-center">
        <a href="{{ route('courses.index') }}" wire:navigate 
           class="inline-flex items-center justify-center px-8 py-3 text-sm font-semibold text-white transition-all duration-200 bg-[#ED1C24] border border-transparent rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-600 shadow-md hover:shadow-lg">
            Lihat Semua Course
            <svg class="w-5 h-5 ml-2 -mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
            </svg>
        </a>
    </div>

    </div>
</section>