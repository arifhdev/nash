<?php

namespace App\Livewire\Frontend;

use App\Models\Course;
use App\Models\UserActivity;
use App\Enums\UserType; // Jangan lupa import Enum ini
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
class CourseDetail extends Component
{
    public $course;
    public $slug;
    public $isEnrolled = false;

    public function mount($slug)
    {
        $this->slug = $slug;

        // 1. Ambil data course dengan PROTEKSI BERLAPIS (Main Dealer + Jabatan Tunggal)
        $this->course = Course::with(['category', 'modules.lessons', 'prerequisites'])
            ->where('slug', $this->slug)
            ->where('is_active', true)
            ->when(Auth::check(), function (Builder $query) {
                $user = Auth::user();

                // PROTEKSI 1: FILTER JABATAN (Single Position)
                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                } else {
                    // Jika user tidak punya jabatan, blokir akses (1 = 0)
                    $query->whereRaw('1 = 0'); 
                }

                // PROTEKSI 2: FILTER WILAYAH MAIN DEALER
                // Hanya berlaku untuk Main Dealer & Dealer. Karyawan AHM akan dilewatkan.
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })
            ->firstOrFail(); // Lempar 404 jika tidak sesuai kriteria akses

        // 2. Cek status pendaftaran
        if (Auth::check()) {
            $this->isEnrolled = Auth::user()->courses()
                ->where('course_id', $this->course->id)
                ->exists();
        }
    }

    public function startLearning()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // PROTEKSI TAMBAHAN: Cegah Bypass Enroll jika course masih Terkunci (Prasyarat belum selesai)
        if ($this->course->isLockedForUser(Auth::user())) {
            session()->flash('error', 'Anda tidak dapat mendaftar. Silakan selesaikan Course Prasyarat terlebih dahulu.');
            return redirect()->back();
        }
        
        if (!$this->isEnrolled) {
            Auth::user()->courses()->attach($this->course->id, [
                'progress_percent' => 0,
                'status' => 'active',
                'last_accessed_at' => now()->timezone('Asia/Jakarta'),
                'created_at' => now()->timezone('Asia/Jakarta'),
                'updated_at' => now()->timezone('Asia/Jakarta'),
            ]);
            
            UserActivity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Enroll Kursus: ' . $this->course->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);

            $this->isEnrolled = true;
            session()->flash('success', 'Berhasil mendaftar kursus!');
        } else {
            Auth::user()->courses()->updateExistingPivot($this->course->id, [
                'last_accessed_at' => now()->timezone('Asia/Jakarta'),
            ]);

            UserActivity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Melanjutkan Kursus: ' . $this->course->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        }

        return redirect()->route('my-learning');
    }

    public function render()
    {
        return view('livewire.frontend.course-detail', [
            'course' => $this->course
        ])->title(($this->course->title ?? 'Detail Kursus') . ' - Akademi Satu Hati');
    }
}