<x-filament-panels::page>
    <div wire:poll.10s>
        @php $data = $this->getStatus(); @endphp

        @if($data['error'])
            {{-- BOX ERROR SAAT DAEMON MATI --}}
            <div class="p-6 rounded-2xl bg-red-50 border border-red-200 flex flex-col md:flex-row items-center justify-between gap-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="p-3 bg-red-600 rounded-xl text-white shadow-lg">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-black text-red-900 uppercase tracking-tight">Supervisor Is Dead</h3>
                        <p class="text-xs font-mono text-red-600 bg-red-100/50 p-1 rounded mt-1">{{ $data['output'] }}</p>
                    </div>
                </div>

                <x-filament::button 
                    wire:click="startSupervisorService" 
                    color="danger" 
                    icon="heroicon-m-bolt"
                    size="lg"
                    class="shadow-xl shadow-red-200"
                >
                    Nyalakan Servis Sekarang
                </x-filament::button>
            </div>
        @else
            {{-- LIST WORKER --}}
            <div class="grid grid-cols-1 gap-4">
                @forelse($data['processes'] as $proc)
                    <div class="bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-800 p-5 rounded-2xl shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4 transition hover:border-gray-300 dark:hover:border-gray-700">
                        <div class="flex items-center gap-4">
                            {{-- Indikator Lampu --}}
                            <div class="relative flex h-3 w-3">
                                @if($proc['status'] === 'RUNNING')
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"></span>
                                @else
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                                @endif
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 dark:text-white tracking-tight">{{ $proc['name'] }}</h4>
                                <p class="text-[10px] text-gray-500 font-mono italic">{{ $proc['description'] }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            {{-- Badge Status --}}
                            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border {{ $proc['status'] === 'RUNNING' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200' }}">
                                {{ $proc['status'] }}
                            </span>

                            {{-- Grup Tombol --}}
                            <div class="flex items-center bg-gray-50 dark:bg-gray-800 p-1 rounded-xl border border-gray-200 dark:border-gray-700">
                                @if($proc['status'] !== 'RUNNING')
                                    <x-filament::icon-button 
                                        icon="heroicon-m-play" color="success" label="Start"
                                        wire:click="startProcess('{{ $proc['name'] }}')"
                                        wire:loading.attr="disabled"
                                    />
                                @else
                                    <x-filament::icon-button 
                                        icon="heroicon-m-pause" color="danger" label="Stop"
                                        wire:click="stopProcess('{{ $proc['name'] }}')"
                                        wire:confirm="Yakin ingin menghentikan worker ini?"
                                        wire:loading.attr="disabled"
                                    />
                                @endif

                                <x-filament::icon-button 
                                    icon="heroicon-m-arrow-path" color="info" label="Restart"
                                    wire:click="restartProcess('{{ $proc['name'] }}')"
                                    wire:loading.attr="disabled"
                                />
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center bg-gray-50 dark:bg-gray-800 rounded-2xl border-2 border-dashed border-gray-200 dark:border-gray-700">
                        <p class="text-gray-400 italic">Tidak ada worker yang terdeteksi di server.</p>
                    </div>
                @endforelse
            </div>
        @endif
        
        <div class="mt-4 flex items-center justify-end gap-2 text-[10px] text-gray-400 font-bold uppercase tracking-widest">
            <div class="w-1.5 h-1.5 bg-blue-500 rounded-full animate-pulse"></div>
            Sync status setiap 10 detik
        </div>
    </div>
</x-filament-panels::page>