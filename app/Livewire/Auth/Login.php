<?php

namespace App\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class Login extends Component
{
    // --- Properties Form ---
    public $phone_number;
    public $remember = false;

    // --- Properties OTP ---
    public $step = 1; // 1: Input Nomor WA, 2: Input OTP
    public $otp_input;

    public function requestOtp()
    {
        // 1. Validasi pastikan nomor WA diisi dan terdaftar di database
        $this->validate([
            'phone_number' => 'required|string|exists:users,phone_number',
        ], [
            'phone_number.required' => 'Nomor WhatsApp wajib diisi.',
            'phone_number.exists' => 'Nomor WhatsApp ini belum terdaftar.',
        ]);

        // 2. Ambil data user
        $user = User::where('phone_number', $this->phone_number)->first();

        // 3. Generate OTP dan simpan ke Session
        $otpCode = rand(100000, 999999);
        Session::put('login_otp', $otpCode);
        Session::put('login_phone', $this->phone_number);

        // 4. Kirim WA
        $this->sendWhatsAppNotification($user->name, $this->phone_number, $otpCode);

        // 5. Pindah ke halaman input OTP
        $this->step = 2;
    }

    public function verifyOtp()
    {
        $this->validate([
            'otp_input' => 'required|numeric'
        ]);

        $savedOtp = Session::get('login_otp');
        $savedPhone = Session::get('login_phone');

        // Cek apakah OTP yang diketik sama dengan yang di Session
        if ($this->otp_input == $savedOtp) {
            
            $user = User::where('phone_number', $savedPhone)->first();

            if ($user) {
                // Login Berhasil!
                Auth::login($user, $this->remember);
                
                // Bersihkan session OTP
                Session::forget(['login_otp', 'login_phone']);
                session()->regenerate();

                return redirect()->intended(route('home'));
            }
        }

        // Kalau OTP salah
        $this->addError('otp_input', 'Kode OTP tidak valid atau salah.');
    }

    private function sendWhatsAppNotification($name, $phoneNumber, $otpCode)
    {
        $token = config('services.ruangwa.token');
        $cleanPhone = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (strpos($cleanPhone, '0') === 0) {
            $cleanPhone = '62' . substr($cleanPhone, 1);
        }

        $message = "Halo {$name},\n\nKode OTP Anda untuk *LOGIN* ke Akademi Satu Hati adalah: *{$otpCode}*\n\nKode ini bersifat rahasia. Jangan berikan kode ini kepada siapa pun.\n\nSalam,\n*Akademi Satu Hati*";

        try {
            $response = Http::withHeaders([
                'User-Agent' => 'Mozilla/5.0',
            ])->asForm()->post('https://app.ruangwa.id/api/send_message', [
                'token' => $token,
                'number' => $cleanPhone,
                'message' => $message,
            ]);

            if (!$response->successful()) {
                Log::error('Ruangwa API Failed (Login): ' . $response->body());
            } else {
                Log::info("WA OTP Login Berhasil Dikirim ke {$cleanPhone}");
            }

        } catch (\Exception $e) {
            Log::error('Ruangwa Connection Error (Login): ' . $e->getMessage());
        }
    }

    // Fungsi tambahan untuk tombol Google SSO
    public function redirectToGoogle()
    {
        return redirect()->route('auth.google');
    }

    public function render()
    {
        return view('livewire.auth.login')->layout('layouts.guest');
    }
}