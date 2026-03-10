@php
    $course = $record->course;
    $user = $record->user;
    
    // Ambil data completion user untuk course ini
    $completedLessons = $user->completedLessons()
        ->wherePivot('course_id', $course->id)
        ->get()
        ->keyBy('id');

    // Logic: Cari materi yang paling terakhir diakses dari seluruh modul
    $latestAccessDate = null;
    $latestLessonName = '-';

    foreach ($completedLessons as $lessonId => $lessonData) {
        $accessTime = $lessonData->pivot->last_accessed_at;
        if ($accessTime) {
            $carbonTime = \Carbon\Carbon::parse($accessTime);
            if (!$latestAccessDate || $carbonTime->gt($latestAccessDate)) {
                $latestAccessDate = $carbonTime;
                // Jaga-jaga jika tabel lessons pakai kolom 'title' atau 'name'
                $latestLessonName = $lessonData->title ?? $lessonData->name ?? 'Materi Tanpa Judul'; 
            }
        }
    }
@endphp

<div class="space-y-5">
    
    {{-- KOTAK SUMMARY: Menampilkan info akses terakhir secara global --}}
    <div class="p-4 bg-primary-50 dark:bg-primary-900/20 border border-primary-200 dark:border-primary-800 rounded-xl">
        <h2 class="text-base font-bold text-primary-700 dark:text-primary-400 mb-3 flex items-center gap-2">
            <svg class="w-5 h-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Ringkasan Aktivitas
        </h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
     
            <div>
                <span class="block text-gray-500 dark:text-gray-400 mb-1">Aktivitas Terakhir:</span>
                <div class="text-gray-900 dark:text-white font-medium">
                    @if($latestAccessDate)
                        {{ $latestAccessDate->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB <br>
                        <span class="text-xs font-normal text-gray-500 dark:text-gray-400 inline-block mt-1">
                            Membuka: <strong>{{ $latestLessonName }}</strong>
                        </span>
                    @else
                        <span class="text-gray-400 italic">Belum ada aktivitas terekam</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- DAFTAR MODUL & LESSON --}}
    @if($course->modules && $course->modules->count() > 0)
        @foreach($course->modules as $module)
            <div class="p-4 border border-gray-200 rounded-xl bg-white dark:bg-gray-900 dark:border-white/10 shadow-sm">
                
                {{-- Menampilkan Nama Modul (Menggunakan $module->name) --}}
                <h3 class="text-base font-bold mb-4 text-gray-900 dark:text-white border-b border-gray-200 dark:border-white/10 pb-2 flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                    </svg>
                    {{ $module->name ?? 'Tanpa Nama Modul' }}
                </h3>
                
                <div class="space-y-3">
                    @if($module->lessons && $module->lessons->count() > 0)
                        @foreach($module->lessons as $lesson)
                            @php
                                $isAccessed = $completedLessons->has($lesson->id);
                                $pivotData = $isAccessed ? $completedLessons->get($lesson->id)->pivot : null;
                                $isCompleted = $pivotData && $pivotData->completed_at !== null;
                                // Antisipasi jika kolom tabel lessons menggunakan 'name' alih-alih 'title'
                                $lessonDisplay = $lesson->title ?? $lesson->name ?? 'Materi Tanpa Judul';
                            @endphp
                            
                            <div class="flex flex-col sm:flex-row justify-between p-3 bg-gray-50 dark:bg-white/5 rounded-lg border border-gray-100 dark:border-white/5 transition hover:bg-gray-100 dark:hover:bg-white/10">
                                <div class="mb-2 sm:mb-0">
                                    <span class="font-medium text-sm text-gray-700 dark:text-gray-200 flex items-center gap-2 block">
                                        <svg class="w-4 h-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        {{ $lessonDisplay }}
                                    </span>
                                    
                                    {{-- Waktu akses terakhir per lesson --}}
                                    @if($isAccessed && $pivotData->last_accessed_at)
                                        <span class="text-xs text-gray-500 dark:text-gray-400 block mt-1 ml-6">
                                            Akses terakhir: {{ \Carbon\Carbon::parse($pivotData->last_accessed_at)->timezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                        </span>
                                    @endif
                                </div>
                                
                                <div class="text-left sm:text-right flex flex-col items-start sm:items-end justify-center ml-6 sm:ml-0">
                                    @if($isCompleted)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-success-100 text-success-700 dark:bg-success-900/30 dark:text-success-400 mb-1">
                                            Selesai
                                        </span>
                                        
                                        @php
                                            $startedAt = $pivotData->started_at ?? $pivotData->created_at ?? null;
                                            
                                            if ($startedAt) {
                                                $started = \Carbon\Carbon::parse($startedAt);
                                                $completed = \Carbon\Carbon::parse($pivotData->completed_at);
                                                $duration = $started->diffForHumans($completed, [
                                                    'syntax' => \Carbon\CarbonInterface::DIFF_ABSOLUTE,
                                                    'parts' => 2,
                                                ]);
                                            } else {
                                                $duration = '-';
                                            }
                                        @endphp
                                        <div class="text-xs text-gray-600 dark:text-gray-400">
                                            <strong>Durasi:</strong> {{ $duration }}
                                        </div>
                                    @elseif($isAccessed)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-info-100 text-info-700 dark:bg-info-900/30 dark:text-info-400">
                                            Sedang Dipelajari
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold uppercase tracking-wider bg-gray-200 text-gray-600 dark:bg-white/10 dark:text-gray-400">
                                            Belum Dibuka
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-sm text-gray-500 dark:text-gray-400 italic px-2">Belum ada materi (lesson) di modul ini.</p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="p-8 text-center border border-dashed rounded-xl border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400">
            Belum ada modul yang ditambahkan ke kursus ini.
        </div>
    @endif
</div>