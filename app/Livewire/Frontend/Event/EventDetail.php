<?php

namespace App\Livewire\Frontend\Event;

use App\Models\Event;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class EventDetail extends Component
{
    public Event $event;

    public function mount($slug)
    {
        // Cari event berdasarkan slug, jika tidak ada -> 404
        $this->event = Event::where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();
    }

    // Set Title halaman dinamis sesuai judul event
    public function render()
    {
        return view('livewire.frontend.event.event-detail')
            ->title($this->event->title . ' - Akademi Satu Hati');
    }
}