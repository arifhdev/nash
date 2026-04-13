<?php

namespace App\Livewire\Frontend\Klhn;

use App\Models\KlhnEvent;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
class KlhnEventDetail extends Component
{
    public KlhnEvent $event;

    // Catatan: Gunakan $id karena di tabel belum ada 'slug'. 
    // Jika Anda sudah menambahkan kolom 'slug' ke migration KLHN, ganti $id menjadi $slug.
    public function mount($id) 
    {
        // Cari event beserta semua foto galeri di dalamnya
        $this->event = KlhnEvent::with('photos')
            ->where('id', $id) // Ganti 'id' menjadi 'slug' jika menggunakan slug
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
            
            ->firstOrFail(); 
    }

    public function render()
    {
        return view('livewire.frontend.klhn.klhn-event-detail')
            ->title($this->event->title . ' - Akademi Satu Hati');
    }
}