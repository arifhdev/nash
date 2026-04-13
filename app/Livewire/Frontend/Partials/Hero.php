<?php

namespace App\Livewire\Frontend\Partials;

use App\Models\HeroSlide;
use App\Models\Lesson;
use App\Models\User;
use Livewire\Component;

class Hero extends Component
{
    // Properti untuk menampung inputan pencarian dari user
    public $searchQuery = '';

    /**
     * Fungsi untuk menangani aksi klik tombol "Cari" atau tekan Enter
     */
    public function submitSearch()
    {
        // Pastikan tidak kosong agar URL rapi (tidak mengirim ?search= kosong)
        if (!empty(trim($this->searchQuery))) {
            // Lempar ke halaman courses.index dengan parameter pencarian
            return $this->redirectRoute('courses.index', ['search' => $this->searchQuery], navigate: true);
        }

        // Jika kosong tapi dipencet cari, lemparkan ke halaman course list biasa
        return $this->redirectRoute('courses.index', navigate: true);
    }

    public function render()
    {
        // Mengambil data slide yang aktif
        $slides = HeroSlide::where('is_active', true)
            ->orderBy('sort_order', 'asc')
            ->get();

        // Mengambil total materi (lesson) yang berstatus aktif
        $totalLessons = Lesson::where('is_active', true)->count();

        // Mengambil total jumlah seluruh user
        $activeMembers = User::count();

        return view('livewire.frontend.partials.hero', [
            'slides' => $slides,
            'totalLessons' => $totalLessons,
            'activeMembers' => $activeMembers,
        ]);
    }
}