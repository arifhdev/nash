<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Livewire\Frontend\HomePage;
use App\Livewire\Frontend\CourseDetail;
use App\Livewire\Profile\Settings;
use App\Livewire\Auth\Login;
use App\Livewire\Auth\Register;
use App\Livewire\CoursePlayer;
use App\Livewire\MyCourses; 
use App\Livewire\Frontend\Event\EventList;
use App\Livewire\Frontend\Event\EventDetail;
use App\Livewire\Frontend\Course\CourseList;

/*
|--------------------------------------------------------------------------
| Guest Routes (Hanya untuk yang BELUM Login)
|--------------------------------------------------------------------------
| Jika user sudah login dan mencoba akses ini, otomatis redirect ke Home.
*/
Route::middleware(['guest'])->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register', Register::class)->name('register');
});

/*
|--------------------------------------------------------------------------
| Authenticated Routes (WAJIB Login)
|--------------------------------------------------------------------------
| Semua route di dalam grup ini TERKUNCI. 
| Jika belum login, user akan otomatis dilempar ke halaman /login.
*/
Route::middleware(['auth'])->group(function () {

    // 1. Dashboard / Home
    Route::get('/', HomePage::class)->name('home');
    
    // 2. Course Detail (Detail Kursus & Enrollment)
    Route::get('/course/{slug}', CourseDetail::class)->name('course.detail');

    // 3. Learning Management (Dashboard Progress Saya)
    // Route ini tetap pakai Closure karena hanya untuk menampilkan statistik summary
    Route::get('/my-learning', function () {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        // Ambil kursus untuk statistik Dashboard (Progress bar, XP, dll)
        $enrolledCourses = $user->courses()
            ->with('category')
            ->orderByPivot('updated_at', 'desc') 
            ->get();

        // Hitung Statistik
        $activeCount = $enrolledCourses->where('pivot.status', 'active')->count();
        $completedCount = $enrolledCourses->where('pivot.status', 'completed')->count();
        
        // Logika XP: Total Persentase Progress dikali 10
        $totalXP = $enrolledCourses->sum('pivot.progress_percent') * 10; 

        return view('frontend.learning.index', compact('enrolledCourses', 'activeCount', 'completedCount', 'totalXP')); 
    })->name('my-learning');

    // 4. My Courses (List Kursus Saya dengan Search & Pagination)
    // UPDATE: Sekarang menggunakan Livewire Component
    Route::get('/my-courses', MyCourses::class)->name('my-courses');

    // 5. Course Player (Halaman Belajar / Video Player)
    Route::get('/course/{courseSlug}/learn/{lessonSlug?}', CoursePlayer::class)
        ->name('course.player');

    // 6. Profile & Settings
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/settings', Settings::class)->name('settings');
    });

    Route::get('/events', EventList::class)->name('events.index');
    Route::get('/event/{slug}', EventDetail::class)->name('event.show');

    Route::get('/courses', CourseList::class)->name('courses.index');

    Route::get('/mandatory-courses', \App\Livewire\MandatoryCourses::class)->name('mandatory-courses');

    // 7. Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login'); 
    })->name('logout');

});