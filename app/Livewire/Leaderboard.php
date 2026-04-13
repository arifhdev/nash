<?php

namespace App\Livewire;

use App\Models\User;
use Livewire\Component;
use Livewire\Attributes\Title;

#[Title('Global Leaderboard - Akademi Satu Hati')]
class Leaderboard extends Component
{
    public function render()
    {
        // Ambil top 10 user berdasarkan XP tertinggi
        $topUsers = User::where('user_type', '!=', 'ahm')
            ->orderBy('total_xp', 'desc')
            ->orderBy('id', 'asc') // <-- TIE BREAKER: Jika seri, ID terkecil (User lama) ada di atas
            ->limit(10)
            ->get();

        return view('livewire.leaderboard', [
            'topUsers' => $topUsers
        ])->layout('layouts.app');
    }
}