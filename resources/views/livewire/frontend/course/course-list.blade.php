<div class="min-h-screen bg-gray-50 py-12">
    <div class="container mx-auto px-4 sm:px-6 lg:px-8 max-w-7xl">
        
        {{-- Header & Search --}}
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Katalog <span class="text-[#ED1C24]">Pembelajaran</span>
            </h1>
            <p class="text-gray-500 mb-8">
                Tingkatkan kompetensi Anda dengan materi eksklusif dari para ahli.
            </p>

            {{-- Search Bar --}}
            <div class="relative max-w-xl mx-auto">
                <input wire:model.live.debounce.300ms="search" 
                       type="text" 
                       placeholder="Cari topik pembelajaran..." 
                       class="w-full pl-12 pr-4 py-3 rounded-full border border-gray-200 focus:border-[#ED1C24] focus:ring focus:ring-red-100 transition shadow-sm">
                <div class="absolute left-4 top-3.5 text-gray-400">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>
            </div>
        </div>

        {{-- Filter Categories --}}
        <div class="flex flex-wrap justify-center gap-3 mb-10">
            <button wire:click="setCategory(null)"
                class="px-5 py-2 rounded-full text-sm font-medium border transition shadow-sm
                {{ is_null($category) 
                    ? 'bg-[#ED1C24] text-white border-[#ED1C24]' 
                    : 'bg-white text-gray-600 border-gray-200 hover:border-[#ED1C24] hover:text-[#ED1C24]' 
                }}">
                Semua
            </button>
            
            @foreach($categories as $cat)
                <button wire:click="setCategory({{ $cat->id }})"
                    class="px-5 py-2 rounded-full text-sm font-medium border transition shadow-sm
                    {{ $category == $cat->id 
                        ? 'bg-[#ED1C24] text-white border-[#ED1C24]' 
                        : 'bg-white text-gray-600 border-gray-200 hover:border-[#ED1C24] hover:text-[#ED1C24]' 
                    }}">
                    {{ $cat->name }}
                </button>
            @endforeach
        </div>

        {{-- Course Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12" 
             wire:loading.class="opacity-50 pointer-events-none">
            
            @forelse($courses as $course)
                @php
                    // Cek status kunci untuk user yang sedang login
                    $isLocked = auth()->check() ? $course->isLockedForUser(auth()->user()) : false;

                    // PERBAIKAN: Cek apakah Wajib Diikuti (Sesuai dengan Single Jabatan User)
                    $isMandatory = auth()->check() && auth()->user()->position_id 
                                   ? ($course->mandatoryPositions ?? collect([]))->contains('id', auth()->user()->position_id) 
                                   : false;
                @endphp

                <div class="group bg-white rounded-2xl border border-gray-100 shadow-sm transition-all duration-300 overflow-hidden flex flex-col h-full relative {{ $isLocked ? 'opacity-90' : 'hover:shadow-xl' }}">
                    
                    {{-- Badge Wajib Diikuti Mengambang di Kanan Atas --}}
                    @if($isMandatory)
                        <div class="absolute top-3 right-3 z-10 px-3 py-1 bg-amber-100/90 backdrop-blur-sm text-amber-700 text-[10px] font-black rounded-full uppercase tracking-widest flex items-center gap-1 shadow-sm border border-amber-200">
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path></svg>
                            Wajib
                        </div>
                    @endif

                    {{-- Image Wrapper --}}
                    <div class="relative h-48 overflow-hidden bg-gray-100">
                        @if($isLocked)
                            <div class="block w-full h-full relative">
                                <img src="{{ Storage::url($course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover grayscale">
                                {{-- Overlay Terkunci --}}
                                <div class="absolute inset-0 bg-gray-900/40 flex flex-col items-center justify-center text-white backdrop-blur-[2px]">
                                    <svg class="w-10 h-10 mb-2 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    <span class="text-sm font-bold tracking-widest uppercase drop-shadow-md">Terkunci</span>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="block w-full h-full">
                                <img src="{{ Storage::url($course->image) }}" 
                                     alt="{{ $course->title }}" 
                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            </a>
                        @endif

                        @if($course->category)
                            <div class="absolute top-3 left-3 px-3 py-1 bg-white/90 backdrop-blur-sm rounded-full text-xs font-bold text-[#ED1C24] shadow-sm z-10">
                                {{ $course->category->name }}
                            </div>
                        @endif
                    </div>
                    
                    {{-- Content --}}
                    <div class="p-6 flex flex-col flex-grow">
                        {{-- Meta Info --}}
                        <div class="flex items-center gap-4 text-xs font-medium text-red-500 mb-3">
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ \Carbon\Carbon::parse($course->start_date)->translatedFormat('d M Y') }}</span>
                            </div>
                            <div class="flex items-center gap-1">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <span>{{ $course->modules_count }} Modul</span>
                            </div>
                        </div>

                        {{-- Title --}}
                        <h3 class="text-lg font-bold text-gray-900 mb-2 leading-snug transition-colors {{ $isLocked ? '' : 'group-hover:text-[#ED1C24]' }}">
                            @if($isLocked)
                                <span class="cursor-not-allowed">{{ $course->title }}</span>
                            @else
                                <a href="{{ route('course.detail', $course->slug) }}" wire:navigate>{{ $course->title }}</a>
                            @endif
                        </h3>
                        
                        {{-- Description / Warning Prasyarat --}}
                        @if($isLocked)
                            <div class="mt-2 mb-4 p-3 bg-red-50 rounded-xl border border-red-100 flex-grow">
                                <p class="text-xs font-bold text-red-700 mb-2 flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Prasyarat Wajib:
                                </p>
                                <ul class="list-disc list-inside text-red-600 space-y-1 text-xs">
                                    @foreach($course->prerequisites as $req)
                                        @if(auth()->check() && !auth()->user()->hasCompletedCourse($req->id))
                                            <li class="line-clamp-1" title="{{ $req->title }}">
                                                <a href="{{ route('course.detail', $req->slug) }}" wire:navigate class="hover:underline hover:text-red-800 transition-colors">
                                                    {{ $req->title }}
                                                </a>
                                            </li>
                                        @endif
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            {{-- FIX: RENDER HTML, STYLING LINK, & OPEN NEW WINDOW --}}
                            <div class="text-gray-600 text-sm leading-relaxed mb-4 line-clamp-3 prose prose-sm max-w-none 
                                [&_a]:text-blue-600 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-blue-800 transition-colors"
                                x-data 
                                x-init="$el.querySelectorAll('a').forEach(a => { a.setAttribute('target', '_blank'); a.setAttribute('rel', 'noopener noreferrer'); })">
                                {!! $course->description !!}
                            </div>
                        @endif

                        {{-- Footer Button --}}
                        <div class="mt-auto pt-4 border-t border-gray-100">
                            @if($isLocked)
                                <span class="inline-flex items-center text-sm font-semibold text-gray-400 cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                    Selesaikan Prasyarat
                                </span>
                            @else
                                <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="inline-flex items-center text-sm font-semibold text-[#ED1C24] hover:text-red-700 transition">
                                    Pelajari Selengkapnya
                                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full text-center py-16">
                    <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-red-50 mb-4">
                        <svg class="w-10 h-10 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 mb-2">Tidak ditemukan</h3>
                    <p class="text-gray-500">
                        Maaf, tidak ada kursus yang cocok dengan pencarian "{{ $search }}".
                    </p>
                    <button wire:click="$set('search', '')" class="mt-4 text-[#ED1C24] font-medium hover:underline">
                        Hapus Pencarian
                    </button>
                </div>
            @endforelse

        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $courses->links() }} 
        </div>

    </div>
</div>