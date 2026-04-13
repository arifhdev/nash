<?php

namespace App\Livewire\Frontend\Broadcast;

use App\Models\LiveBroadcast;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class LiveBroadcastDetail extends Component
{
    public LiveBroadcast $broadcast;

    public function mount($id)
    {
        $this->broadcast = LiveBroadcast::findOrFail($id);
    }

    public function render()
    {
        return view('livewire.frontend.broadcast.live-broadcast-detail')
            ->title($this->broadcast->title . ' - Akademi Satu Hati');
    }
}