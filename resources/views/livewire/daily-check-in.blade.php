<div>
    @if($alreadyCheckedIn)
        {{-- Tampilan JIKA SUDAH CHECK-IN (Abu-abu / Disabled) --}}
        <button disabled class="w-full flex items-center justify-center gap-2 bg-gray-100 text-gray-400 py-3 px-4 rounded-xl font-bold cursor-not-allowed border border-gray-200 transition-all">
            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Sudah Check-in Hari Ini
        </button>
    @else
        {{-- Tampilan JIKA BELUM CHECK-IN --}}
        <button wire:click="claim" 
                wire:loading.attr="disabled"
                class="w-full flex items-center justify-center gap-2 bg-gradient-to-r from-orange-400 to-[#ED1C24] hover:from-orange-500 hover:to-red-700 text-white py-3 px-4 rounded-xl font-bold shadow-lg shadow-red-500/30 transform hover:scale-[1.02] transition-all duration-300">
            
            {{-- Loading Spinner (Disembunyikan pakai Tailwind 'hidden', muncul saat diklik) --}}
            <svg wire:loading.class.remove="hidden" wire:target="claim" class="hidden animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>

            {{-- Ikon Bintang (Disembunyikan saat sedang loading) --}}
            <svg wire:loading.class="hidden" wire:target="claim" class="w-5 h-5 text-yellow-300" fill="currentColor" viewBox="0 0 20 20">
                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
            </svg>
            
            {{-- Teks (Tukar otomatis pakai class & dinamis baca database) --}}
            <span wire:loading.class="hidden" wire:target="claim">
                Ambil 
                @if($xpToReward > 0) 
                    +{{ $xpToReward }} XP 
                @endif 
                
                @if($xpToReward > 0 && $pointsToReward > 0) 
                    & 
                @endif
                
                @if($pointsToReward > 0) 
                    +{{ $pointsToReward }} Poin 
                @endif
            </span>
            <span wire:loading.class.remove="hidden" wire:target="claim" class="hidden">Memproses...</span>
            
        </button>
    @endif
</div>