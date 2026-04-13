<?php

namespace App\Livewire\Frontend\Course;

use App\Models\Category;
use App\Models\Course;
use App\Enums\UserType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
#[Title('Semua Kursus - Akademi Satu Hati')]
class CourseList extends Component
{
    use WithPagination;

    #[Url] 
    public $search = '';

    #[Url]
    public $category = null;

    public function setCategory($id)
    {
        $this->category = ($id == $this->category) ? null : $id;
        $this->resetPage(); 
    }

    public function updatedSearch()
    {
        $this->resetPage(); 
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $courses = Course::query()
            ->with(['category', 'prerequisites']) 
            ->withCount('modules')
            ->where('is_active', true)
            
            // PROTEKSI AKSES: Menggabungkan Filter Jabatan (Single) & Wilayah Main Dealer
            ->when(auth()->check(), function (Builder $query) {
                $user = auth()->user();

                // 1. FILTER JABATAN (Single Position)
                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                } else {
                    // Jika user tidak punya jabatan sama sekali, sembunyikan semua course
                    $query->whereRaw('1 = 0'); 
                }

                // 2. FILTER WILAYAH MAIN DEALER
                // Hanya berlaku untuk Main Dealer & Dealer. Karyawan AHM akan dilewatkan.
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })

            // 3. FILTER SEARCH
            ->when($this->search, function (Builder $query) {
                $query->where(function(Builder $q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })

            // 4. FILTER KATEGORI
            ->when($this->category, function (Builder $query) {
                $query->where('category_id', $this->category);
            })
            ->latest()
            ->paginate(9);

        return view('livewire.frontend.course.course-list', [
            'categories' => $categories,
            'courses' => $courses
        ]);
    }
}