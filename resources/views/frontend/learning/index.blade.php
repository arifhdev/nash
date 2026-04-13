<x-app-layout>
    @php
        // Mengambil data user yang sedang login
        $user = auth()->user();
        
        // Mengambil total Poin (Mata Uang) dan XP (Leaderboard)
        $totalPoints = $user->total_points ?? 0;
        $totalXP = $user->total_xp ?? 0;
        
        // Menghitung peringkat global (Dengan Tie-Breaker + Exclude Admin)
        $globalRank = \App\Models\User::where('user_type', '!=', 'ahm') // Kecualikan admin pusat
            ->where(function($query) use ($totalXP, $user) {
                // Syarat 1: Hitung orang yang XP-nya mutlak lebih besar
                $query->where('total_xp', '>', $totalXP)
                      // Syarat 2 (Tie-Breaker): Jika XP-nya sama, hitung yang ID-nya lebih kecil (Daftar duluan)
                      ->orWhere(function($q) use ($totalXP, $user) {
                          $q->where('total_xp', '=', $totalXP)
                            ->where('id', '<', $user->id);
                      });
            })->count() + 1;
    @endphp

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
                        <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100 gap-6">
                            <div>
                                <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Learning Progress</h1>
                                <p class="mt-2 text-sm text-gray-500 font-medium">Pantau aktivitas dan capaian kompetensi Anda.</p>
                            </div>
                            
                            {{-- Wrapper Kanan: Tombol Check-In, Poin Badge & XP Badge --}}
                            <div class="mt-4 md:mt-0 flex flex-col items-end gap-2" 
                                 x-data="{ xp: {{ $totalXP }}, points: {{ $totalPoints }} }" 
                                 @points-updated.window="xp += $event.detail.xp || $event.detail.amount || 0; points += $event.detail.points || 0">
                                
                                {{-- LABEL COMING SOON UNTUK POIN & XP --}}
                                <div class="px-3 py-1 bg-gray-100 text-gray-500 text-[10px] font-black uppercase tracking-widest rounded-full border border-gray-200">
                                    Coming Soon
                                </div>

                                <div class="flex flex-row items-center gap-4">
                                    {{-- KOMPONEN TOMBOL CHECK-IN DI-HOLD SEMENTARA --}}
                                    {{-- 
                                    <div class="w-auto min-w-[220px]">
                                        <livewire:daily-check-in />
                                    </div>
                                    --}}

                                    <div class="flex items-center gap-3">
                                        {{-- POIN BADGE (Mata Uang) - Tampilan Abu-abu (Hold) --}}
                                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200 shadow-sm whitespace-nowrap grayscale opacity-70">
                                            <div class="p-2 bg-gray-400 rounded-lg text-white">
                                                {{-- Ikon Koin/Uang --}}
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            </div>
                                            <div class="text-right flex-1">
                                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">Saldo Poin</span>
                                                <span class="text-xl font-black text-gray-400">
                                                    <span x-text="points.toLocaleString('id-ID')">{{ number_format($totalPoints) }}</span> 
                                                    <span class="text-xs text-gray-400 font-bold">Pts</span>
                                                </span>
                                            </div>
                                        </div>

                                        {{-- XP BADGE (Leaderboard) - Tampilan Abu-abu (Hold) --}}
                                        <div class="flex items-center gap-3 bg-gray-50 px-4 py-3 rounded-xl border border-gray-200 shadow-sm whitespace-nowrap grayscale opacity-70">
                                            <div class="p-2 bg-gray-400 rounded-lg text-white">
                                                {{-- Ikon Lightning / Petir untuk XP --}}
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                            </div>
                                            <div class="text-right flex-1">
                                                <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest block">Total XP</span>
                                                <span class="text-xl font-black text-gray-400">
                                                    <span x-text="xp.toLocaleString('id-ID')">{{ number_format($totalXP) }}</span> 
                                                    <span class="text-xs text-gray-400 font-bold">XP</span>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
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

                            {{-- Card 2: Sertifikat (BISA DIKLIK) --}}
                            <a href="{{ route('my-certificates') }}" wire:navigate class="block bg-white rounded-xl p-5 border border-gray-200 hover:border-blue-200 hover:shadow-md hover:-translate-y-1 transition-all duration-300 group">
                                <div class="flex items-start justify-between mb-2">
                                    <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Sertifikat</p>
                                    <div class="p-1.5 bg-gray-50 rounded-lg text-gray-400 group-hover:bg-blue-50 group-hover:text-blue-600 transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                </div>
                                <p class="text-3xl font-black text-gray-900">{{ $completedCount ?? 0 }}</p>
                            </a>

                            {{-- Card 3: Peringkat Global (DI-HOLD / DISABLED) --}}
                            <div class="block bg-gray-50 rounded-xl p-5 border border-gray-200 grayscale opacity-70 cursor-not-allowed">
                                <div class="flex items-start justify-between mb-2">
                                    <div class="flex flex-col gap-1">
                                        <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Peringkat Global</p>
                                        <span class="inline-block px-2 py-0.5 bg-gray-200 text-gray-500 text-[9px] font-bold uppercase tracking-widest rounded w-max">Coming Soon</span>
                                    </div>
                                    <div class="p-1.5 bg-gray-200 rounded-lg text-gray-400">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>
                                    </div>
                                </div>
                                <p class="text-3xl font-black text-gray-400 mt-2">
                                    <span class="text-gray-300 text-xl">-</span>
                                </p>
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
                                @php
                                    $ongoingCourses = $enrolledCourses->filter(function($course) {
                                        return $course->pivot->progress_percent < 100;
                                    });
                                @endphp

                                @forelse($ongoingCourses as $course)
                                    <div class="group flex flex-col md:flex-row gap-6 p-5 rounded-2xl border border-gray-200 hover:border-red-200 hover:shadow-lg hover:shadow-red-500/5 transition-all duration-300 bg-white">
                                        
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
                                        <div class="w-24 h-24 bg-gray-50 rounded-full flex items-center justify-center mb-6 border border-gray-100 animate-pulse">
                                            <svg class="w-10 h-10 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-black text-gray-900 uppercase tracking-tight">Tidak ada kursus berjalan</h3>
                                        <p class="text-gray-500 text-sm mt-2 max-w-sm">Semua kursus yang Anda ambil telah diselesaikan. Luar biasa!</p>
                                        <a href="{{ route('courses.index') }}" wire:navigate class="mt-8 inline-flex items-center px-8 py-3 bg-[#ED1C24] text-white text-xs font-black uppercase tracking-widest rounded-lg hover:bg-red-700 transition shadow-lg shadow-red-200">
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