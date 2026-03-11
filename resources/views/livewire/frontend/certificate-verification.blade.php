<div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8 min-h-[70vh]">
    <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-100">
        
        <div class="bg-[#ED1C24] p-8 text-center relative overflow-hidden">
            <div class="absolute inset-0 opacity-10">
                <svg class="h-full w-full" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="squares" x="0" y="0" width="20" height="20" patternUnits="userSpaceOnUse"><rect x="0" y="0" width="4" height="4" fill="currentColor"></rect></pattern></defs><rect width="100%" height="100%" fill="url(#squares)"></rect></svg>
            </div>
            <div class="relative z-10">
                <svg class="w-16 h-16 text-white mx-auto mb-4 opacity-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                <h2 class="text-3xl font-extrabold text-white tracking-tight">Verifikasi Sertifikat</h2>
                <p class="text-red-100 mt-2 text-lg">Cek keaslian sertifikat kelulusan Akademi Satu Hati</p>
            </div>
        </div>

        <div class="p-8 md:p-10">
            <form wire:submit.prevent="verify" class="max-w-2xl mx-auto flex flex-col sm:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" wire:model="certificateNumber" 
                        class="w-full px-5 py-4 rounded-xl border border-gray-300 focus:ring-2 focus:ring-[#ED1C24] focus:border-[#ED1C24] text-gray-900 placeholder-gray-400 shadow-sm transition-all text-lg font-mono" 
                        placeholder="Contoh: 00001/SALESMANSHIP/III/2026">
                    @error('certificateNumber') <span class="text-red-500 text-sm mt-2 block font-medium">{{ $message }}</span> @enderror
                </div>
                
                <button type="submit" wire:loading.attr="disabled" class="px-8 py-4 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-lg transition-all flex items-center justify-center text-lg disabled:opacity-50 min-w-[220px]">
                    
                    <div wire:loading.class="hidden" wire:target="verify">
                        Cek Validasi
                    </div>

                    <div wire:loading.class.remove="hidden" wire:target="verify" class="hidden flex items-center">
                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Mencari...
                    </div>
                    
                </button>
            </form>

            @if($serverError)
            <div class="max-w-2xl mx-auto mt-6 bg-red-100 border-l-4 border-red-500 text-red-700 p-4 rounded shadow-sm" role="alert">
                <p class="font-bold">Ditemukan Kesalahan Sistem:</p>
                <p class="text-sm font-mono mt-1">{{ $serverError }}</p>
            </div>
            @endif

            @if($hasSearched && !$serverError)
                <div class="mt-10 border-t border-gray-100 pt-10">
                    @if($certificate)
                        <div class="bg-green-50 border border-green-200 rounded-2xl p-8 shadow-sm">
                            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-4 mb-6 border-b border-green-200 pb-6 text-center sm:text-left">
                                <div class="w-16 h-16 bg-green-500 rounded-full flex items-center justify-center flex-shrink-0 shadow-md">
                                    <svg class="w-8 h-8 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-2xl font-black text-green-800">Sertifikat Valid & Resmi</h3>
                                    <p class="text-green-600 text-base mt-1">Data sertifikat ini terdaftar di sistem database Akademi Satu Hati.</p>
                                </div>
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-y-6 gap-x-8 text-base">
                                <div class="bg-white p-4 rounded-lg border border-green-100">
                                    <span class="block text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wider">Nomor Registrasi</span>
                                    <span class="block text-gray-900 font-bold font-mono text-lg">{{ $certificate->certificate_number }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-green-100">
                                    <span class="block text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wider">Tanggal Diterbitkan</span>
                                    <span class="block text-gray-900 font-bold text-lg">{{ \Carbon\Carbon::parse($certificate->issued_at)->translatedFormat('d F Y') }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-green-100">
                                    <span class="block text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wider">Nama Peserta</span>
                                    <span class="block text-gray-900 font-bold uppercase text-lg">{{ $certificate->user->name }}</span>
                                </div>
                                <div class="bg-white p-4 rounded-lg border border-green-100">
                                    <span class="block text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wider">Honda ID / Dealer</span>
                                    <span class="block text-gray-900 font-bold text-lg">
                                        {{ $certificate->user->honda_id ?? '-' }} 
                                        <span class="text-gray-300 mx-1">|</span> 
                                        {{ $certificate->user->mainDealer ? strtoupper($certificate->user->mainDealer->name) : '-' }}
                                    </span>
                                </div>
                                <div class="md:col-span-2 bg-white p-4 rounded-lg border border-green-100">
                                    <span class="block text-gray-500 font-semibold mb-1 text-sm uppercase tracking-wider">Nama Pelatihan yang Diselesaikan</span>
                                    <span class="block text-gray-900 font-black text-xl text-[#ED1C24]">{{ $certificate->course->title }}</span>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-red-50 border border-red-200 rounded-2xl p-10 text-center shadow-sm max-w-2xl mx-auto">
                            <div class="w-20 h-20 bg-red-100 text-red-500 rounded-full flex items-center justify-center mx-auto mb-6">
                                <svg class="w-10 h-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </div>
                            <h3 class="text-2xl font-black text-red-800 mb-2">Sertifikat Tidak Ditemukan</h3>
                            <p class="text-red-600 text-lg leading-relaxed">
                                Maaf, nomor registrasi <span class="font-mono font-bold text-red-900 bg-red-200 px-2 py-1 rounded">{{ $certificateNumber }}</span> tidak terdaftar di sistem kami. <br><br>Pastikan penulisan nomor sudah benar termasuk tanda garis miring (/).
                            </p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>