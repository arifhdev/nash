<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Course;
use App\Models\Event;
use App\Models\Faq;
use App\Models\Testimonial; // Tambahkan Model Testimonial
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

// Mengatur Layout utama ke: resources/views/layouts/app.blade.php
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

        // 2. Query Kursus dengan penghitungan Modul otomatis
        $courses = Course::query()
            ->with('category')      // Eager load kategori
            ->withCount('modules')  // Hitung jumlah modul relasi secara otomatis
            ->where('is_active', true)
            ->when($this->selectedCategoryId, function ($query) {
                $query->where('category_id', $this->selectedCategoryId);
            })
            ->latest()
            ->get();

        // 3. Query Event
        // Mengambil 5 event terbaru yang aktif untuk mengisi layout Bento Grid
        $events = Event::where('is_active', true)
            ->orderBy('start_date', 'desc')
            ->take(5)
            ->get();

        // 4. Query FAQ
        // Ambil FAQ yang aktif dan urutkan sesuai kolom 'sort'
        $faqs = Faq::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->get();

        // 5. Query Testimonial (BARU)
        // Ambil testimonial aktif untuk slider di landing page
        $testimonials = Testimonial::where('is_active', true)
            ->orderBy('sort', 'asc')
            ->latest()
            ->get();

        return view('livewire.frontend.home-page', [
            'categories'   => $categories,
            'courses'      => $courses,
            'events'       => $events,
            'faqs'         => $faqs,
            'testimonials' => $testimonials, // Kirim variabel testimonials ke view
        ]);
    }
}