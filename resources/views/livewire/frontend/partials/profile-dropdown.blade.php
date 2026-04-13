<div x-data="{ open: false }" class="relative">
    
    {{-- Trigger Icon (MURNI KLIK - BEBAS HOVER BIAR GAK GLITCH DI HP) --}}
    <div class="pb-2">
        <button type="button" 
                @click="open = !open" 
                @click.outside="open = false"
                class="flex items-center focus:outline-none">
            @if(auth()->check() && auth()->user()->profile_photo_url)
                <img class="h-9 w-9 rounded-full object-cover border-2 border-gray-200 shadow-sm" src="{{ auth()->user()->profile_photo_url }}" alt="{{ auth()->user()->name }}">
            @else
                {{-- Icon Default untuk Guest --}}
                <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-[#ED1C24] to-red-400 flex items-center justify-center text-white shadow-md border-2 border-white">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
            @endif
        </button>
    </div>

    {{-- Dropdown Menu --}}
    <div x-show="open" 
         x-cloak
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-1"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-1"
         class="absolute right-0 mt-0 w-64 bg-white rounded-xl shadow-2xl border border-gray-100 z-[100] overflow-hidden">
        
        @auth
            {{-- User Info Header --}}
            <div class="px-5 py-4 bg-gray-50/50 border-b border-gray-100">
                <p class="text-sm font-bold text-gray-900 truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-gray-500 truncate mt-0.5">{{ auth()->user()->email }}</p>
                <div class="mt-2 flex flex-wrap gap-1">
                    @foreach(auth()->user()->getRoleNames() as $role)
                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-red-100 text-[#ED1C24] border border-red-200">
                            {{ str_replace('_', ' ', $role) }}
                        </span>
                    @endforeach
                </div>
            </div>

            {{-- Links --}}
            <div class="py-1">
                <a href="{{ route('my-learning') }}" wire:navigate @click="open = false" class="group flex items-center px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="p-1.5 rounded-lg bg-blue-50 text-blue-600 group-hover:bg-blue-100 mr-3 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    My Learning Page
                </a>
                
                <a href="{{ route('mandatory-courses') }}" wire:navigate @click="open = false" class="group flex items-center px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="p-1.5 rounded-lg bg-purple-50 text-purple-600 group-hover:bg-purple-100 mr-3 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    Course Wajib
                </a>

                <a href="{{ route('my-courses') }}" wire:navigate @click="open = false" class="group flex items-center px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="p-1.5 rounded-lg bg-orange-50 text-orange-600 group-hover:bg-orange-100 mr-3 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    My Courses
                </a>

                <a href="{{ route('my-certificates') }}" wire:navigate @click="open = false" class="group flex items-center px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="p-1.5 rounded-lg bg-green-50 text-green-600 group-hover:bg-green-100 mr-3 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    Sertifikat Saya
                </a>

                <a href="{{ route('profile.settings') }}" wire:navigate @click="open = false" class="group flex items-center px-5 py-2.5 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                    <div class="p-1.5 rounded-lg bg-gray-100 text-gray-600 group-hover:bg-gray-200 mr-3 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                    </div>
                    Settings
                </a>
            </div>

            {{-- Logout --}}
            <div class="border-t border-gray-100 bg-gray-50/30">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center px-5 py-3 text-sm font-semibold text-red-600 hover:bg-red-50 transition-colors text-left">
                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                        Sign Out
                    </button>
                </form>
            </div>
        @else
            <div class="p-5 flex flex-col items-center justify-center text-center">
                <div class="mb-4">
                    <p class="text-sm font-bold text-gray-900">Selamat Datang!</p>
                    <p class="text-xs text-gray-500 mt-1 leading-relaxed">Silakan masuk ke akun Anda untuk mengakses materi pembelajaran.</p>
                </div>
                <a href="{{ route('login') }}" wire:navigate @click="open = false" class="w-full flex justify-center items-center px-4 py-2 bg-[#ED1C24] text-white text-sm font-bold rounded-lg shadow hover:bg-red-700 hover:shadow-lg transition-all duration-200">
                    Masuk Akun
                </a>
            </div>
        @endauth
    </div>
</div>