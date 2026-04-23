<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F3F4F6]">
    
    {{-- Logo / Brand Header --}}
    <div class="mb-8 text-center">
         <a href="/" wire:navigate class="inline-block">
            <img src="https://dev.akademi-satuhati.com/images/logo.png" alt="Akademi Satu Hati" class="h-20 w-auto mx-auto mb-4 hover:scale-105 transition-transform duration-300">
         </a>
         <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight">Login Akun</h2>
         <p class="mt-2 text-sm text-gray-600">Masuk dengan Nomor WhatsApp atau Gmail</p>
    </div>

    {{-- Main Card --}}
    <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-xl sm:rounded-2xl border border-gray-200">
        
        {{-- ALERT MENTAL DARI SSO (Ditolak karena belum daftar/tautkan akun) --}}
        @if (session()->has('status'))
            <div class="mb-6 font-medium text-sm text-red-600 bg-red-50 px-4 py-3 rounded-lg border border-red-200 flex items-center">
                <svg class="w-5 h-5 mr-3 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>{{ session('status') }}</span>
            </div>
        @endif

        {{-- STEP 1: FORM INPUT NOMOR WA --}}
        @if($step === 1)
            <form wire:submit="requestOtp" class="space-y-5">
                
                {{-- Nomor WhatsApp --}}
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-1">Nomor WhatsApp</label>
                    <input wire:model="phone_number" type="text" 
                           class="w-full pl-4 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400" 
                           placeholder="Contoh: 0812xxxx" required autofocus>
                    @error('phone_number') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- Remember Me --}}
                <div class="flex items-center justify-between pt-1">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input wire:model="remember" id="remember_me" type="checkbox" 
                               class="rounded border-gray-300 text-[#ED1C24] shadow-sm focus:ring-[#ED1C24] cursor-pointer">
                        <span class="ml-2 text-sm text-gray-600 font-medium select-none">Ingat Saya</span>
                    </label>
                </div>

                {{-- Submit Button (Request OTP) --}}
                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-black text-white bg-[#ED1C24] hover:bg-red-700 transition uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="requestOtp">Masuk via WhatsApp</span>
                        <span wire:loading.flex wire:target="requestOtp" class="items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Mengirim OTP...</span>
                        </span>
                    </button>
                </div>
            </form>

            {{-- PEMISAH ATAU --}}
            <div class="mt-6 flex items-center">
                <div class="w-full border-t border-gray-200"></div>
                <div class="px-4 text-xs font-bold text-gray-400 uppercase tracking-wider">Atau</div>
                <div class="w-full border-t border-gray-200"></div>
            </div>

            {{-- TOMBOL GOOGLE SSO --}}
            <div class="mt-6">
                <button wire:click="redirectToGoogle" type="button" class="w-full flex justify-center items-center gap-3 py-3 px-4 border border-gray-300 rounded-lg shadow-sm text-sm font-bold text-gray-700 bg-white hover:bg-gray-50 transition">
                    <svg class="w-5 h-5" viewBox="0 0 24 24"><path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/><path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/><path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/><path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/></svg>
                    Masuk dengan Google
                </button>
            </div>

        {{-- STEP 2: FORM VERIFIKASI OTP --}}
        @elseif($step === 2)
            <form wire:submit="verifyOtp" class="space-y-6 text-center animate-fade-in-up">
                
                <div class="mb-4">
                    <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-green-100 mb-4">
                        <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h3 class="text-lg leading-6 font-bold text-gray-900">Cek WhatsApp Anda</h3>
                    <p class="text-sm text-gray-500 mt-2">
                        Kami telah mengirimkan 6 digit kode OTP ke nomor <br>
                        <span class="font-bold text-gray-800">{{ $phone_number }}</span>
                    </p>
                </div>

                <div>
                    <input wire:model="otp_input" type="text" maxlength="6"
                        class="w-full text-center tracking-[0.5em] text-2xl font-bold py-4 bg-white border border-gray-300 rounded-lg focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-300"
                        placeholder="••••••" required autofocus>
                    @error('otp_input') <span class="text-red-500 text-xs font-bold mt-2 block">{{ $message }}</span> @enderror
                </div>

                <div class="pt-2">
                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-black text-white bg-[#ED1C24] hover:bg-red-700 transition uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="verifyOtp">Verifikasi & Masuk</span>
                        <span wire:loading.flex wire:target="verifyOtp" class="items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
                
                <div class="mt-4">
                    <button type="button" wire:click="$set('step', 1)" class="text-sm text-gray-500 hover:text-[#ED1C24] underline transition font-medium">
                        Batal dan ubah nomor
                    </button>
                </div>
            </form>
        @endif

        {{-- Footer Link --}}
        <div class="mt-8 text-center pt-6 border-t border-gray-100">
            <p class="text-sm text-gray-600">
                Belum punya akun? 
                <a href="{{ route('register') }}" wire:navigate class="font-bold text-[#ED1C24] hover:text-red-700 hover:underline">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>
</div>