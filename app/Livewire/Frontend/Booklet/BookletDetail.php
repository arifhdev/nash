<?php

namespace App\Livewire\Frontend\Booklet;

use App\Models\Booklet;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class BookletDetail extends Component
{
    public Booklet $booklet;

    public function mount($slug)
    {
        // Cari booklet berdasarkan slug, jika tidak ada -> 404
        $this->booklet = Booklet::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    // Set Title halaman dinamis sesuai judul booklet
    public function render()
    {
        return view('livewire.frontend.booklet.booklet-detail')
            ->title($this->booklet->title . ' - Akademi Satu Hati');
    }
}