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

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone_number = $user->phone_number;
        $this->job_position = $user->job_position;
        $this->honda_id = $user->honda_id;
    }

    public function updateProfile()
    {
        $user = Auth::user();
        
        $validated = $this->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'phone_number' => 'nullable|string|max:20',
            'job_position' => 'nullable|string|max:100',
        ]);

        $user->update($validated);

        session()->flash('success', 'Profil Anda berhasil diperbarui.');
    }

    public function render()
    {
        return view('livewire.profile.settings')->layout('layouts.app');
    }
}