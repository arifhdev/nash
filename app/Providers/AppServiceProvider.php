<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\PasswordReset;
use App\Models\UserActivity;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // 1. Mencatat saat User Login
        Event::listen(function (Login $event) {
            UserActivity::create([
                'user_id' => $event->user->id,
                'activity_type' => 'Login',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        // 2. Mencatat saat User Logout
        Event::listen(function (Logout $event) {
            if ($event->user) {
                UserActivity::create([
                    'user_id' => $event->user->id,
                    'activity_type' => 'Logout',
                    'ip_address' => request()->ip(),
                    'user_agent' => request()->userAgent(),
                ]);
            }
        });

        // 3. Mencatat saat User Baru Berhasil Register
        Event::listen(function (Registered $event) {
            UserActivity::create([
                'user_id' => $event->user->id,
                'activity_type' => 'Register Akun Baru',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });

        // 4. Mencatat saat User Berhasil Reset Password
        Event::listen(function (PasswordReset $event) {
            UserActivity::create([
                'user_id' => $event->user->id,
                'activity_type' => 'Reset Password',
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]);
        });
    }
}