<div class="bg-[#F3F4F6] min-h-screen py-10">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Tombol Kembali (Sudah Diperbaiki ke /my-learning) --}}
        <a href="{{ url('/my-learning') }}" wire:navigate class="inline-flex items-center text-sm font-bold text-gray-500 hover:text-[#ED1C24] transition-colors mb-6">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
            Kembali ke Learning Progress
        </a>

        <div class="bg-white rounded-3xl shadow-sm overflow-hidden border border-gray-200">
            {{-- Header Leaderboard --}}
            <div class="bg-gradient-to-br from-[#ED1C24] to-red-800 p-8 text-center text-white relative overflow-hidden">
                {{-- Ornamen Latar (Opsional) --}}
                <svg class="absolute top-0 left-0 w-full h-full opacity-10 pointer-events-none" fill="currentColor" viewBox="0 0 100 100" preserveAspectRatio="none">
                    <path d="M0 100 C 20 0 50 0 100 100 Z"></path>
                </svg>

                <div class="relative z-10">
                    <h1 class="text-3xl font-black uppercase tracking-widest">Global Leaderboard</h1>
                    <p class="text-red-100 text-sm mt-2 font-medium">Para pejuang kompetensi terbaik bulan ini</p>
                </div>
            </div>

            <div class="p-6 md:p-8">
                <div class="space-y-4">
                    @forelse($topUsers as $index => $topUser)
                        <div class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 {{ auth()->id() == $topUser->id ? 'bg-red-50 border-2 border-red-200 shadow-md transform scale-[1.02]' : 'bg-gray-50 hover:bg-white border border-transparent hover:border-gray-200 hover:shadow-sm' }}">
                            
                            {{-- Rank Number --}}
                            <div class="w-10 text-center font-black text-2xl 
                                {{ $index == 0 ? 'text-yellow-500' : '' }}
                                {{ $index == 1 ? 'text-gray-400' : '' }}
                                {{ $index == 2 ? 'text-amber-700' : '' }}
                                {{ $index > 2 ? 'text-gray-300 text-xl' : '' }}">
                                #{{ $index + 1 }}
                            </div>

                            {{-- Avatar --}}
                            <div class="w-12 h-12 rounded-full bg-gray-200 flex-shrink-0 border-2 border-white shadow-sm overflow-hidden">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($topUser->name) }}&background=random&color=fff" class="w-full h-full object-cover" alt="">
                            </div>

                            {{-- Name & Region --}}
                            <div class="flex-1 min-w-0">
                                <h3 class="font-bold text-gray-900 leading-tight truncate">
                                    {{ $topUser->name }}
                                    @if(auth()->id() == $topUser->id)
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-[#ED1C24] text-white">ANDA</span>
                                    @endif
                                </h3>
                                <p class="text-[10px] text-gray-500 uppercase font-bold tracking-tighter truncate mt-0.5">
                                    {{ $topUser->mainDealer->name ?? 'Astra Honda Motor' }}
                                </p>
                            </div>

                            {{-- XP Score --}}
                            <div class="text-right flex-shrink-0">
                                <span class="block text-xl font-black {{ auth()->id() == $topUser->id ? 'text-[#ED1C24]' : 'text-gray-900' }}">{{ number_format($topUser->total_xp) }}</span>
                                <span class="text-[10px] font-bold text-gray-400 uppercase">Total XP</span>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-16">
                            <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <h3 class="text-gray-500 font-bold">Belum ada data</h3>
                            <p class="text-sm text-gray-400 mt-1">Jadilah yang pertama mengumpulkan XP!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>