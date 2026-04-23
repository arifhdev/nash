<?php

namespace App\Livewire\Profile;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class Settings extends Component
{
    public $name;
    public $email;
    public $phone_number;
    public $job_position;
    public $honda_id;
    
    public $has_google_linked;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number; 
        $this->job_position = $user->job_position;
        $this->honda_id = $user->honda_id;
        
        $this->has_google_linked = !is_null($user->google_id);
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            // Email dihapus dari sini agar tidak bisa di-update manual
            'job_position' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        session()->flash('success', 'Profil Anda berhasil diperbarui.');
    }

    public function unlinkGoogle()
    {
        $user = Auth::user();
        
        $user->update([
            'google_id' => null,
            // Kita kosongkan emailnya juga kalau tautan dilepas, 
            // agar mereka tahu email itu "nempel" karena Google.
            'email' => null 
        ]);
        
        $this->email = null;
        $this->has_google_linked = false;
        
        session()->flash('success', 'Tautan akun Google berhasil dilepas.');
    }

    public function render()
    {
        return view('livewire.profile.settings')->layout('layouts.app');
    }
}