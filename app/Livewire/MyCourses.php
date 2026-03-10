<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('layouts.app')] // Sesuaikan dengan nama layout Anda (misal: components.layouts.app atau layouts.app)
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
            ->with(['category', 'modules']) // Eager load biar ringan
            ->where(function($query) {
                // Logic search judul course
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