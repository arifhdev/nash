<?php

namespace App\Livewire\Frontend;

use App\Models\Course;
use App\Models\UserActivity; // PENTING: Import model activity
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class CourseDetail extends Component
{
    public $course;
    public $slug;
    public $isEnrolled = false; // Properti untuk status pendaftaran

    public function mount($slug)
    {
        $this->slug = $slug;

        // 1. Ambil data course dengan relasi modul & lesson
        $this->course = Course::with(['category', 'modules.lessons'])
            ->where('slug', $this->slug)
            ->where('is_active', true)
            ->firstOrFail();

        // 2. Cek apakah User Login & Sudah Enroll?
        if (Auth::check()) {
            $this->isEnrolled = Auth::user()->courses()
                ->where('course_id', $this->course->id)
                ->exists();
        }
    }

    public function startLearning()
    {
        // 1. Wajib Login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // 2. Jika BELUM terdaftar, masukkan ke database (Enroll)
        if (!$this->isEnrolled) {
            Auth::user()->courses()->attach($this->course->id, [
                'progress_percent' => 0,
                'status' => 'active',
                'last_accessed_at' => now()->timezone('Asia/Jakarta'), // Catat akses pertama
                'created_at' => now()->timezone('Asia/Jakarta'),
                'updated_at' => now()->timezone('Asia/Jakarta'),
            ]);
            
            // --- LOG AKTIVITAS: ENROLL ---
            UserActivity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Enroll Kursus: ' . $this->course->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            // -----------------------------

            $this->isEnrolled = true;
            session()->flash('success', 'Berhasil mendaftar kursus!');
        } else {
            // Jika SUDAH terdaftar (Lanjut Belajar), update pivot last_accessed_at
            Auth::user()->courses()->updateExistingPivot($this->course->id, [
                'last_accessed_at' => now()->timezone('Asia/Jakarta'),
            ]);

            // --- LOG AKTIVITAS: LANJUT BELAJAR ---
            UserActivity::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Melanjutkan Kursus: ' . $this->course->title,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
            // -------------------------------------
        }

        // 3. Redirect ke Dashboard Pembelajaran (My Learning)
        // Jika Anda sudah punya rute khusus langsung ke player, bisa diganti ke:
        // return redirect()->route('course.player', ['courseSlug' => $this->course->slug]);
        return redirect()->route('my-learning');
    }

    public function render()
    {
        return view('livewire.frontend.course-detail', [
            'course' => $this->course
        ])->title(($this->course->title ?? 'Detail Kursus') . ' - Akademi Satu Hati');
    }
}