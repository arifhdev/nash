<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')] // Sesuaikan dengan nama layout Anda
#[Title('Kursus Saya')]
class MyCourses extends Component
{
    use WithPagination;

    public $search = '';

    // Reset halaman ke 1 setiap kali user mengetik di search bar
    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();

        // Query mengambil course milik user (Relasi Many-to-Many)
        $courses = $user->courses()
            // --- UPDATE DI SINI: Tambahkan 'prerequisites' ---
            ->with(['category', 'modules', 'prerequisites']) 
            // PROTEKSI: Pastikan course ini MASIH diizinkan untuk Main Dealer user
            ->when($user->main_dealer_id, function (Builder $query) use ($user) {
                $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                    $q->where('main_dealers.id', $user->main_dealer_id);
                });
            })
            // PASTIKAN juga course-nya masih berstatus aktif (opsional tapi disarankan)
            ->where('courses.is_active', true) 
            ->where(function($query) {
                // Logic search judul course (Sudah aman karena dibungkus kurung)
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            // Urutkan berdasarkan tanggal enroll (terbaru diambil)
            ->orderByPivot('created_at', 'desc') 
            ->paginate(6); // Tampilkan 6 per halaman

        return view('livewire.my-courses', [
            'courses' => $courses
        ]);
    }
}