<?php

namespace App\Livewire\Frontend\Booklet;

use App\Models\Booklet;
use App\Enums\UserType;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

#[Layout('layouts.app')]
#[Title('Katalog Booklet - Akademi Satu Hati')]
class BookletList extends Component
{
    use WithPagination;

    public function render()
    {
        $booklets = Booklet::query()
            ->where('is_active', true)
            
            // PROTEKSI AKSES: Menggabungkan Filter Jabatan (Single) & Wilayah Main Dealer
            ->when(auth()->check(), function (Builder $query) {
                $user = auth()->user();

                // 1. FILTER JABATAN (Single Position)
                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                } else {
                    // Jika user tidak punya jabatan sama sekali, sembunyikan semua booklet
                    $query->whereRaw('1 = 0'); 
                }

                // 2. FILTER WILAYAH MAIN DEALER
                // Hanya berlaku untuk tipe karyawan Main Dealer dan Dealer.
                // Karyawan AHM otomatis bisa melihat semua booklet nasional.
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })
            ->orderBy('created_at', 'desc')
            ->paginate(9);

        return view('livewire.frontend.booklet.booklet-list', [
            'booklets' => $booklets
        ]);
    }
}