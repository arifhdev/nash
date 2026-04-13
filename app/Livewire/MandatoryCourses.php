<?php

namespace App\Livewire;

use App\Models\Course;
use App\Enums\UserType; // Pastikan Enum ini di-import
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

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
        
        // Ambil ID jabatan user saat ini (Single Position)
        $positionId = $user->position_id;

        $courses = Course::query()
            // --- PROTEKSI 1: FILTER JABATAN WAJIB (MANDATORY) ---
            ->where(function (Builder $query) use ($positionId) {
                if ($positionId) {
                    $query->whereHas('mandatoryPositions', function (Builder $q) use ($positionId) {
                        $q->where('positions.id', $positionId);
                    });
                } else {
                    // Jika user tidak punya jabatan, berarti tidak ada course wajib untuknya
                    $query->whereRaw('1 = 0');
                }
            })

            // --- PROTEKSI 2: FILTER WILAYAH MAIN DEALER ---
            // Hanya berlaku untuk Karyawan MD & Dealer. Karyawan AHM bebas wilayah.
            ->when($user, function (Builder $query) use ($user) {
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })

            // --- FILTER SEARCH ---
            ->when($this->search, function ($query) {
                // Tambahkan prefix 'courses.' agar tidak ambiguous
                $query->where('courses.title', 'like', '%' . $this->search . '%');
            })
            
            ->where('courses.is_active', true)
            ->with([
                'users' => function ($query) use ($user) {
                    $query->where('users.id', $user->id);
                }, 
                'category', 
                'modules',
                'prerequisites'
            ])
            ->paginate(10);

        return view('livewire.mandatory-courses', [
            'courses' => $courses,
        ])
        ->layout('layouts.app') 
        ->title('Course Wajib');
    }
}