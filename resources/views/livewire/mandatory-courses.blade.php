<div class="bg-[#F3F4F6] min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            <x-learning-sidebar />

            <main class="flex-1 space-y-8">
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 min-h-[700px]">
                    
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100 gap-4">
                        <div>
                            <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Course Wajib</h1>
                            <p class="mt-2 text-sm text-gray-500 font-medium">Daftar materi yang wajib Anda selesaikan sesuai jabatan.</p>
                        </div>

                        <div class="relative w-full md:w-72">
                            <input type="text" 
                                   wire:model.live.debounce.500ms="search" 
                                   placeholder="Cari judul materi wajib..." 
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition shadow-sm placeholder-gray-400">
                            
                            <div class="absolute left-3.5 top-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            <div wire:loading.flex wire:target="search" class="absolute right-3 top-3 hidden items-center">
                                <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>

                    {{-- COURSE LIST VIEW --}}
                    <div class="flex flex-col gap-5">
                        @forelse($courses as $course)
                            @php
                                $enrollment = $course->users->first();
                                $progress = $enrollment ? $enrollment->pivot->progress_percent : 0;
                                $isLocked = $course->isLockedForUser(auth()->user());
                            @endphp

                            <div class="group flex flex-col sm:flex-row bg-white rounded-2xl border border-gray-200 overflow-hidden transition-all duration-300 {{ $isLocked ? 'opacity-90' : 'hover:border-red-200 hover:shadow-lg hover:shadow-red-500/5' }}">
                                
                                {{-- Thumbnail Kiri --}}
                                @if($isLocked)
                                    <div class="block relative w-full sm:w-64 md:w-72 h-48 sm:h-auto bg-gray-100 overflow-hidden flex-shrink-0 cursor-not-allowed">
                                @else
                                    <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="block relative w-full sm:w-64 md:w-72 h-48 sm:h-auto bg-gray-100 overflow-hidden flex-shrink-0">
                                @endif
                                
                                    @if($course->image)
                                        <img src="{{ Storage::url($course->image) }}" class="w-full h-full object-cover transition-transform duration-500 {{ $isLocked ? 'grayscale' : 'group-hover:scale-105' }}" alt="{{ $course->title }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif

                                    @if($isLocked)
                                        <div class="absolute inset-0 bg-gray-900/40 flex flex-col items-center justify-center text-white backdrop-blur-[2px]">
                                            <svg class="w-8 h-8 mb-1 drop-shadow-md" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        </div>
                                    @endif
                                    
                                    <div class="absolute top-4 left-4 z-10">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-white/90 text-gray-900 backdrop-blur-sm shadow-sm border border-gray-100">
                                            {{ $course->category->name ?? 'General' }}
                                        </span>
                                    </div>

                                    <div class="absolute top-4 right-4 sm:hidden z-10">
                                        @if($isLocked)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-gray-800 text-white backdrop-blur-sm">Terkunci</span>
                                        @elseif($progress >= 100)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-green-100 text-green-700">Selesai</span>
                                        @elseif($progress > 0)
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-yellow-100 text-yellow-700">{{ $progress }}%</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-1 rounded-md text-[10px] font-bold bg-red-100 text-red-700">Wajib</span>
                                        @endif
                                    </div>

                                @if($isLocked)
                                    </div>
                                @else
                                    </a>
                                @endif

                                {{-- Body Kanan --}}
                                <div class="flex-1 p-6 flex flex-col justify-between">
                                    <div>
                                        <div class="flex justify-between items-start gap-4">
                                            @if($isLocked)
                                                <h3 class="text-xl font-bold text-gray-500 leading-tight mb-2 cursor-not-allowed">
                                                    {{ $course->title }}
                                                </h3>
                                            @else
                                                <a href="{{ route('course.detail', $course->slug) }}" wire:navigate>
                                                    <h3 class="text-xl font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors leading-tight mb-2">
                                                        {{ $course->title }}
                                                    </h3>
                                                </a>
                                            @endif
                                            
                                            <div class="hidden sm:block flex-shrink-0">
                                                @if($isLocked)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-800 text-white shadow-sm border border-gray-700">Terkunci</span>
                                                @elseif($progress >= 100)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700 shadow-sm border border-green-200">Selesai</span>
                                                @elseif($progress > 0)
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-yellow-100 text-yellow-700 shadow-sm border border-yellow-200">On Progress ({{ $progress }}%)</span>
                                                @else
                                                    <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-[#ED1C24] text-white shadow-sm border border-red-700 animate-pulse">Belum Dimulai</span>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        @if($isLocked)
                                            <div class="mt-3 mb-2 p-3 bg-red-50 rounded-xl border border-red-100">
                                                <p class="text-[11px] font-bold text-red-700 mb-1.5 flex items-center gap-1 uppercase tracking-wide">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                    Prasyarat Belum Selesai:
                                                </p>
                                                <ul class="list-disc list-inside text-red-600 space-y-1 text-xs">
                                                    @foreach($course->prerequisites as $req)
                                                        @if(!auth()->user()->hasCompletedCourse($req->id))
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
                                            <div class="text-gray-500 text-sm leading-relaxed mt-2 line-clamp-2 prose prose-sm max-w-none 
                                                [&_a]:text-blue-600 [&_a]:underline [&_a]:font-semibold hover:[&_a]:text-blue-800 transition-colors"
                                                x-data 
                                                x-init="$el.querySelectorAll('a').forEach(a => { a.setAttribute('target', '_blank'); a.setAttribute('rel', 'noopener noreferrer'); })">
                                                {!! $course->description !!}
                                            </div>
                                        @endif
                                    </div>

                                    <div class="mt-6 flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-t border-gray-50 pt-4">
                                        <div class="w-full sm:w-1/2">
                                            @if(!$isLocked)
                                                @if($progress > 0 && $progress < 100)
                                                    <div class="flex items-center justify-between mb-1">
                                                        <span class="text-[10px] font-bold text-gray-400 uppercase">Progress</span>
                                                        <span class="text-[10px] font-bold text-[#ED1C24]">{{ $progress }}%</span>
                                                    </div>
                                                    <div class="w-full bg-gray-100 rounded-full h-1.5">
                                                        <div class="bg-[#ED1C24] h-1.5 rounded-full" style="width: {{ $progress }}%"></div>
                                                    </div>
                                                @elseif($progress == 0)
                                                    <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                                                        <svg class="w-4 h-4 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                                        <span>Wajib Diselesaikan</span>
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                        
                                        @if($isLocked)
                                            <span class="inline-flex justify-center items-center text-xs font-black text-gray-400 bg-gray-100 px-6 py-2.5 rounded-lg cursor-not-allowed uppercase tracking-wider shadow-sm border border-gray-200 flex-shrink-0">
                                                Terkunci
                                            </span>
                                        @else
                                            <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="inline-flex justify-center items-center text-xs font-black text-white bg-black px-6 py-2.5 rounded-lg hover:bg-[#ED1C24] transition-colors uppercase tracking-wider shadow-sm flex-shrink-0">
                                                {{ $progress >= 100 ? 'Lihat Ulang' : ($progress > 0 ? 'Lanjut Belajar' : 'Mulai Belajar') }}
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center border-2 border-dashed border-gray-200 rounded-2xl">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-green-50 mb-4">
                                    <svg class="w-8 h-8 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $search ? 'Tidak ditemukan' : 'Luar Biasa!' }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $search ? 'Coba kata kunci lain.' : 'Belum ada course yang diwajibkan untuk jabatan Anda.' }}
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-8">
                        {{ $courses->links() }} 
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>