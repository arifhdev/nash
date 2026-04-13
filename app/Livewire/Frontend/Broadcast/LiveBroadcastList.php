<?php

namespace App\Livewire\Frontend\Broadcast;

use App\Models\LiveBroadcast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
#[Title('Live Broadcast - Akademi Satu Hati')]
class LiveBroadcastList extends Component
{
    use WithPagination;

    public function render()
    {
        // Mengurutkan agar tayangan LIVE dan UPCOMING berada di atas, ENDED di bawah
        $broadcasts = LiveBroadcast::orderByRaw("FIELD(status, 'live', 'upcoming', 'ended')")
            ->orderBy('scheduled_at', 'desc')
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.frontend.broadcast.live-broadcast-list', [
            'broadcasts' => $broadcasts
        ]);
    }
}