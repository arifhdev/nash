<?php

namespace App\Livewire;

use App\Models\Certificate;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class MyCertificates extends Component
{
    public function render()
    {
        // Ambil sertifikat berdasarkan user yang login, urutkan dari yang terbaru
        $certificates = Certificate::with('course')
            ->where('user_id', Auth::id())
            ->orderBy('issued_at', 'desc')
            ->get();

        return view('livewire.my-certificates', [
            'certificates' => $certificates
        ])->layout('layouts.app'); // Sesuaikan jika layout kamu namanya beda (misal: layouts.learning)
    }
}