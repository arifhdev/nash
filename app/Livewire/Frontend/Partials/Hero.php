<?php

namespace App\Livewire\Frontend\Partials;

use App\Models\HeroSlide;
use Livewire\Component;

class Hero extends Component
{
    public function render()
    {
        // Mengambil data slide yang aktif dan diurutkan berdasarkan sort_order
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        return view('livewire.frontend.partials.hero', [
            'slides' => $slides
        ]);
    }
}