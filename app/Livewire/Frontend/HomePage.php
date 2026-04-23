<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Testimonial;
use App\Models\LandingPageSetting; 
use App\Enums\UserType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
#[Title('Beranda - Akademi Satu Hati')]
class HomePage extends Component
{
    public $selectedCategoryId = null;

    public function setCategory($id)
    {
        $this->selectedCategoryId = $id;
    }

    public function render()
    {
        // 1. Ambil Kategori aktif
        $categories = Category::where('is_active', true)->get();

        // 2. Query Kursus dengan proteksi Akses Penuh (Jabatan & Wilayah)
        $courses = Course::query()
            ->with('category')
            ->withCount('modules')
            ->where('is_active', true)
            ->when(auth()->check(), function (Builder $query) {
                $user = auth()->user();

                // A. Filter berdasarkan Jabatan (Position)
                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                }

                // B. Filter berdasarkan Wilayah Main Dealer 
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })
            ->when($this->selectedCategoryId, function ($query) {
                $query->where('category_id', $this->selectedCategoryId);
            })
            ->latest() // Urutkan dari yang terbaru
            ->take(6)  // <-- TAMBAHKAN INI UNTUK MEMBATASI HANYA 6 COURSE
            ->get();

        // 3. Query Event (5 Event terbaru untuk Bento Grid)
        $events = Event::where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        // 4. Query FAQ
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        // 5. Query Testimonial (Slider)
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->latest()
            ->get();

        // 6. Ambil Pengaturan Landing Page (Section "Mengapa Bergabung")
        $whyJoinSetting = LandingPageSetting::where('key', 'why_join_section')->first();

        return view('livewire.frontend.home-page', [
            'categories'   => $categories,
            'courses'      => $courses,
            'events'       => $events,
            'faqs'         => $faqs,
            'testimonials' => $testimonials,
            'whyJoinData'  => $whyJoinSetting ? $whyJoinSetting->payload : null,
        ]);
    }
}