<?php

namespace App\Livewire\Frontend\Booklet;

use App\Models\Booklet;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Katalog Booklet - Akademi Satu Hati')]
class BookletList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.frontend.booklet.booklet-list', [
            'booklets' => Booklet::where('is_active', true)
                ->orderBy('created_at', 'desc')
                ->paginate(9) // Tampilkan 9 booklet per halaman
        ]);
    }
}