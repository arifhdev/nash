<?php

namespace App\Livewire\Frontend\Klhn;

use App\Models\KlhnEvent;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
#[Title('Galeri Event KLHN - Akademi Satu Hati')]
class KlhnEventList extends Component
{
    use WithPagination;

    public function render()
    {
        // Mengambil data event beserta 1 foto pertama untuk dijadikan Thumbnail di halaman List
        $events = KlhnEvent::with(['photos' => function($q) {
                $q->latest()->limit(1); // Ambil 1 foto saja untuk cover
            }])
            // ->where('is_active', true) // UNCOMMENT JIKA ADA KOLOM is_active
            
            /* --- UNCOMMENT BAGIAN INI JIKA KLHN BUTUH PROTEKSI SEPERTI BOOKLET ---
            ->when(auth()->check() && auth()->user()->main_dealer_id, function (Builder $query) {
                $query->whereHas('mainDealers', function (Builder $q) {
                    $q->where('main_dealers.id', auth()->user()->main_dealer_id);
                });
            })
            ->when(auth()->check(), function (Builder $query) {
                $positionIds = auth()->user()->positions->pluck('id')->toArray();
                if (empty($positionIds)) {
                    $query->whereRaw('1 = 0'); 
                } else {
                    $query->whereHas('positions', function (Builder $q) use ($positionIds) {
                        $q->whereIn('positions.id', $positionIds);
                    });
                }
            })
            ---------------------------------------------------------------------- */
            
            ->orderBy('event_date', 'desc')
            ->paginate(9);

        return view('livewire.frontend.klhn.klhn-event-list', [
            'events' => $events
        ]);
    }
}