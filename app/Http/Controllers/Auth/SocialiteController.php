<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            return redirect()->route('profile.settings')->with('error', 'Gagal mengambil data dari Google.');
        }

        $user = Auth::user();

        // SKENARIO 1: USER SEDANG LOGIN (Proses Tautkan Akun)
        if ($user) {
            // Cek apakah Google ID ini sudah dipakai orang lain
            $exists = User::where('google_id', $googleUser->id)->where('id', '!=', $user->id)->first();
            
            if ($exists) {
                return redirect()->route('profile.settings')->with('error', 'Akun Google ini sudah ditautkan ke pengguna lain.');
            }

            // Simpan data Google ke user saat ini
            $user->update([
                'google_id' => $googleUser->id,
                'email' => $googleUser->email, // Email jadi valid karena dari Google
            ]);

            return redirect()->route('profile.settings')->with('success', 'Akun Google berhasil ditautkan!');
        }

        // SKENARIO 2: USER TIDAK LOGIN (Proses Login SSO)
        $existingUser = User::where('google_id', $googleUser->id)->first();

        if ($existingUser) {
            Auth::login($existingUser);
            return redirect()->intended(route('home'));
        }

        // Jika tidak ada user dan tidak sedang login, tolak (Sesuai ide kita: Ditolak jika belum daftar WA)
        return redirect()->route('login')->with('status', 'Akun Google Anda belum terdaftar. Silakan login via WhatsApp terlebih dahulu.');
    }
}