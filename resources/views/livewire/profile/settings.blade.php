<div class="bg-[#F3F4F6] min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- 1. MODULAR SIDEBAR --}}
            <x-learning-sidebar />

            {{-- 2. MAIN CONTENT AREA --}}
            <main class="flex-1 space-y-8">
                
                {{-- Header Section --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 flex items-center gap-4">
                    <div class="p-3 bg-red-50 text-[#ED1C24] rounded-xl">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <div>
                        <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Account Settings</h1>
                        <p class="mt-1 text-sm text-gray-500 font-medium">Informasi profil dan otentikasi akun Anda.</p>
                    </div>
                </div>

                {{-- CARD 1: Informasi Pribadi (SEMUA READ ONLY, TANPA TOMBOL) --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Informasi Pribadi</h2>
                        <p class="text-xs text-gray-500 mt-1">Data identitas utama yang terverifikasi.</p>
                    </div>
                    
                    {{-- Alert Messages --}}
                    @if (session()->has('success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm font-bold text-green-700">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="mb-6 rounded-lg bg-red-50 p-4 border border-red-200 flex items-center gap-3">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <span class="text-sm font-bold text-red-700">{{ session('error') }}</span>
                        </div>
                    @endif

                    {{-- Data Fields (Bukan form lagi, murni tampilan div) --}}
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            
                            @if($honda_id)
                            <div>
                                <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Honda ID</label>
                                <input type="text" value="{{ $honda_id }}" 
                                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono font-bold text-gray-600 cursor-not-allowed shadow-inner" 
                                       readonly disabled>
                            </div>
                            @endif

                            {{-- Nama (DIUBAH JADI READ ONLY) --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Nama Lengkap</label>
                                <input wire:model="name" type="text" 
                                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-bold text-gray-600 cursor-not-allowed shadow-inner" 
                                       readonly disabled>
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Email Address</label>
                                <input wire:model="email" type="email" 
                                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono font-bold text-gray-500 cursor-not-allowed shadow-inner" 
                                       readonly disabled placeholder="Belum ada email tertaut">
                                <p class="text-[10px] text-gray-500 mt-1 italic uppercase tracking-wider">Terisi otomatis via Google SSO</p>
                            </div>

                            {{-- Nomor WhatsApp --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Nomor WhatsApp</label>
                                <input wire:model="phone_number" type="text" 
                                       class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono font-bold text-gray-500 cursor-not-allowed shadow-inner" 
                                       readonly disabled>
                                <p class="text-[10px] text-gray-500 mt-1 italic uppercase tracking-wider">Digunakan untuk login OTP</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- CARD 2: SSO & KONEKSI AKUN --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Koneksi Akun (SSO)</h2>
                        <p class="text-xs text-gray-500 mt-1">Tautkan akun Anda ke Google untuk verifikasi email dan login lebih cepat.</p>
                    </div>

                    <div class="p-4 bg-gray-50 rounded-xl border border-gray-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <div class="p-3 bg-white rounded-xl shadow-sm border border-gray-100 shrink-0">
                                <svg class="w-6 h-6" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">Google Account</p>
                                <p class="text-xs text-gray-500 mt-0.5">
                                    {{ $has_google_linked ? 'Akun Anda saat ini sudah tertaut dengan Gmail.' : 'Belum ada akun Google yang tertaut.' }}
                                </p>
                            </div>
                        </div>

                        <div class="shrink-0">
                            @if($has_google_linked)
                                <button wire:click="unlinkGoogle" 
                                        onclick="confirm('Yakin ingin melepas tautan Google SSO?') || event.stopImmediatePropagation()"
                                        class="w-full sm:w-auto px-6 py-2.5 bg-white border border-red-200 rounded-lg text-xs font-bold text-red-600 hover:bg-red-50 focus:ring-2 focus:ring-red-100 transition shadow-sm">
                                    Lepas Tautan
                                </button>
                            @else
                                <a href="{{ route('auth.google') }}" 
                                   class="inline-flex justify-center w-full sm:w-auto px-6 py-2.5 bg-white border border-gray-300 rounded-lg text-xs font-bold text-gray-700 hover:bg-gray-100 focus:ring-2 focus:ring-gray-100 transition shadow-sm">
                                    Tautkan Sekarang
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</div>