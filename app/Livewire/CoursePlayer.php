<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserActivity;
use App\Models\Certificate; // TAMBAHAN: Model Certificate
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str; // TAMBAHAN: Untuk Str::slug generate nomor reg
use Livewire\Component;

class CoursePlayer extends Component
{
    public $course;
    public $currentLesson;
    public $lessonsCompletedIds = []; 

    public function mount($courseSlug, $lessonSlug = null)
    {
        $this->course = Course::where('slug', $courseSlug)
            ->with(['modules' => function($q) {
                $q->orderBy('course_module.sort_order'); 
            }, 'modules.lessons' => function($q) {
                $q->where('lessons.is_active', true)
                  ->orderBy('lesson_module.sort_order');
            }])
            ->firstOrFail();

        if (Auth::check()) {
            $isEnrolled = Auth::user()->courses()->where('course_id', $this->course->id)->exists();
            if ($isEnrolled) {
                Auth::user()->courses()->updateExistingPivot($this->course->id, [
                    'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                ]);
            }
        }

        if ($lessonSlug) {
            $this->currentLesson = Lesson::where('slug', $lessonSlug)->firstOrFail();
        } else {
            $firstModule = $this->course->modules->first();
            $this->currentLesson = $firstModule ? $firstModule->lessons->first() : null;
        }

        if (Auth::check() && $this->currentLesson) {
            $user = Auth::user();
            $lessonPivot = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $this->currentLesson->id)
                ->where('course_id', $this->course->id)
                ->first();

            if (!$lessonPivot) {
                DB::table('lesson_user')->insert([
                    'user_id' => $user->id,
                    'lesson_id' => $this->currentLesson->id,
                    'course_id' => $this->course->id,
                    'count_view' => 1,
                    'failed_attempts' => 0,
                    'count_completed' => 0,
                    'started_at' => now()->timezone('Asia/Jakarta'),
                    'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                    'created_at' => now()->timezone('Asia/Jakarta'),
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);
            } else {
                DB::table('lesson_user')
                    ->where('id', $lessonPivot->id)
                    ->update([
                        'count_view' => DB::raw('count_view + 1'),
                        'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                        'updated_at' => now()->timezone('Asia/Jakarta'),
                    ]);
            }
        }

        $this->loadProgress();
    }

    public function loadProgress()
    {
        if (Auth::check()) {
            $this->lessonsCompletedIds = Auth::user()
                ->completedLessons()
                ->wherePivot('course_id', $this->course->id)
                ->whereNotNull('lesson_user.completed_at') 
                ->pluck('lessons.id')
                ->toArray();
        }
    }

    /**
     * Handle Kuis Gagal (DIPANGGIL DARI BLADE)
     */
    public function recordFailedAttempt()
    {
        $user = Auth::user();
        
        // 1. Increment jumlah gagal di database
        DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->update([
                'failed_attempts' => DB::raw('failed_attempts + 1'),
                'updated_at' => now()->timezone('Asia/Jakarta'),
            ]);

        // 2. Cek jumlah gagal terbaru
        $failedCount = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->value('failed_attempts');

        // 3. Jika sudah 3x, eksekusi hukuman remidi
        if ($failedCount >= 3) {
            return $this->resetPreviousLessonProgress();
        }

        return $failedCount;
    }

    /**
     * RESET PROGRESS MATERI SEBELUMNYA (HARDCORE FAIL PUNISHMENT)
     */
    public function resetPreviousLessonProgress()
    {
        $user = Auth::user();
        $allLessons = $this->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->search(fn($l) => $l->id === $this->currentLesson->id);

        // Reset hitungan gagal kuis ini dulu biar gak loop
        DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->update(['failed_attempts' => 0]);

        if ($currentIndex > 0) {
            $previousLesson = $allLessons[$currentIndex - 1];
            
            // 1. Matikan status selesai materi sebelumnya (Centang hijau hilang)
            DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $previousLesson->id)
                ->where('course_id', $this->course->id)
                ->update([
                    'completed_at' => null,
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);
            
            // 2. Sinkronkan Progress Kursus (Progress bar turun)
            $this->updateCourseProgress($user);

            // 3. Log Aktivitas - Update text jadi 3x
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'Hukuman Gagal Kuis 3x: Reset Materi ' . $previousLesson->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            session()->flash('error', 'Gagal kuis 3x! Kamu wajib tonton ulang materi sebelumnya.');

            return redirect()->route('course.player', [$this->course->slug, $previousLesson->slug]);
        }

        return 0;
    }

    public function markAsComplete()
    {
        if (!$this->currentLesson) return;
        $user = Auth::user();

        $isCompletedNow = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->exists();

        if (!$isCompletedNow) {
            DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $this->currentLesson->id)
                ->where('course_id', $this->course->id)
                ->update([
                    'count_completed' => DB::raw('count_completed + 1'),
                    'completed_at' => now()->timezone('Asia/Jakarta'),
                    'failed_attempts' => 0, // Reset gagal saat akhirnya lulus
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'Menyelesaikan Materi: ' . ($this->currentLesson->title ?? $this->currentLesson->name),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        $this->updateCourseProgress($user);
        $this->loadProgress();
        $this->goToNextLesson();
    }

    public function updateCourseProgress($user)
    {
        $totalLessons = $this->course->modules->flatMap->lessons->count();
        $completedCount = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->count();

        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        $user->courses()->updateExistingPivot($this->course->id, [
            'progress_percent' => $percent,
            'status' => $percent == 100 ? 'completed' : 'active',
            'completed_at' => $percent == 100 ? now()->timezone('Asia/Jakarta') : null,
        ]);

        // TAMBAHAN: Generate Sertifikat jika progress 100%
        if ($percent == 100) {
            $this->generateCertificate($user);
        }
    }

    /**
     * TAMBAHAN: GENERATE SERTIFIKAT OTOMATIS
     */
    public function generateCertificate($user)
    {
        // Cek apakah sertifikat sudah pernah dibuat untuk course ini
        $exists = Certificate::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->exists();

        if (!$exists) {
            // Setup Format Nomor Registrasi
            // Contoh: 00001/SALESMANSHIP-2/III/2026
            $nextId = (Certificate::max('id') ?? 0) + 1;
            $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT); 
            
            $courseCode = strtoupper(Str::slug($this->course->title));
            
            $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $month = $romans[date('n') - 1]; // Konversi bulan jadi romawi
            $year = date('Y');

            $certNumber = "{$paddedId}/{$courseCode}/{$month}/{$year}";

            Certificate::create([
                'user_id' => $user->id,
                'course_id' => $this->course->id,
                'certificate_number' => $certNumber,
                'issued_at' => now()->timezone('Asia/Jakarta'),
            ]);
        }
    }

    public function goToNextLesson()
    {
        $allLessons = $this->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->search(fn($l) => $l->id === $this->currentLesson->id);

        if ($currentIndex !== false && isset($allLessons[$currentIndex + 1])) {
            $nextLesson = $allLessons[$currentIndex + 1];
            return redirect()->route('course.player', [$this->course->slug, $nextLesson->slug]);
        }
    }
    

    public function render()
    {
        return view('livewire.course-player')->layout('layouts.learning');
    }
}