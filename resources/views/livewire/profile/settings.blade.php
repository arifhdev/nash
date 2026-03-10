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
                        <p class="mt-1 text-sm text-gray-500 font-medium">Perbarui informasi profil dan kelola keamanan akun Anda.</p>
                    </div>
                </div>

                {{-- FORM 1: Update Profile Information --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                    <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Informasi Pribadi</h2>
                        <p class="text-xs text-gray-500 mt-1">Pastikan data diri Anda selalu up-to-date.</p>
                    </div>
                    
                    {{-- Success Message --}}
                    @if (session()->has('success'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3 animate-fade-in-down">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm font-bold text-green-700">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form wire:submit="updateProfile" class="space-y-6">
                                            {{-- Editable Fields Grid --}}
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-2">
                            @if($honda_id)
                            <div>
                                    <label class="block font-bold text-xs text-gray-500 uppercase tracking-wider mb-2">Honda ID</label>
                                    <input type="text" value="{{ $honda_id }}" 
                                            class="w-full rounded-lg border border-gray-200 bg-gray-100 px-4 py-2.5 text-sm font-mono font-bold text-gray-600 cursor-not-allowed shadow-inner" 
                                            readonly disabled>
                            </div>
                            @endif

                            {{-- Nama --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Nama Lengkap</label>
                                <input wire:model="name" type="text" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 font-medium focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                                @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Email Address</label>
                                <input wire:model="email" type="email" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 font-medium focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                                @error('email') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- No HP / WhatsApp --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Nomor WhatsApp</label>
                                <input wire:model="phone_number" type="text" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 font-medium focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                                @error('phone_number') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        </div>

                        {{-- Submit Button --}}
                        <div class="flex items-center justify-end pt-6 border-t border-gray-100 mt-6">
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    wire:target="updateProfile"
                                    class="relative inline-flex items-center justify-center px-8 py-3 bg-[#ED1C24] border border-transparent rounded-lg font-black text-xs text-white uppercase tracking-widest hover:bg-red-700 focus:outline-none focus:border-red-800 focus:ring focus:ring-red-200 disabled:opacity-50 transition shadow-lg shadow-red-200 min-w-[180px]">
                                
                                <span wire:loading.class="hidden" wire:target="updateProfile">
                                    Simpan Perubahan
                                </span>
                                
                                <span class="hidden flex items-center gap-2" 
                                      wire:loading.class.remove="hidden" 
                                      wire:target="updateProfile">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Menyimpan...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

                {{-- FORM 2: Update Password --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                     <div class="mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-lg font-bold text-gray-900">Keamanan Akun</h2>
                        <p class="text-xs text-gray-500 mt-1">Gunakan password yang kuat untuk melindungi akun Anda.</p>
                    </div>
                    
                    {{-- Success Message Password --}}
                    @if (session()->has('password-updated'))
                        <div class="mb-6 rounded-lg bg-green-50 p-4 border border-green-200 flex items-center gap-3 animate-fade-in-down">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            <span class="text-sm font-bold text-green-700">{{ session('password-updated') }}</span>
                        </div>
                    @endif

                    <form wire:submit="updatePassword" class="space-y-6">
                        <div class="grid grid-cols-1 gap-6 max-w-xl">
                            {{-- Current Password --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Password Saat Ini</label>
                                <input wire:model="current_password" type="password" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                                @error('current_password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- New Password --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Password Baru</label>
                                <input wire:model="password" type="password" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                                @error('password') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>

                            {{-- Confirm Password --}}
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Konfirmasi Password Baru</label>
                                <input wire:model="password_confirmation" type="password" 
                                       class="w-full rounded-lg border border-gray-300 px-4 py-2.5 text-sm text-gray-900 focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] placeholder-gray-400 shadow-sm transition duration-150 ease-in-out">
                            </div>
                        </div>

                         <div class="flex items-center justify-start pt-6 border-t border-gray-100 mt-6">
                            <button type="submit" 
                                    wire:loading.attr="disabled" 
                                    wire:target="updatePassword"
                                    class="relative inline-flex items-center justify-center px-8 py-3 bg-gray-900 border border-transparent rounded-lg font-black text-xs text-white uppercase tracking-widest hover:bg-black focus:outline-none focus:border-black focus:ring focus:ring-gray-300 disabled:opacity-50 transition shadow-lg min-w-[180px]">
                                
                                <span wire:loading.class="hidden" wire:target="updatePassword">
                                    Update Password
                                </span>
                                
                                <span class="hidden flex items-center gap-2" 
                                      wire:loading.class.remove="hidden" 
                                      wire:target="updatePassword">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Memproses...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>

            </main>
        </div>
    </div>
</div>