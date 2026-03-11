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
use App\Livewire\Frontend\CertificateVerification;
use App\Livewire\MyCertificates;
use App\Models\Certificate;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Livewire\Frontend\Booklet\BookletList;
use App\Livewire\Frontend\Booklet\BookletDetail;

/*
|--------------------------------------------------------------------------
| Public Routes (Bisa diakses SIAPA SAJA tanpa login)
|--------------------------------------------------------------------------
*/
// Route Publik untuk Cek Sertifikat (Bisa diakses HRD/Umum)
Route::get('/verifikasi-sertifikat', CertificateVerification::class)->name('certificate.verify');


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
    Route::get('/my-learning', function () {
        $user = Auth::user();

        // Ambil kursus untuk statistik Dashboard
        $enrolledCourses = $user->courses()
            ->with('category')
            ->orderByPivot('updated_at', 'desc') 
            ->get();

        // Hitung Statistik
        $activeCount = $enrolledCourses->where('pivot.status', 'active')->count();
        
        // PERBAIKAN: Hitung jumlah sertifikat langsung dari database sertifikat
        $completedCount = \App\Models\Certificate::where('user_id', $user->id)->count();
        
        // Logika XP: Total Persentase Progress dikali 10
        $totalXP = $enrolledCourses->sum('pivot.progress_percent') * 10; 

        return view('frontend.learning.index', compact('enrolledCourses', 'activeCount', 'completedCount', 'totalXP')); 
    })->name('my-learning');

    // 4. My Courses (List Kursus Saya dengan Search & Pagination)
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

    // 7. Sertifikat Saya
    Route::get('/my-certificates', MyCertificates::class)->name('my-certificates');

    // 8. DOWNLOAD SERTIFIKAT (Route yang hilang tadi)
    Route::get('/certificate/download/{certNumber}', function ($certNumber) {
        $certificate = Certificate::where('certificate_number', $certNumber)
                        ->with(['user', 'course'])
                        ->firstOrFail();

        // Pastikan HANYA pemilik sertifikat yang bisa mendownloadnya
        if (Auth::id() !== $certificate->user_id) {
            abort(403, 'Unauthorized. Anda tidak memiliki akses ke sertifikat ini.');
        }

        $pdf = Pdf::loadView('pdf.certificate', compact('certificate'))
                  ->setPaper('a4', 'landscape'); 
        
        return $pdf->stream('Sertifikat-' . \Illuminate\Support\Str::slug($certificate->course->title) . '.pdf'); 
    })->name('certificate.download')->where('certNumber', '.*');

    // 9. Logout
    Route::post('/logout', function () {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login'); 
    })->name('logout');

    // 10. Booklet
    Route::get('/booklets', BookletList::class)->name('booklets.index');
    Route::get('/booklet/{slug}', BookletDetail::class)->name('booklet.show');

});