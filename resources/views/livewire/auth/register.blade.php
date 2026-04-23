<div x-data="{ showPdpModal: false }" class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#F3F4F6]">

    {{-- Logo / Brand Header --}}
    <div class="mb-8 text-center">
        <a href="/" wire:navigate class="inline-block">
            <img src="https://dev.akademi-satuhati.com/images/logo.png" alt="Akademi Satu Hati" class="h-20 w-auto mx-auto mb-4 hover:scale-105 transition-transform duration-300">
        </a>
        <h2 class="text-3xl font-extrabold text-gray-900 uppercase tracking-tight">Register Akun</h2>
        <p class="mt-2 text-sm text-gray-600">Bergabunglah dengan Akademi Satu Hati</p>
    </div>

    {{-- Main Card --}}
    <div class="w-full sm:max-w-2xl px-8 py-10 bg-white shadow-xl sm:rounded-2xl border border-gray-200">

        {{-- STEP 1: Form Registrasi Utama --}}
        @if($step === 1)
            <form wire:submit="register" class="space-y-5">

                {{-- 1. Status Karyawan (Paling Atas) --}}
                <div>
                    <label class="block font-bold text-sm text-gray-700 mb-1">Status Karyawan <span class="text-red-500">*</span></label>
                    <select wire:model.live="user_type"
                        class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400 cursor-pointer">
                        <option value="">-- Pilih Status --</option>
                        @foreach($userTypes as $key => $label)
                            {{-- Logic: Lewati opsi Non Karyawan Dealer --}}
                            @if($key !== 'non_dealer')
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endif
                        @endforeach
                    </select>
                    @error('user_type') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                </div>

                {{-- LOGIC: Field Khusus (Muncul Setelah Pilih Status) --}}
                @if($user_type)
                    <div class="p-5 bg-gray-50 rounded-lg border border-gray-200 space-y-5 animate-fade-in-down">

                        {{-- JALUR AHM: KARYAWAN AHM --}}
                        @if($user_type === 'ahm')
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">AHM ID <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input wire:model.live.debounce.500ms="ahm_id" type="text"
                                        class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400 uppercase"
                                        placeholder="MASUKKAN AHM ID ANDA">
                                    <div wire:loading wire:target="ahm_id" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                </div>
                                @error('ahm_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- JALUR DEALER: HANYA KARYAWAN DEALER --}}
                        @if($user_type === 'dealer')
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-1">Honda ID <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input wire:model.live.debounce.500ms="honda_id" type="text"
                                        class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400 uppercase"
                                        placeholder="MASUKKAN ID HONDA ANDA">
                                    <div wire:loading wire:target="honda_id" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    </div>
                                </div>
                                @error('honda_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                            </div>
                        @endif

                        {{-- JALUR MAIN DEALER: OPSI TRAINER ATAU BUKAN --}}
                        @if($user_type === 'main_dealer')
                            <div>
                                <label class="block font-bold text-sm text-gray-700 mb-2">Posisi di Main Dealer <span class="text-red-500">*</span></label>
                                <div class="flex items-center gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model.live="role_in_md" value="trainer" class="w-4 h-4 text-[#ED1C24] focus:ring-[#ED1C24] border-gray-300">
                                        <span class="text-sm font-medium text-gray-700">Sebagai Trainer</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" wire:model.live="role_in_md" value="non_trainer" class="w-4 h-4 text-[#ED1C24] focus:ring-[#ED1C24] border-gray-300">
                                        <span class="text-sm font-medium text-gray-700">Bukan Trainer</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Input Trainer ID --}}
                            @if($role_in_md === 'trainer')
                                <div class="animate-fade-in-down">
                                    <label class="block font-bold text-sm text-gray-700 mb-1">Trainer ID <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input wire:model.live.debounce.500ms="trainer_id" type="text"
                                            class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400 uppercase"
                                            placeholder="MASUKKAN TRAINER ID ANDA">
                                        <div wire:loading wire:target="trainer_id" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                    </div>
                                    @error('trainer_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Input MD ID --}}
                            @if($role_in_md === 'non_trainer')
                                <div class="animate-fade-in-down">
                                    <label class="block font-bold text-sm text-gray-700 mb-1">MD ID <span class="text-red-500">*</span></label>
                                    <div class="relative">
                                        <input wire:model.live.debounce.500ms="custom_id" type="text"
                                            class="w-full pl-4 pr-10 py-3 bg-white border border-gray-200 rounded-lg text-sm text-gray-700 focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition placeholder-gray-400 uppercase"
                                            placeholder="MASUKKAN MD ID ANDA">
                                        <div wire:loading wire:target="custom_id" class="absolute inset-y-0 right-0 flex items-center pr-3">
                                            <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        </div>
                                    </div>
                                    @error('custom_id') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            @endif
                        @endif

                        {{-- INFO OTOMATIS: Penempatan, Divisi & Jabatan --}}
                        @if($main_dealer_id || $dealer_id || $division_id || $position_id)
                            <div class="animate-fade-in-down space-y-4 pt-4 border-t border-gray-200 mt-4">
                                
                                @if($main_dealer_id)
                                    <div>
                                        <label class="block font-bold text-sm text-gray-700 mb-1">Main Dealer Penempatan</label>
                                        <input type="text" value="{{ \App\Models\MainDealer::find($main_dealer_id)?->name }}" class="w-full pl-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly disabled>
                                    </div>
                                @endif

                                @if($user_type === 'dealer' && $dealer_id)
                                    <div>
                                        <label class="block font-bold text-sm text-gray-700 mb-1">Dealer Penempatan</label>
                                        <input type="text" value="{{ \App\Models\Dealer::find($dealer_id)?->name }}" class="w-full pl-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly disabled>
                                    </div>
                                @endif

                                @if($division_id)
                                    <div>
                                        <label class="block font-bold text-sm text-gray-700 mb-1">Divisi</label>
                                        <input type="text" value="{{ \App\Models\Division::find($division_id)?->name }}" class="w-full pl-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly disabled>
                                    </div>
                                @endif

                                @if($position_id)
                                    <div>
                                        <label class="block font-bold text-sm text-gray-700 mb-1">Jabatan</label>
                                        <input type="text" value="{{ \App\Models\Position::find($position_id)?->name }}" class="w-full pl-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" readonly disabled>
                                    </div>
                                @endif

                            </div>
                        @endif
                    </div>
                @endif

                <hr class="border-gray-100 my-2">

                {{-- LOGIC TAMPIL FORM DATA DIRI --}}
                @php
                    $hasValidId = ($user_type === 'ahm' && $ahm_id && !$errors->has('ahm_id')) ||
                                  ($user_type === 'dealer' && $honda_id && !$errors->has('honda_id')) ||
                                  ($user_type === 'main_dealer' && $role_in_md === 'trainer' && $trainer_id && !$errors->has('trainer_id')) ||
                                  ($user_type === 'main_dealer' && $role_in_md === 'non_trainer' && $custom_id && !$errors->has('custom_id'));
                @endphp

                @if($hasValidId)
                    {{-- Form Lanjutan (Nama dan HP) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5 animate-fade-in-up">
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-1">Nama Lengkap</label>
                            {{-- Nama Lengkap Read-Only & Uppercase --}}
                            <input wire:model="name" type="text"
                                class="w-full pl-4 py-3 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-700 uppercase cursor-not-allowed focus:outline-none"
                                placeholder="Nama Sesuai KTP" readonly>
                            @error('name') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        
                        <div>
                            <label class="block font-bold text-sm text-gray-700 mb-1">Nomor WhatsApp</label>
                            <input wire:model="phone_number" type="text" class="w-full pl-4 py-3 bg-white border border-gray-200 rounded-lg text-sm focus:outline-none focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24]" placeholder="0812xxxx" required>
                            @error('phone_number') <span class="text-red-500 text-xs font-bold mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    {{-- CHECKBOX PERSETUJUAN PDP --}}
                    <div class="pt-4 animate-fade-in-up flex items-start gap-3">
                        <div class="flex items-center h-5">
                            <input id="agreed_pdp" wire:model="agreed_pdp" type="checkbox" required
                                class="w-4 h-4 text-[#ED1C24] border-gray-300 rounded focus:ring-[#ED1C24] cursor-pointer">
                        </div>
                        <div class="text-sm">
                            <label for="agreed_pdp" class="font-medium text-gray-700 cursor-pointer">
                                Saya telah membaca dan menyetujui
                            </label>
                            <button type="button" @click="showPdpModal = true" class="text-[#ED1C24] font-bold hover:underline hover:text-red-700">
                                Persetujuan Pemrosesan Data Pribadi (PDP)
                            </button>
                        </div>
                    </div>

                    <div class="pt-4 animate-fade-in-up">
                        <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-black text-white bg-[#ED1C24] hover:bg-red-700 transition duration-150 ease-in-out uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                            <span wire:loading.remove wire:target="register">Kirim Kode OTP</span>
                            <span wire:loading.flex wire:target="register" class="flex-row items-center justify-center gap-2">
                                <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span>Mengirim...</span>
                            </span>
                        </button>
                    </div>
                @else
                    <div class="text-center py-6">
                        <p class="text-sm text-gray-500 italic">Silakan pilih status karyawan dan masukkan ID yang valid untuk melanjutkan.</p>
                    </div>
                @endif
            </form>

            <div class="mt-6 text-center">
                <p class="text-sm text-gray-600">Sudah punya akun? <a href="{{ route('login') }}" wire:navigate class="font-bold text-[#ED1C24] hover:text-red-700 hover:underline">Login disini</a></p>
            </div>
            
        {{-- STEP 2: Form Verifikasi OTP --}}
        @elseif($step === 2)
            <form wire:submit="verifyOtp" class="space-y-6 text-center">
                
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
                    <button type="submit" wire:loading.attr="disabled" class="w-full flex justify-center items-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-black text-white bg-[#ED1C24] hover:bg-red-700 transition duration-150 ease-in-out uppercase tracking-wider disabled:opacity-70 disabled:cursor-not-allowed">
                        <span wire:loading.remove wire:target="verifyOtp">Verifikasi & Daftar</span>
                        <span wire:loading.flex wire:target="verifyOtp" class="flex-row items-center justify-center gap-2">
                            <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Memproses...</span>
                        </span>
                    </button>
                </div>
                
                <div class="mt-4">
                    <button type="button" wire:click="$set('step', 1)" class="text-sm text-gray-500 hover:text-gray-700 underline">
                        Batal dan ubah nomor
                    </button>
                </div>
            </form>
        @endif

    </div>

    {{-- MODAL PDP (Ditangani dengan Alpine.js) --}}
    <div x-show="showPdpModal" style="display: none;" class="relative z-50" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        {{-- Background overlay --}}
        <div x-show="showPdpModal"
            x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity"></div>

        <div class="fixed inset-0 z-10 w-screen overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                {{-- Modal Panel --}}
                <div x-show="showPdpModal" @click.away="showPdpModal = false"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">

                    <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                        <div class="sm:flex sm:items-start">
                            <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                                <h3 class="text-lg font-bold leading-6 text-gray-900 mb-4 border-b pb-2" id="modal-title">
                                    Persetujuan Pemrosesan Data Pribadi (PDP)
                                </h3>
                                <div class="mt-2 text-sm text-gray-600 max-h-60 overflow-y-auto pr-2 space-y-3 prose">
                                    @if($pdpContent)
                                        {!! $pdpContent->content !!}
                                    @else
                                        <p>Konten Persetujuan belum tersedia.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6">
                        <button type="button" @click="showPdpModal = false" class="inline-flex w-full justify-center rounded-lg bg-[#ED1C24] px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-700 sm:ml-3 sm:w-auto">
                            Saya Mengerti & Setuju
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>