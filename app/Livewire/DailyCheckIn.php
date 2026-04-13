<?php

namespace App\Livewire;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\GamificationSetting; // Jangan lupa import model ini

class DailyCheckIn extends Component
{
    public $alreadyCheckedIn = false;
    public $pointsToReward = 0;
    public $xpToReward = 0;

    public function mount()
    {
        // 1. Ambil pengaturan dari database (Jika belum disetting admin, otomatis buatkan default 0 poin & 10 XP)
        $setting = GamificationSetting::firstOrCreate(
            ['id' => 1],
            ['daily_checkin_points' => 0, 'daily_checkin_xp' => 10]
        );

        // 2. Simpan ke variabel publik
        $this->pointsToReward = $setting->daily_checkin_points;
        $this->xpToReward = $setting->daily_checkin_xp;

        // 3. Cek status check-in hari ini
        $this->checkStatus();
    }

    public function checkStatus()
    {
        $user = auth()->user();
        $this->alreadyCheckedIn = $user->pointHistories()
            ->where('description', 'Daily Check-in')
            ->whereDate('created_at', Carbon::today())
            ->exists();
    }

    public function claim()
    {
        if ($this->alreadyCheckedIn) {
            return;
        }

        $user = auth()->user();
        
        // Berikan reward Poin dan XP sesuai angka di Filament!
        $user->addReward($this->pointsToReward, $this->xpToReward, 'Daily Check-in');

        $this->alreadyCheckedIn = true;

        // Beri tahu halaman agar mengupdate total Poin dan XP di atas secara bersamaan
        $this->dispatch('points-updated', points: $this->pointsToReward, xp: $this->xpToReward);
    }

    public function render()
    {
        return view('livewire.daily-check-in');
    }
}