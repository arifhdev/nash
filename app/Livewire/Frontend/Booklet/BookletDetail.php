<?php

namespace App\Livewire\Frontend\Booklet;

use App\Models\Booklet;
use App\Enums\UserType;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

#[Layout('layouts.app')]
class BookletDetail extends Component
{
    public Booklet $booklet;

    public function mount($slug)
    {
        // Cari booklet berdasarkan slug dengan proteksi ganda (Jabatan Tunggal & Wilayah)
        $this->booklet = Booklet::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->when(Auth::check(), function (Builder $query) {
                $user = Auth::user();

                // 1. PROTEKSI JABATAN (Single Position)
                if ($user->position_id) {
                    $query->whereHas('positions', function (Builder $q) use ($user) {
                        $q->where('positions.id', $user->position_id);
                    });
                } else {
                    // Jika user tidak punya jabatan, blokir akses
                    $query->whereRaw('1 = 0'); 
                }

                // 2. PROTEKSI WILAYAH MAIN DEALER
                $userTypeValue = $user->user_type instanceof UserType ? $user->user_type->value : $user->user_type;
                
                if (in_array($userTypeValue, ['main_dealer', 'dealer']) && $user->main_dealer_id) {
                    $query->whereHas('mainDealers', function (Builder $q) use ($user) {
                        $q->where('main_dealers.id', $user->main_dealer_id);
                    });
                }
            })
            ->firstOrFail(); 
    }

    /**
     * Helper untuk mengubah link YouTube biasa menjadi link embed (iframe)
     */
    public function getYoutubeEmbedUrl(?string $url): ?string
    {
        if (!$url) return null;

        // Ekstrak ID dari URL (mendukung watch?v=, youtu.be/, dan format lainnya)
        preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/i', $url, $match);
        
        $videoId = $match[1] ?? null;

        return $videoId ? "https://www.youtube.com/embed/{$videoId}" : null;
    }

    public function render()
    {
        return view('livewire.frontend.booklet.booklet-detail')
            ->title($this->booklet->title . ' - Akademi Satu Hati');
    }
}