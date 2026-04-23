<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureGoogleIsLinked
{
    public function handle(Request $request, Closure $next): Response
    {
        // Jika user sudah login, TAPI google_id-nya masih kosong (belum ditautkan)
        if (Auth::check() && is_null(Auth::user()->google_id)) {

            // PENTING: Kita harus mengecualikan beberapa rute agar tidak terjadi "Redirect Loop" (Error muter-muter)
            // User diizinkan mengakses halaman setting, route proses google, dan logout.
            $allowedRoutes = [
                'profile.settings',
                'auth.google',
                'logout'
            ];

            if (in_array($request->route()->getName(), $allowedRoutes) || $request->is('auth/google/callback')) {
                return $next($request);
            }

            // Jika dia mencoba buka halaman lain (misal Home/Course), tendang ke halaman Setting!
            return redirect()->route('profile.settings')
                ->with('error', 'WAJIB: Silakan tautkan akun Google Anda terlebih dahulu untuk bisa mengakses menu lainnya.');
        }

        return $next($request);
    }
}