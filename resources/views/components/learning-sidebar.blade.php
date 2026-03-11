<aside class="w-full lg:w-72 flex-shrink-0">
    <div class="sticky top-24 space-y-6">
        
        {{-- Profile Section --}}
        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200 flex flex-col items-center">
            {{-- Profile Photo --}}
            <div class="relative mb-4 group">
                <div class="w-20 h-20 bg-white rounded-full p-1 border border-gray-200 shadow-sm transition-all duration-300 group-hover:border-red-100">
                    @if(auth()->user()->profile_photo_url)
                        <img src="{{ auth()->user()->profile_photo_url }}" class="w-full h-full rounded-full object-cover">
                    @else
                        <div class="w-full h-full rounded-full bg-[#ED1C24] flex items-center justify-center shadow-inner">
                            <span class="text-2xl font-black text-white tracking-tighter">{{ substr(auth()->user()->name, 0, 1) }}</span>
                        </div>
                    @endif
                </div>
                <div class="absolute bottom-1 right-1 w-4 h-4 bg-green-500 border-2 border-white rounded-full shadow-sm z-10"></div>
            </div>

            <h2 class="text-lg font-extrabold text-gray-900 leading-tight text-center truncate w-full">{{ auth()->user()->name }}</h2>
            
            <div class="mt-3 flex flex-wrap justify-center gap-1">
                @foreach(auth()->user()->getRoleNames() as $role)
                    <div class="flex items-center gap-1 px-3 py-1 bg-red-50 rounded-full border border-red-100">
                        <div class="w-1.5 h-1.5 bg-[#ED1C24] rounded-full animate-pulse"></div>
                        <span class="text-[10px] font-bold text-[#ED1C24] uppercase tracking-wider">
                            {{ str_replace('_', ' ', $role) }}
                        </span>
                    </div>
                @endforeach
            </div>

            <div class="mt-6 pt-6 border-t border-gray-100 w-full text-center space-y-4">
                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Main Dealer</p>
                    <p class="text-xs font-bold text-gray-700 line-clamp-2">
                        @if(auth()->user()->mainDealer)
                            {{ auth()->user()->mainDealer->name }} - {{ auth()->user()->mainDealer->code }}
                        @else
                            -
                        @endif
                    </p>
                </div>

                @if(auth()->user()->dealer)
                    <div>
                        <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Dealer</p>
                        <p class="text-xs font-bold text-gray-700 line-clamp-2">
                            {{ auth()->user()->dealer->name }} - {{ auth()->user()->dealer->code }}
                        </p>
                    </div>
                @endif

                <div>
                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-widest mb-1">Jabatan</p>
                    <div class="inline-flex items-center justify-center gap-1.5 bg-gray-50 px-3 py-1.5 rounded-lg border border-gray-100 max-w-full">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        <p class="text-xs font-bold text-gray-700 text-center leading-tight">
                            @if(auth()->user()->positions->count() > 0)
                                {{ auth()->user()->positions->map(fn($pos) => $pos->name . ($pos->group ? ' (' . $pos->group . ')' : ''))->join(', ') }}
                            @else
                                -
                            @endif
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Navigation Menu --}}
        <div class="bg-white rounded-2xl p-4 shadow-sm border border-gray-200">
            <nav class="space-y-1">
                @php
                    $navs = [
                        [
                            'id' => 'my-learning', 
                            'label' => 'Learning Progress', 
                            'route' => route('my-learning'), 
                            'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'
                        ],
                        [
                            'id' => 'mandatory-courses', 
                            'label' => 'Course Wajib', 
                            'route' => route('mandatory-courses'), 
                            'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'
                        ],
                        [
                            'id' => 'my-courses', 
                            'label' => 'My Course Catalog', 
                            'route' => route('my-courses'), 
                            'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'
                        ],
                        // TAMBAHAN MENU BARU: SERTIFIKAT SAYA
                        [
                            'id' => 'my-certificates', 
                            'label' => 'My Certificates', 
                            'route' => route('my-certificates'), 
                            'icon' => 'M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z'
                        ],
                        [
                            'id' => 'profile.settings', 
                            'label' => 'Account Settings', 
                            'route' => route('profile.settings'), 
                            'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37.996.608 2.296.07 2.572-1.065z'
                        ]
                    ];
                @endphp

                @foreach($navs as $nav)
                    @php 
                        $isActive = request()->routeIs($nav['id']); 
                    @endphp
                    
                    <a href="{{ $nav['route'] }}" wire:navigate
                       class="group relative flex items-center gap-4 px-4 py-3 rounded-lg transition-all duration-200 {{ $isActive ? 'bg-[#ED1C24] shadow-md shadow-red-200' : 'hover:bg-gray-50' }}">
                        
                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center transition-all {{ $isActive ? 'bg-white/20 text-white' : 'bg-gray-100 text-gray-400 group-hover:bg-white group-hover:text-[#ED1C24]' }}">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $nav['icon'] }}" />
                            </svg>
                        </div>
                        
                        <span class="text-sm font-bold tracking-tight {{ $isActive ? 'text-white' : 'text-gray-600 group-hover:text-gray-900' }}">
                            {{ $nav['label'] }}
                        </span>

                        @if($isActive)
                            <div class="absolute right-4 w-1.5 h-1.5 bg-white rounded-full"></div>
                        @endif
                    </a>
                @endforeach

                <div class="my-4 px-4 border-t border-gray-100"></div>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-4 px-4 py-3 rounded-lg text-gray-500 hover:text-[#ED1C24] hover:bg-red-50 transition-all duration-200 group">
                        <div class="flex-shrink-0 w-8 h-8 bg-gray-100 rounded-lg flex items-center justify-center group-hover:bg-white transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                            </svg>
                        </div>
                        <span class="text-sm font-bold tracking-widest uppercase italic">Sign Out</span>
                    </button>
                </form>
            </nav>
        </div>
    </div>
</aside>