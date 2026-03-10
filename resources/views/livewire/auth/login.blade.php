<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F3F4F6]">
    
    {{-- Logo / Brand Header --}}
    <div class="mb-8 text-center">
         <a href="/" wire:navigate class="inline-block">
            <img src="https://dev.akademi-satuhati.com/images/logo.png" alt="Akademi Satu Hati" class="h-20 w-auto mx-auto mb-4 hover:scale-105 transition-transform duration-300">
         </a>
         <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight">Login Akun</h2>
         <p class="mt-2 text-sm text-gray-600">Masuk untuk melanjutkan pembelajaran</p>
    </div>

    {{-- Main Card --}}
    <div class="w-full sm:max-w-md px-8 py-10 bg-white shadow-xl sm:rounded-2xl border border-gray-200">
        
        {{-- Session Status --}}
        @if (session()->has('status'))
            <div class="mb-5 font-medium text-sm text-green-600 bg-green-50 px-4 py-3 rounded-lg border border-green-100 flex items-center">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('status') }}
            </div>
        @endif

        <form wire:submit="login" class="space-y-5">
            
            {{-- Email --}}
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-1">Email</label>
                <input wire:model="email" type="email" 
                       class="w-full pl-4 pr-24 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400" 
                       placeholder="email@anda.com" required autofocus>
                @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Password --}}
            <div>
                <label class="block font-bold text-sm text-gray-700 mb-1">Password</label>
                <input wire:model="password" type="password" 
                       class="w-full pl-4 pr-24 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400" 
                       placeholder="••••••••" required autocomplete="current-password">
                @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
            </div>

            {{-- Remember Me & Forgot Password --}}
            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center cursor-pointer">
                    <input wire:model="remember" id="remember_me" type="checkbox" 
                           class="rounded border-gray-300 text-[#ED1C24] shadow-sm focus:ring-[#ED1C24] cursor-pointer">
                    <span class="ml-2 text-sm text-gray-600 font-medium select-none">Ingat Saya</span>
                </label>

                @if (Route::has('password.request'))
                    <a href="{{ route('password.request') }}" wire:navigate class="text-sm font-bold text-gray-400 hover:text-[#ED1C24] transition">
                        Lupa Password?
                    </a>
                @endif
            </div>

            {{-- Submit Button --}}
            <div class="pt-2">
                <button type="submit" 
                        wire:loading.attr="disabled"
                        class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-black text-white bg-[#ED1C24] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition duration-150 ease-in-out uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                    
                    {{-- Text Normal: Hilang saat loading --}}
                    <span wire:loading.remove>
                        Masuk Akun
                    </span>

                    {{-- Text Loading: FORCE FLEX ROW --}}
                    {{-- wire:loading.flex MEMAKSA display: flex saat aktif --}}
                    <span wire:loading.flex class="flex-row items-center justify-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="whitespace-nowrap">Memproses...</span>
                    </span>
                </button>
            </div>

        </form>

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