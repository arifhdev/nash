<div class="py-12 bg-gray-50 min-h-screen">
    {{-- Notifikasi Sukses --}}
    @if (session()->has('success'))
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mb-6">
            <div class="rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3 animate-fade-in-down">
                <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span class="text-sm font-bold text-green-700">{{ session('success') }}</span>
            </div>
        </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            {{-- COLUMN KIRI: DETAIL MATERI --}}
            <div class="lg:col-span-2 space-y-8">
                
                {{-- Header Card --}}
                <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-100">
                    <span class="px-3 py-1 bg-red-100 text-[#ED1C24] text-xs font-bold rounded-full uppercase tracking-wider">
                        {{ $course->category->name ?? 'Uncategorized' }}
                    </span>
                    <h1 class="text-3xl font-extrabold text-gray-900 mt-4 leading-tight">{{ $course->title }}</h1>
                    <p class="text-gray-600 mt-4 leading-relaxed text-sm md:text-base">{{ $course->description }}</p>
                </div>

                {{-- Modules List --}}
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                        <h2 class="text-xl font-bold text-gray-900">Materi Kursus</h2>
                        <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">{{ $course->modules->count() }} Modul</span>
                    </div>

                    <div class="divide-y divide-gray-100">
                        @forelse($course->modules as $index => $module)
                            <div x-data="{ open: {{ $index == 0 ? 'true' : 'false' }} }" class="bg-white">
                                <button @click="open = !open" class="w-full flex items-center justify-between p-6 hover:bg-gray-50 transition group">
                                    <div class="flex items-center gap-4 text-left">
                                        <div class="w-8 h-8 bg-gray-900 text-white rounded-lg flex items-center justify-center text-sm font-bold group-hover:bg-[#ED1C24] transition-colors">
                                            {{ $index + 1 }}
                                        </div>
                                        <div>
                                            <h3 class="font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors">{{ $module->name }}</h3>
                                            <p class="text-xs text-gray-500">{{ $module->lessons->count() }} Pelajaran</p>
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 transition-transform duration-300" :class="open ? 'rotate-180 text-[#ED1C24]' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="open" x-collapse>
                                    <div class="px-6 pb-6 space-y-3">
                                        @foreach($module->lessons as $lesson)
                                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-red-100 hover:bg-red-50/30 transition group cursor-default">
                                                <div class="flex items-center gap-3">
                                                    @if($lesson->type == 'video')
                                                        <div class="p-1.5 bg-white rounded-md text-[#ED1C24] shadow-sm">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M2 6a2 2 0 012-2h12a2 2 0 012 2v8a2 2 0 01-2 2H4a2 2 0 01-2-2V6zm10 4l-4-3v6l4-3z"/></svg>
                                                        </div>
                                                    @else
                                                        <div class="p-1.5 bg-white rounded-md text-blue-500 shadow-sm">
                                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9 2a2 2 0 00-2 2v8a2 2 0 002 2h6a2 2 0 002-2V6.414L14.586 2H9z"/></svg>
                                                        </div>
                                                    @endif
                                                    <span class="text-sm font-medium text-gray-700 group-hover:text-gray-900">{{ $lesson->title }}</span>
                                                </div>
                                                <span class="text-xs text-gray-400 font-mono bg-white px-2 py-1 rounded border border-gray-100">{{ $lesson->duration_minutes }} Min</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="p-8 text-center">
                                <p class="text-gray-500 italic text-sm">Belum ada modul materi untuk kursus ini.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- COLUMN KANAN: SIDEBAR ACTION --}}
            <div class="lg:col-span-1">
                <div class="sticky top-24 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                    
                    {{-- Course Thumbnail --}}
                    <div class="relative h-56 bg-gray-200">
                        @if($course->image)
                            <img src="{{ Storage::url($course->image) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gray-100 text-gray-400">
                                <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        
                        {{-- Overlay Gradient --}}
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        
                        <div class="absolute bottom-4 left-4">
                            <span class="px-2 py-1 bg-white/90 backdrop-blur-sm rounded text-[10px] font-bold uppercase tracking-wider text-gray-900">
                                {{ $course->category->name ?? 'Course' }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6">
                        
                        {{-- ACTION BUTTON SECTION --}}
                        <div class="mt-2">
                            @if($isEnrolled)
                                {{-- JIKA SUDAH ENROLL: Tombol Lanjutkan (Link Langsung ke Player) --}}
                                <a href="{{ route('course.player', $course->slug) }}" 
                                   wire:navigate
                                   class="w-full py-4 bg-gray-900 rounded-xl font-black text-white uppercase tracking-widest hover:bg-black transition shadow-lg flex items-center justify-center gap-2 group cursor-pointer text-center">
                                    <span>Lanjutkan Belajar</span>
                                    <svg class="w-4 h-4 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </a>
                            @else
                                {{-- JIKA BELUM ENROLL: Tombol Daftar (Mencatat Enroll Dulu) --}}
                                <button wire:click="startLearning" 
                                        wire:loading.attr="disabled"
                                        class="w-full py-4 bg-[#ED1C24] rounded-xl font-black text-white uppercase tracking-widest hover:bg-red-700 transition shadow-lg shadow-red-200 flex items-center justify-center gap-2 group">
                                    
                                    <span wire:loading.remove wire:target="startLearning">
                                        Mulai Belajar Sekarang
                                    </span>

                                    {{-- Loading State --}}
                                    <span class="hidden flex items-center gap-2" 
                                          wire:loading.class.remove="hidden" 
                                          wire:loading.class="flex"
                                          wire:target="startLearning">
                                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        Memproses...
                                    </span>
                                </button>
                            @endif
                        </div>

                        {{-- Course Metadata --}}
                        <div class="mt-8 space-y-4">
                            <div class="flex items-center justify-between text-sm">
                                <span class="text-gray-500 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                    Total Modul
                                </span>
                                <span class="font-bold text-gray-900">{{ $course->modules->count() }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between text-sm border-t border-gray-50 pt-4">
                                <span class="text-gray-500 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Sertifikat
                                </span>
                                <span class="font-bold {{ $course->has_certificate ? 'text-green-600' : 'text-gray-400' }}">
                                    {{ $course->has_certificate ? 'Tersedia' : 'Tidak Ada' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between text-sm border-t border-gray-50 pt-4">
                                <span class="text-gray-500 flex items-center gap-2">
                                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    Estimasi Waktu
                                </span>
                                <span class="font-bold text-gray-900">~ 2 Jam</span>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
</div>