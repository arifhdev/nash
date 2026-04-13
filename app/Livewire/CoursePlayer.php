<?php

namespace App\Livewire;

use App\Models\Course;
use App\Models\Lesson;
use App\Models\UserActivity;
use App\Models\Certificate;
use App\Enums\UserType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

class CoursePlayer extends Component
{
    public $course;
    public $currentLesson;
    public $lessonsCompletedIds = []; 
    
    // VARIABEL BARU UNTUK MENAMPUNG KUIS YANG SUDAH DIACAK
    public $preparedQuizzes = []; 

    public function mount($courseSlug, $lessonSlug = null)
    {
        // 1. AMBIL DATA COURSE DENGAN PROTEKSI BERLAPIS
        $this->course = Course::where('slug', $courseSlug)
            ->with(['modules' => function($q) {
                $q->orderBy('course_module.sort_order'); 
            }, 'modules.lessons' => function($q) {
                $q->where('lessons.is_active', true)
                  ->orderBy('lesson_module.sort_order');
            }, 'prerequisites']) 
            ->when(Auth::check(), function (Builder $query) {
                $user = Auth::user();

                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                } else {
                    $query->whereRaw('1 = 0'); 
                }

                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })
            ->firstOrFail();

        // PROTEKSI PRASYARAT
        if (Auth::check() && $this->course->isLockedForUser(Auth::user())) {
            session()->flash('error', 'Akses Ditolak! Anda harus menyelesaikan materi prasyarat terlebih dahulu.');
            $this->redirect(route('my.courses'), navigate: true); 
            return;
        }

        // UPDATE LAST ACCESSED COURSE
        if (Auth::check()) {
            $isEnrolled = Auth::user()->courses()->where('course_id', $this->course->id)->exists();
            if ($isEnrolled) {
                Auth::user()->courses()->updateExistingPivot($this->course->id, [
                    'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                ]);
            }
        }

        // SET CURRENT LESSON
        if ($lessonSlug) {
            $this->currentLesson = Lesson::where('slug', $lessonSlug)->firstOrFail();
        } else {
            $firstModule = $this->course->modules->first();
            $this->currentLesson = $firstModule ? $firstModule->lessons->first() : null;
        }

        // PERSIAPKAN DATA KUIS JIKA TIPE PELAJARAN ADALAH QUIZ
        if ($this->currentLesson && $this->currentLesson->type === 'quiz') {
            $this->prepareQuizzesForCurrentLesson();
        }

        // CATAT HISTORI VIEW LESSON
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

    /**
     * METHOD BARU: MEMPERSIAPKAN KUIS
     * Method ini akan mengambil data dari DB, mengacak soal, membatasi jumlah,
     * dan mengacak pilihan jawaban agar siap disajikan ke view.
     */
    private function prepareQuizzesForCurrentLesson()
    {
        $quizData = $this->currentLesson->quiz_data;
        if (empty($quizData)) {
            $this->preparedQuizzes = [];
            return;
        }

        $allQuizzes = collect($quizData);
        $limit = $this->currentLesson->quiz_display_count ?? $allQuizzes->count();
        
        // Acak urutan pertanyaan dan batasi jumlahnya
        $randomQuizzes = $allQuizzes->shuffle()->take($limit);

        $formattedQuizzes = $randomQuizzes->map(function ($quiz) {
            // Ambil teks dari opsi yang benar sebelum diacak
            $correctAnswerKey = 'option_' . strtolower($quiz['correct_answer']);
            $correctAnswerText = $quiz[$correctAnswerKey] ?? null;

            // Kumpulkan semua opsi ke dalam array
            $options = array_filter([
                $quiz['option_a'] ?? null,
                $quiz['option_b'] ?? null,
                $quiz['option_c'] ?? null,
                $quiz['option_d'] ?? null,
            ]);

            // Acak urutan opsi jawaban
            shuffle($options);

            return [
                'question' => $quiz['question'],
                'options' => $options, 
                'correct_answer_text' => $correctAnswerText, 
            ];
        })->values()->toArray();

        $this->preparedQuizzes = $formattedQuizzes;
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

    public function recordFailedAttempt()
    {
        $user = Auth::user();
        
        DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->update([
                'failed_attempts' => DB::raw('failed_attempts + 1'),
                'updated_at' => now()->timezone('Asia/Jakarta'),
            ]);

        $failedCount = DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->value('failed_attempts');

        if ($failedCount >= 3) {
            $this->resetPreviousLessonProgress();
            return route('course.player', [$this->course->slug, $this->getPreviousLesson()->slug ?? $this->currentLesson->slug]);
        }

        return $failedCount;
    }

    private function getPreviousLesson()
    {
        $allLessons = $this->course->modules->flatMap->lessons;
        $currentIndex = $allLessons->search(fn($l) => $l->id === $this->currentLesson->id);
        
        if ($currentIndex > 0) {
            return $allLessons[$currentIndex - 1];
        }
        return null;
    }

    public function resetPreviousLessonProgress()
    {
        $user = Auth::user();
        $previousLesson = $this->getPreviousLesson();

        DB::table('lesson_user')
            ->where('user_id', $user->id)
            ->where('lesson_id', $this->currentLesson->id)
            ->where('course_id', $this->course->id)
            ->update(['failed_attempts' => 0]);

        if ($previousLesson) {
            DB::table('lesson_user')
                ->where('user_id', $user->id)
                ->where('lesson_id', $previousLesson->id)
                ->where('course_id', $this->course->id)
                ->update([
                    'completed_at' => null,
                    'updated_at' => now()->timezone('Asia/Jakarta'),
                ]);
            
            $this->updateCourseProgress($user);

            UserActivity::create([
                'user_id' => $user->id,
                'activity_type' => 'Hukuman Gagal Kuis 3x: Reset Materi ' . $previousLesson->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            session()->flash('error', 'Gagal kuis 3x! Kamu wajib tonton ulang materi sebelumnya.');
        }
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
                    'failed_attempts' => 0, 
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

        if ($percent == 100) {
            $this->generateCertificate($user);
            
            if ($this->course->points_reward > 0 || $this->course->xp_reward > 0) {
                $alreadyRewarded = $user->pointHistories()
                    ->where('source_type', Course::class)
                    ->where('source_id', $this->course->id)
                    ->exists();
                    
                if (!$alreadyRewarded) {
                    $user->addReward(
                        $this->course->points_reward ?? 0, 
                        $this->course->xp_reward ?? 0, 
                        'Menyelesaikan Kursus: ' . $this->course->title, 
                        $this->course
                    );
                }
            }
        }
    }

    public function generateCertificate($user)
    {
        $exists = Certificate::where('user_id', $user->id)
            ->where('course_id', $this->course->id)
            ->exists();

        if (!$exists) {
            $nextId = (Certificate::max('id') ?? 0) + 1;
            $paddedId = str_pad($nextId, 5, '0', STR_PAD_LEFT); 
            
            $courseCode = strtoupper(Str::slug($this->course->title));
            
            $romans = ['I','II','III','IV','V','VI','VII','VIII','IX','X','XI','XII'];
            $month = $romans[date('n') - 1]; 
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