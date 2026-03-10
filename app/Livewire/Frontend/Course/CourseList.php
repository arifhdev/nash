<?php

namespace App\Livewire\Frontend\Course;

use App\Models\Category;
use App\Models\Course;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

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
        $this->category = $id === $this->category ? null : $id;
        $this->resetPage(); // Reset ke halaman 1 saat ganti filter
    }

    public function updatedSearch()
    {
        $this->resetPage(); // Reset ke halaman 1 saat searching
    }

    public function render()
    {
        $categories = Category::where('is_active', true)->get();

        $courses = Course::query()
            ->with(['category'])
            ->withCount('modules')
            ->where('is_active', true)
            ->when($this->search, function ($query) {
                $query->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($query) {
                $query->where('category_id', $this->category);
            })
            ->latest()
            ->paginate(9); // 9 Item per halaman

        return view('livewire.frontend.course.course-list', [
            'categories' => $categories,
            'courses' => $courses
        ]);
    }
}