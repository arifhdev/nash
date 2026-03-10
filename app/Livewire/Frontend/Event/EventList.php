<?php

namespace App\Livewire\Frontend\Event;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Semua Event - Akademi Satu Hati')]
class EventList extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.frontend.event.event-list', [
            'events' => Event::where('is_active', true)
                ->orderBy('start_date', 'desc')
                ->paginate(9) // Tampilkan 9 event per halaman
        ]);
    }
}