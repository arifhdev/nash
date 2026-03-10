<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Login extends Component
{
    public $email;
    public $password;
    public $remember = false; // Tambahkan property Remember Me

    public function login()
    {
        $validated = $this->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        // Tambahkan $this->remember ke Auth::attempt
        if (Auth::attempt($validated, $this->remember)) {
            session()->regenerate();
            return redirect()->intended(route('home'));
        }

        $this->addError('email', 'Email atau password salah.');
    }

    public function render()
    {
        // Ganti layout ke 'layouts.guest' agar bersih
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}