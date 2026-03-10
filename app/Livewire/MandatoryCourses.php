<?php

namespace App\Livewire;

use App\Models\Course;
use Livewire\Component;
use Livewire\WithPagination;

class MandatoryCourses extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = auth()->user();
        
        // Ambil ID jabatan user saat ini
        $positionIds = $user->positions->pluck('id')->toArray();

        // Cari course yang punya relasi ke jabatan user tersebut
        $courses = Course::whereHas('positions', function ($query) use ($positionIds) {
                $query->whereIn('positions.id', $positionIds);
            })
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%');
            })
            ->where('is_active', true)
            ->with(['users' => function ($query) use ($user) {
                $query->where('users.id', $user->id);
            }, 'category', 'modules'])
            ->paginate(10);

        return view('livewire.mandatory-courses', [
            'courses' => $courses,
        ])
        ->layout('layouts.app') // <--- TAMBAHKAN BARIS INI (Sesuaikan jika nama file layout Bapak berbeda)
        ->title('Course Wajib');
    }
}