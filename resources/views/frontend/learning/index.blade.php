<x-app-layout>
    <div class="bg-[#F3F4F6] min-h-screen">
        <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col lg:flex-row gap-8">
                
                {{-- 1. MODULAR SIDEBAR --}}
                <x-learning-sidebar />

                {{-- 2. MAIN CONTENT AREA --}}
                <main class="flex-1">
                    {{-- Container Utama --}}
                    <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 min-h-[700px]">
                        
                        {{-- Content Header --}}
                        <div class="flex flex-col md:flex-row md:items-end justify-between mb-8 pb-6 border-b border-gray-100">
                            <div>
                                <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Learning Progress</h1>
                                <p class="mt-2 text-sm text-gray-500 font-medium">Pantau aktivitas dan capaian kompetensi Anda.</p>
                            </div>
                            
                            {{-- XP Badge (Dinamis) --}}
                            <div class="mt-4 md:mt-0 flex items-center gap-3 bg-red-50 px-5 py-3 rounded-xl border border-red-100">
                                <div class="p-2 bg-[#ED1C24] rounded-lg text-white">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                </div>
                                <div class="text-right">
                                    <span class="text-[10px] font-bold text-red-600 uppercase tracking-widest block">Total Poin</span>
                                    <span class="text-xl font-black text-gray-900">{{ number_format($totalXP ?? 0) }} <span class="text-xs text-gray-500 font-bold">XP</span></span>
                                </div>
                            </div>
                        </div>

                        {{-- Stats / Quick Access Grid (Dinamis) --}}
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-10">
                            {{-- Card 1: Kursus Aktif --}}
                            <div class="bg-white rounded-xl p-5 border border-gray-200 hover:border-red-200 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Kursus Aktif</p>
                                    <div class="p-1.5 bg-gray-50 rounded-lg text-gray-400 group-hover:bg-red-50 group-hover:text-[#ED1C24] transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </div>
                                </div>
                                <p class="text-3xl font-black text-gray-900">{{ $activeCount ?? 0 }}</p>
                            </div>

                            {{-- Card 2: Sertifikat --}}
                            <div class="bg-white rounded-xl p-5 border border-gray-200 hover:border-blue-200 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sertifikat</p>
                                    <div class="p-1.5 bg-gray-50 rounded-lg text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <p class="text-3xl font-black text-gray-900">{{ $completedCount ?? 0 }}</p>
                            </div>

                            {{-- Card 3: Peringkat --}}
                            <div class="bg-white rounded-xl p-5 border border-gray-200 hover:border-orange-200 hover:shadow-md transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Peringkat Global</p>
                                    <div class="p-1.5 bg-gray-50 rounded-lg text-gray-400 group-hover:bg-orange-50 group-hover:text-orange-500 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                </div>
                                <p class="text-3xl font-black text-gray-900">-</p>
                            </div>
                        </div>

                        {{-- Course List Section --}}
                        <div class="space-y-6">
    <div class="flex items-center justify-between">
        <h2 class="text-lg font-extrabold text-gray-900">Lanjutkan Belajar</h2>
        <a href="{{ route('my-courses') }}" wire:navigate class="group flex items-center text-xs font-bold text-[#ED1C24] uppercase tracking-wider hover:text-red-700 transition">
            Lihat Semua Katalog
            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
        </a>
    </div>

    <div class="grid grid-cols-1 gap-5">
        {{-- TAMBAHKAN BLOK PHP INI UNTUK FILTER --}}
        @php
            $ongoingCourses = $enrolledCourses->filter(function($course) {
                // Tampilkan hanya jika progress belum 100%
                return $course->pivot->progress_percent < 100;
            });
        @endphp

        {{-- UBAH VARIABEL LOOP MENJADI $ongoingCourses --}}
        @forelse($ongoingCourses as $course)
            <div class="group flex flex-col md:flex-row gap-6 p-5 rounded-2xl border border-gray-200 hover:border-red-200 hover:shadow-lg hover:shadow-red-500/5 transition-all duration-300 bg-white">
                
                {{-- ... Bagian Thumbnail & Konten tetap sama ... --}}
                
                {{-- Thumbnail --}}
                <div class="w-full md:w-56 h-32 bg-gray-100 rounded-xl flex-shrink-0 overflow-hidden relative">
                    @if($course->image)
                        <img src="{{ Storage::url($course->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $course->title }}">
                    @else
                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                    @endif
                    <a href="{{ route('course.player', $course->slug) }}" wire:navigate class="absolute inset-0 bg-black/0 group-hover:bg-black/10 transition-colors"></a>
                </div>

                {{-- Info --}}
                <div class="flex-1 flex flex-col justify-between py-1">
                    <div>
                        <div class="flex justify-between items-start mb-2">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-gray-100 text-gray-600 border border-gray-200">
                                {{ $course->category->name ?? 'General' }}
                            </span>
                            
                            {{-- Karena sudah difilter, status pasti belum selesai --}}
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wide">{{ $course->pivot->progress_percent }}% Selesai</span>
                        </div>
                        
                        <a href="{{ route('course.detail', $course->slug) }}" wire:navigate>
                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors leading-tight line-clamp-2">
                                {{ $course->title }}
                            </h3>
                        </a>
                    </div>
                    
                    <div class="mt-4 flex items-center gap-5">
                        <div class="flex-1">
                            <div class="flex justify-between text-[10px] font-bold text-gray-400 mb-1 uppercase">
                                <span>Progress</span>
                                <span>{{ $course->pivot->progress_percent }}/100</span>
                            </div>
                            <div class="bg-gray-100 rounded-full h-2 overflow-hidden">
                                <div class="bg-[#ED1C24] h-full rounded-full transition-all duration-1000 relative overflow-hidden" style="width: {{ $course->pivot->progress_percent }}%">
                                    <div class="absolute inset-0 bg-white/20 w-full h-full animate-[shimmer_2s_infinite]"></div>
                                </div>
                            </div>
                        </div>
                        
                        <a href="{{ route('course.player', $course->slug) }}" wire:navigate class="flex-shrink-0 text-xs font-black text-white bg-black px-6 py-2.5 rounded-lg hover:bg-[#ED1C24] transition-colors shadow-sm uppercase tracking-wider">
                            Resume
                        </a>
                    </div>
                </div>

            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-24 text-center">
                {{-- ... Empty state ... --}}
                <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100 animate-pulse">
                    <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                {{-- Update pesan empty state agar sesuai konteks --}}
                <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Tidak ada kursus berjalan</h3>
                <p class="text-gray-500 text-sm mt-2 max-w-sm">Semua kursus yang Anda ambil telah diselesaikan. Luar biasa!</p>
                <a href="#" class="mt-8 inline-flex items-center px-8 py-3 bg-[#ED1C24] text-white text-xs font-black uppercase tracking-widest rounded-lg hover:bg-red-700 transition shadow-lg shadow-red-200">
                    Cari Kursus Baru
                </a>
            </div> 
        @endforelse
    </div>
</div>
                    </div>
                </main>

            </div>
        </div>
    </div>
</x-app-layout>