<div class="bg-[#F3F4F6] min-h-screen">
    <div class="max-w-7xl mx-auto py-10 px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col lg:flex-row gap-8">
            
            {{-- 1. MODULAR SIDEBAR --}}
            {{-- Pastikan component sidebar ini tidak statis / tidak error di livewire --}}
            <x-learning-sidebar />

            {{-- 2. MAIN CONTENT AREA --}}
            <main class="flex-1 space-y-8">
                
                {{-- Container Utama --}}
                <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 min-h-[700px]">
                    
                    {{-- Content Header --}}
                    <div class="flex flex-col md:flex-row md:items-center justify-between mb-8 pb-6 border-b border-gray-100 gap-4">
                        <div>
                            <h1 class="text-2xl font-extrabold text-gray-900 uppercase tracking-tight">Kursus Saya</h1>
                            <p class="mt-2 text-sm text-gray-500 font-medium">Daftar materi yang sedang atau telah Anda pelajari.</p>
                        </div>

                        {{-- Search Form (LIVEWIRE) --}}
                        <div class="relative w-full md:w-72">
                            {{-- Input Search dengan Live Update --}}
                            <input type="text" 
                                   wire:model.live.debounce.500ms="search" 
                                   placeholder="Cari judul materi..." 
                                   class="w-full pl-10 pr-4 py-2.5 rounded-lg border border-gray-200 text-sm focus:border-[#ED1C24] focus:ring-1 focus:ring-[#ED1C24] transition shadow-sm placeholder-gray-400">
                            
                            <div class="absolute left-3.5 top-3 text-gray-400">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                            </div>

                            {{-- Loading Indicator saat searching --}}
                            <div wire:loading.flex wire:target="search" class="absolute right-3 top-3 hidden items-center">
    <svg class="animate-spin h-5 w-5 text-[#ED1C24]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
</div>
                        </div>
                    </div>

                    {{-- Course Grid --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        @forelse($courses as $course)
                            {{-- Course Item Card --}}
                            <div class="group flex flex-col bg-white rounded-2xl border border-gray-200 overflow-hidden hover:border-red-200 hover:shadow-lg hover:shadow-red-500/5 transition-all duration-300">
                                
                                {{-- Thumbnail Link --}}
                                <a href="{{ route('course.detail', $course->slug) }}" wire:navigate class="block relative h-48 bg-gray-100 overflow-hidden">
                                    @if($course->image)
                                        <img src="{{ Storage::url($course->image) }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="{{ $course->title }}">
                                    @else
                                        <div class="w-full h-full flex items-center justify-center bg-gray-200 text-gray-400">
                                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                        </div>
                                    @endif
                                    
                                    {{-- Category Badge --}}
                                    <div class="absolute top-4 left-4">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-white/90 text-gray-900 backdrop-blur-sm shadow-sm border border-gray-100">
                                            {{ $course->category->name ?? 'General' }}
                                        </span>
                                    </div>

                                    {{-- STATUS BADGE (Penting untuk My Course) --}}
                                    <div class="absolute top-4 right-4">
                                        @if($course->pivot->progress_percent >= 100)
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-700 shadow-sm border border-green-200">
                                                Selesai
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-yellow-100 text-yellow-700 shadow-sm border border-yellow-200">
                                                {{ $course->pivot->progress_percent }}%
                                            </span>
                                        @endif
                                    </div>
                                </a>

                                {{-- Body --}}
                                <div class="flex-1 p-6 flex flex-col justify-between">
                                    <div>
                                        <a href="{{ route('course.detail', $course->slug) }}" wire:navigate>
                                            <h3 class="text-lg font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors leading-tight mb-2 line-clamp-2">
                                                {{ $course->title }}
                                            </h3>
                                        </a>
                                        
                                        {{-- Progress Bar Kecil --}}
                                        <div class="w-full bg-gray-100 rounded-full h-1.5 mb-4">
                                            <div class="bg-[#ED1C24] h-1.5 rounded-full" style="width: {{ $course->pivot->progress_percent }}%"></div>
                                        </div>

                                        <p class="text-sm text-gray-500 line-clamp-2">
                                            {{ $course->description }}
                                        </p>
                                    </div>

                                    <div class="mt-6 flex items-center justify-between border-t border-gray-50 pt-4">
                                        <div class="flex items-center gap-2 text-xs font-bold text-gray-400">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                                            <span>{{ $course->modules->count() }} Modul</span>
                                        </div>
                                        
                                        <a href="{{ route('course.player', $course->slug) }}" wire:navigate class="inline-flex items-center text-xs font-black text-white bg-black px-4 py-2 rounded-lg hover:bg-[#ED1C24] transition-colors uppercase tracking-wider shadow-sm">
                                            {{ $course->pivot->progress_percent >= 100 ? 'Review' : 'Lanjut' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @empty
                            {{-- Empty State --}}
                            <div class="col-span-1 md:col-span-2 py-12 text-center">
                                <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-gray-50 mb-4">
                                    <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <h3 class="text-lg font-bold text-gray-900">
                                    {{ $search ? 'Tidak ditemukan' : 'Belum ada kursus' }}
                                </h3>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ $search ? 'Coba kata kunci lain.' : 'Anda belum mengambil kursus apapun.' }}
                                </p>
                                
                                {{-- Tombol Reset Search jika sedang mencari --}}
                                @if($search)
                                    <button wire:click="$set('search', '')" class="mt-4 inline-block text-xs font-bold text-[#ED1C24] hover:underline cursor-pointer">
                                        Reset Pencarian
                                    </button>
                                @endif
                            </div>
                        @endforelse

                    </div>

                    {{-- Pagination --}}
                    <div class="mt-10 border-t border-gray-100 pt-6">
                        {{ $courses->links() }} 
                    </div>
                    
                </div>
            </main>

        </div>
    </div>
</div>