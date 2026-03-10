<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CoursePlayer extends Component
{
    public $course;
    public $currentLesson;
    public $lessonsCompletedIds = []; 

    /**
     * Dijalankan otomatis saat halaman pertama kali dibuka.
     */
    public function mount($courseSlug, $lessonSlug = null)
    {
        // 1. Ambil Data Course + Module + Lesson
        $this->course = Course::where('slug', $courseSlug)
            ->with(['modules' => function($q) {
                $q->orderBy('course_module.sort_order'); 
            }, 'modules.lessons' => function($q) {
                $q->where('lessons.is_active', true)
                  ->orderBy('lesson_module.sort_order');
            }])
            ->firstOrFail();

        // 2. Validasi Akses dan Tracking Level Course
        if (Auth::check()) {
            $isEnrolled = Auth::user()->courses()->where('course_id', $this->course->id)->exists();
            
            if ($isEnrolled) {
                Auth::user()->courses()->updateExistingPivot($this->course->id, [
                    'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                ]);
            }
        }

        // 3. Tentukan Lesson mana yang sedang dibuka
        if ($lessonSlug) {
            $this->currentLesson = Lesson::where('slug', $lessonSlug)->firstOrFail();
        } else {
            $firstModule = $this->course->modules->first();
            $this->currentLesson = $firstModule ? $firstModule->lessons->first() : null;
        }

        // --- LOGIC TRACKING PER-LESSON DENGAN INCREMENT VIEW ---
        if (Auth::check() && $this->currentLesson) {
            $user = Auth::user();
            
            // Cek langsung ke database agar tidak terhalang filter relasi
            $lessonPivot = DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $this->currentLesson->id)
                ->where('course_id', $this->course->id)
                ->first();

            if (!$lessonPivot) {
                // Jika BELUM pernah buka, catat waktu mulai & set count_view ke 1
                DB::table('lesson_user')->insert([
                    'user_id' => $user->id,
                    'lesson_id' => $this->currentLesson->id,
                    'course_id' => $this->course->id,
                    'count_view' => 1,
                    'count_completed' => 0,
                    'started_at' => now()->timezone('Asia/Jakarta'),
                    'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                    'created_at' => now()->timezone('Asia/Jakarta'),
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);
            } else {
                // Jika SUDAH pernah buka, Increment count_view dan update last_accessed_at
                DB::table('lesson_user')
                    ->where('id', $lessonPivot->id)
                    ->update([
                        'count_view' => DB::raw('count_view + 1'), // Auto tambah 1 tiap buka halaman
                        'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                        'updated_at' => now()->timezone('Asia/Jakarta'),
                    ]);
            }
        }
        // ----------------------------------------------

        // 4. Load Progress User (Untuk Tanda Centang Hijau di Sidebar)
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
     * Logic tombol "Tandai Selesai"
     */
    public function markAsComplete()
    {
        if (!$this->currentLesson) return;
        
        $user = Auth::user();

        // --- UPDATE STATUS SELESAI & INCREMENT COMPLETED ---
        // Cek apakah lesson ini sedang dalam posisi "belum selesai" di siklus saat ini
        $isCompletedNow = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->exists();

        // Hanya update completed_at dan increment count_completed JIKA belum diselesaikan di siklus ini
        if (!$isCompletedNow) {
            DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $this->currentLesson->id)
                ->where('course_id', $this->course->id)
                ->update([
                    'count_completed' => DB::raw('count_completed + 1'), // Tambah riwayat kelulusan lesson ini
                    'completed_at' => now()->timezone('Asia/Jakarta'),
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);

            // Catat Log Aktivitas hanya saat pertama kali klik Selesai di siklus ini
            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'Menyelesaikan Materi: ' . ($this->currentLesson->title ?? $this->currentLesson->name),
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }
        // ---------------------------------------------------

        // Update Progress Bar Total (course_user) -> Rapor Akhir
        $this->updateCourseProgress($user);

        // Update Local State (Agar UI langsung berubah hijau tanpa refresh)
        if (!in_array($this->currentLesson->id, $this->lessonsCompletedIds)) {
            $this->lessonsCompletedIds[] = $this->currentLesson->id;
        }

        // Redirect ke Materi Selanjutnya
        $this->goToNextLesson();
    }

    /**
     * Hitung ulang persentase progress kursus
     */
    public function updateCourseProgress($user)
    {
        $totalLessons = 0;
        foreach($this->course->modules as $mod) {
            $totalLessons += $mod->lessons->count();
        }

        // Hitung jumlah lesson yang SUDAH SELESAI saja
        $completedCount = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->whereNotNull('completed_at')
            ->count();

        // Hitung Persentase
        $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

        // Update tabel pivot course_user
        $user->courses()->updateExistingPivot($this->course->id, [
            'progress_percent' => $percent,
            'status' => $percent == 100 ? 'completed' : 'active',
            'completed_at' => $percent == 100 ? now()->timezone('Asia/Jakarta') : null,
        ]);

        // --- LOG AKTIVITAS: LULUS KURSUS (Jika 100%) ---
        if ($percent == 100) {
            $alreadyLogged = UserActivity::where('user_id', $user->id)
                ->where('activity_type', 'Lulus Kursus: ' . $this->course->title)
                ->exists();

            if (!$alreadyLogged) {
                UserActivity::create([
                    'user_id' => $user->id,
                    'activity_type' => 'Lulus Kursus: ' . $this->course->title,
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        }
    }

    /**
     * Cari dan redirect ke lesson berikutnya
     */
    public function goToNextLesson()
    {
        $allLessons = $this->course->modules->pluck('lessons')->flatten();
        
        $currentIndex = $allLessons->search(function($lesson) {
            return $lesson->id === $this->currentLesson->id;
        });

        if ($currentIndex !== false && isset($allLessons[$currentIndex + 1])) {
            $nextLesson = $allLessons[$currentIndex + 1];
            
            return redirect()->route('course.player', [
                'courseSlug' => $this->course->slug, 
                'lessonSlug' => $nextLesson->slug
            ]);
        }
    }

    public function render()
    {
        return view('livewire.course-player')
            ->layout('layouts.learning');
    }
}