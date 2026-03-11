<div x-data="{ 
    mobileSidebarOpen: false, 
    videoCompleted: false, 
    initVideo() {
        window.addEventListener('message', (event) => {
            if (event.data && event.data.event === 'onStateChange' && event.data.info === 0) {
                this.videoCompleted = true;
            }
        });
    }
}" 
x-init="initVideo()"
class="flex h-screen bg-white font-sans overflow-hidden">
    
    {{-- 1. OVERLAY MOBILE --}}
    <div x-show="mobileSidebarOpen" 
         @click="mobileSidebarOpen = false"
         x-transition:enter="transition-opacity ease-linear duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition-opacity ease-linear duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden">
    </div>

    {{-- 2. SIDEBAR NAVIGATION --}}
    <div :class="mobileSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
         class="fixed inset-y-0 left-0 z-30 w-80 bg-gray-50 border-r border-gray-200 transition-transform duration-300 ease-in-out lg:translate-x-0 lg:static lg:inset-0 flex flex-col h-full">
        
        {{-- Sidebar Header --}}
        <div class="p-6 border-b border-gray-200 bg-white">
            <h2 class="font-bold text-gray-900 text-lg leading-snug line-clamp-2 mb-3">
                {{ $course->title }}
            </h2>
            
            @php
                $totalLessons = $course->modules->sum(fn($m) => $m->lessons->count());
                $completedCount = count($lessonsCompletedIds);
                $percent = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;
            @endphp

            <div class="w-full bg-gray-200 rounded-full h-2.5 mb-2">
                <div class="bg-[#ED1C24] h-2.5 rounded-full transition-all duration-500" style="width: {{ $percent }}%"></div>
            </div>
            <div class="flex justify-between text-xs font-semibold text-gray-500 uppercase tracking-wide">
                <span>{{ $percent }}% Selesai</span>
                <span>{{ $completedCount }}/{{ $totalLessons }} Materi</span>
            </div>
        </div>

        {{-- Sidebar Content --}}
        <div class="flex-1 overflow-y-auto custom-scrollbar">
            
            @php
                $previousLessonCompleted = true; 
            @endphp

            @foreach($course->modules as $module)
                <div x-data="{ open: true }" class="border-b border-gray-100 last:border-0">
                    <button @click="open = !open" class="flex items-center justify-between w-full px-6 py-4 bg-gray-50 hover:bg-gray-100 transition-colors text-left group">
                        <span class="text-sm font-bold text-gray-800 group-hover:text-gray-900">{{ $module->name }}</span>
                        <svg :class="open ? 'rotate-180' : ''" class="w-4 h-4 text-gray-400 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>

                    <div x-show="open" x-collapse class="bg-white">
                        @foreach($module->lessons as $lesson)
                            @php
                                $isActive = $currentLesson && $currentLesson->id == $lesson->id;
                                $isCompleted = in_array($lesson->id, $lessonsCompletedIds);
                                
                                $isLocked = !$previousLessonCompleted;
                                if ($isCompleted) {
                                    $previousLessonCompleted = true;
                                } else {
                                    $previousLessonCompleted = false;
                                }
                                if($isActive) $isLocked = false;
                            @endphp

                            @if($isLocked)
                                <div class="flex items-start px-6 py-3 text-sm border-l-[3px] border-transparent text-gray-400 bg-gray-50 cursor-not-allowed opacity-75 select-none">
                                    <div class="flex-shrink-0 mt-0.5 mr-3">
                                        <svg class="w-4 h-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                        </svg>
                                    </div>
                                    <span class="leading-snug">{{ $lesson->title }}</span>
                                </div>
                            @else
                                <a href="{{ route('course.player', [$course->slug, $lesson->slug]) }}" 
                                   class="group flex items-start px-6 py-3 text-sm border-l-[3px] transition-all duration-150
                                   {{ $isActive 
                                        ? 'border-[#ED1C24] bg-red-50/50 text-[#ED1C24] font-medium' 
                                        : 'border-transparent text-gray-600 hover:bg-gray-50 hover:text-gray-900' 
                                   }}">
                                    <div class="flex-shrink-0 mt-0.5 mr-3">
                                        @if($isCompleted)
                                            <div class="bg-green-100 rounded-full p-0.5">
                                                <svg class="w-3.5 h-3.5 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                                </svg>
                                            </div>
                                        @elseif($lesson->type == 'video')
                                            <svg class="w-4 h-4 {{ $isActive ? 'text-[#ED1C24]' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @elseif($lesson->type == 'quiz')
                                            <svg class="w-4 h-4 {{ $isActive ? 'text-[#ED1C24]' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                            </svg>
                                        @elseif($lesson->type == 'pdf')
                                            <svg class="w-4 h-4 {{ $isActive ? 'text-[#ED1C24]' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4 {{ $isActive ? 'text-[#ED1C24]' : 'text-gray-400 group-hover:text-gray-500' }}" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                            </svg>
                                        @endif
                                    </div>
                                    <span class="leading-snug">{{ $lesson->title }}</span>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="p-4 border-t border-gray-200 bg-white">
            <a href="{{ url('/my-learning') }}" class="flex items-center justify-center w-full px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg shadow-sm hover:bg-gray-50 transition-colors">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                Kembali ke My Learning
            </a>
        </div>
    </div>
    

    {{-- 3. MAIN CONTENT AREA --}}
    <div class="flex-1 flex flex-col h-full overflow-hidden bg-white relative w-full">
        
        <div class="lg:hidden flex items-center justify-between px-4 py-3 bg-white border-b border-gray-200 shadow-sm z-10">
            <button @click="mobileSidebarOpen = true" class="text-gray-500 hover:text-gray-700 focus:outline-none">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
            </button>
            <span class="font-bold text-gray-800 text-sm truncate max-w-[200px]">{{ $currentLesson->title ?? 'Materi Belajar' }}</span>
            <div class="w-6"></div>
        </div>

        <div class="flex-1 overflow-y-auto bg-white">
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
                
                @if($currentLesson)
                    
                    {{-- CEK STATUS UNTUK TOMBOL SELESAI --}}
                    @if($currentLesson->type == 'video' && $currentLesson->video_url)
                        {{-- Video player set videoCompleted via API --}}
                    @elseif($currentLesson->type == 'pdf' || $currentLesson->type == 'quiz')
                        <div x-init="videoCompleted = false"></div>
                    @else
                        <div x-init="videoCompleted = true"></div>
                    @endif

                    {{-- 1. RENDER VIDEO PLAYER --}}
                    @if($currentLesson->type == 'video' && $currentLesson->video_url)
                        @php
                            $videoUrl = $currentLesson->video_url;
                            $videoId = null;
                            $pattern = '%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i';
                            
                            if (preg_match($pattern, $videoUrl, $match)) {
                                $videoId = $match[1];
                            }
                        @endphp

                        <div class="mb-10">
                        @if($videoId)
                            {{-- WRAPPER YOUTUBE UTAMA --}}
                            <div x-data="{
                                    player: null,
                                    isPlaying: false,
                                    isFullscreen: false,
                                    videoProgress: 0, 
                                    progressTimer: null, 
                                    currentTimeDisplay: '00:00',
                                    durationDisplay: '00:00',

                                    formatTime(time) {
                                        if (isNaN(time) || time < 0) return '00:00';
                                        let minutes = Math.floor(time / 60);
                                        let seconds = Math.floor(time % 60);
                                        return (minutes < 10 ? '0' : '') + minutes + ':' + (seconds < 10 ? '0' : '') + seconds;
                                    },

                                    initYoutube() {
                                        var tag = document.createElement('script');
                                        tag.src = 'https://www.youtube.com/iframe_api';
                                        var firstScriptTag = document.getElementsByTagName('script')[0];
                                        firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

                                        window.onYouTubeIframeAPIReady = () => {
                                            this.player = new YT.Player('youtube-player', {
                                                events: {
                                                    'onReady': () => {
                                                        setTimeout(() => {
                                                            if(this.player.getDuration() > 0) {
                                                                this.durationDisplay = this.formatTime(this.player.getDuration());
                                                            }
                                                        }, 1000);
                                                    },
                                                    'onStateChange': (event) => {
                                                        this.isPlaying = (event.data == YT.PlayerState.PLAYING);
                                                        
                                                        if (this.isPlaying) {
                                                            this.startProgressTimer();
                                                            this.durationDisplay = this.formatTime(this.player.getDuration());
                                                        } else {
                                                            this.stopProgressTimer();
                                                        }

                                                        if (event.data === 0) {
                                                            window.postMessage({ event: 'onStateChange', info: 0 }, '*');
                                                            this.player.stopVideo(); 
                                                            this.isPlaying = false;
                                                            this.videoProgress = 100; 
                                                            this.currentTimeDisplay = this.durationDisplay;
                                                            if(this.isFullscreen) this.toggleFullscreen();
                                                        }
                                                    }
                                                }
                                            });
                                        };

                                        document.addEventListener('fullscreenchange', () => {
                                            this.checkFullscreenState();
                                        });
                                    },
                                    startProgressTimer() {
                                        this.stopProgressTimer();
                                        this.progressTimer = setInterval(() => {
                                            if (this.player && typeof this.player.getCurrentTime === 'function') {
                                                let duration = this.player.getDuration();
                                                let currentTime = this.player.getCurrentTime();
                                                
                                                if(duration > 0) {
                                                    this.videoProgress = (currentTime / duration) * 100;
                                                    this.currentTimeDisplay = this.formatTime(currentTime);
                                                }
                                            }
                                        }, 500); 
                                    },
                                    stopProgressTimer() {
                                        if (this.progressTimer) {
                                            clearInterval(this.progressTimer);
                                            this.progressTimer = null;
                                        }
                                    },
                                    checkFullscreenState() {
                                        this.isFullscreen = !!(document.fullscreenElement || document.webkitFullscreenElement);
                                    },
                                    togglePlay() {
                                        if (this.player && typeof this.player.getPlayerState === 'function') {
                                            if (this.player.getPlayerState() == YT.PlayerState.PLAYING) {
                                                this.player.pauseVideo();
                                            } else {
                                                this.player.playVideo();
                                            }
                                        }
                                    },
                                    backward10s() {
                                        if (this.player && typeof this.player.getCurrentTime === 'function') {
                                            let currentTime = this.player.getCurrentTime();
                                            let newTime = Math.max(currentTime - 10, 0);
                                            
                                            this.player.seekTo(newTime, true);
                                            this.videoProgress = (newTime / this.player.getDuration()) * 100;
                                            this.currentTimeDisplay = this.formatTime(newTime);
                                        }
                                    },
                                    toggleFullscreen() {
                                        let container = this.$refs.videoContainer;
                                        
                                        if (!document.fullscreenElement && !document.webkitFullscreenElement) {
                                            if (container.requestFullscreen) {
                                                container.requestFullscreen().catch(err => {
                                                    this.isFullscreen = true; 
                                                });
                                            } else if (container.webkitRequestFullscreen) {
                                                container.webkitRequestFullscreen();
                                            } else {
                                                this.isFullscreen = !this.isFullscreen;
                                            }
                                        } else {
                                            if (document.exitFullscreen) document.exitFullscreen();
                                            else if (document.webkitExitFullscreen) document.webkitExitFullscreen();
                                            
                                            this.isFullscreen = false;
                                        }
                                    }
                                }"
                                x-init="initYoutube()"
                                x-ref="videoContainer"
                                :class="isFullscreen ? 'fixed inset-0 z-50 w-full h-full bg-black flex items-center justify-center' : 'relative w-full bg-black rounded-2xl shadow-2xl overflow-hidden group aspect-video'"
                                >

                                <iframe id="youtube-player"
                                        src="https://www.youtube.com/embed/{{ $videoId }}?enablejsapi=1&controls=0&disablekb=1&modestbranding=1&rel=0&iv_load_policy=3&fs=0" 
                                        class="absolute inset-0 w-full h-full pointer-events-none" 
                                        frameborder="0" 
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" 
                                        allowfullscreen>
                                </iframe>

                                {{-- AREA KLIK UNTUK PLAY/PAUSE --}}
                                <div @click="togglePlay()" 
                                     @contextmenu.prevent 
                                     class="absolute inset-0 z-10 w-full h-full bg-transparent cursor-pointer flex items-center justify-center">
                                    
                                    <div x-show="!isPlaying" 
                                         x-transition:enter="transition ease-out duration-300"
                                         x-transition:enter-start="opacity-0 scale-50"
                                         x-transition:enter-end="opacity-100 scale-100"
                                         class="w-16 h-16 md:w-20 md:h-20 bg-red-600 rounded-full flex items-center justify-center shadow-lg transform transition-transform duration-200 hover:scale-110">
                                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white ml-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z" />
                                        </svg>
                                    </div>

                                    <div x-show="isPlaying" 
                                         class="opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-black/50 p-3 md:p-4 rounded-full backdrop-blur-sm">
                                        <svg class="w-8 h-8 md:w-10 md:h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zM7 8a1 1 0 012 0v4a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v4a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- KUMPULAN KONTROL KIRI BAWAH --}}
                                <div class="absolute bottom-6 left-4 z-20 flex items-center gap-2 md:gap-3">
                                    <button @click="backward10s()"
                                            class="p-2 md:px-4 md:py-2 rounded-lg text-white bg-black/60 hover:bg-black/80 shadow-lg backdrop-blur-sm transition-colors duration-200 focus:outline-none flex items-center gap-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0019 16V8a1 1 0 00-1.6-.8l-5.333 4zM4.066 11.2a1 1 0 000 1.6l5.334 4A1 1 0 0011 16V8a1 1 0 00-1.6-.8l-5.334 4z" />
                                        </svg>
                                        <span class="text-xs font-bold tracking-wider hidden md:inline-block">10s</span>
                                    </button>

                                    <div class="px-3 py-2 rounded-lg text-white bg-black/60 shadow-lg backdrop-blur-sm flex items-center text-xs md:text-sm font-medium tracking-wider font-mono">
                                        <span x-text="currentTimeDisplay">00:00</span>
                                        <span class="text-gray-400 mx-1.5">/</span>
                                        <span x-text="durationDisplay" class="text-gray-300">00:00</span>
                                    </div>
                                </div>

                                {{-- TOMBOL FULLSCREEN --}}
                                <button @click="toggleFullscreen()"
                                        class="absolute bottom-6 right-4 z-20 p-2.5 rounded-lg text-white bg-black/60 hover:bg-black/80 shadow-lg backdrop-blur-sm transition-colors duration-200 focus:outline-none">
                                    <svg x-show="!isFullscreen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                    </svg>
                                    <svg x-show="isFullscreen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 drop-shadow-md" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                         <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v4h4m-4-4l5 5M20 8V4h-4m4 4l5-5M4 8V4h4m-4 4l5-5M20 16v4h-4m4-4l-5 5" />
                                    </svg>
                                </button>

                                <div class="absolute bottom-0 left-0 right-0 z-20 h-1.5 bg-gray-600">
                                    <div class="h-full bg-red-600 transition-all duration-500 ease-linear" :style="`width: ${videoProgress}%`"></div>
                                </div>

                            </div>
                        @else
                            <div class="relative w-full bg-black rounded-2xl shadow-2xl overflow-hidden" style="aspect-ratio: 16/9;">
                                <video x-ref="player" 
                                       @ended="videoCompleted = true" 
                                       controls controlsList="nodownload" oncontextmenu="return false;"
                                       class="absolute inset-0 w-full h-full object-contain bg-black">
                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                </video>
                            </div>
                        @endif
                        </div>
                    @endif

                    {{-- 2. RENDER PDF READER DENGAN TIMER --}}
                    @if($currentLesson->type == 'pdf' && $currentLesson->content)
                        <div x-data="{
                            timeLeft: {{ $currentLesson->min_viewing_seconds ?? 0 }},
                            initPdfTimer() {
                                if(this.timeLeft <= 0) { $data.videoCompleted = true; return; }
                                let interval = setInterval(() => {
                                    this.timeLeft--;
                                    if (this.timeLeft <= 0) {
                                        clearInterval(interval);
                                        $data.videoCompleted = true;
                                    }
                                }, 1000);
                            },
                            formatSeconds(sec) {
                                let m = Math.floor(sec / 60);
                                let s = sec % 60;
                                return (m > 0 ? m + 'm ' : '') + s + 's';
                            }
                        }" x-init="initPdfTimer()" class="mb-10">
                            
                            {{-- Info Bar Timer --}}
                            <div class="flex items-center justify-between bg-gray-900 text-white px-6 py-3 rounded-t-2xl">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                    </svg>
                                    <span class="text-sm font-bold truncate">Materi Dokumen PDF</span>
                                </div>
                                
                                <div x-show="timeLeft > 0" class="flex items-center gap-2 bg-white/10 px-3 py-1 rounded-full border border-white/20">
                                    <span class="text-xs uppercase tracking-widest font-black animate-pulse text-red-400">Time Left:</span>
                                    <span class="font-mono font-bold text-sm" x-text="formatSeconds(timeLeft)"></span>
                                </div>
                                <div x-show="timeLeft <= 0" class="flex items-center gap-2 bg-green-500 px-3 py-1 rounded-full shadow-lg">
                                    <svg class="w-4 h-4 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                                    <span class="text-xs font-black uppercase">Selesai Dibaca</span>
                                </div>
                            </div>

                            {{-- PDF Viewer (Iframe) --}}
                            <div class="relative w-full bg-gray-200 rounded-b-2xl shadow-2xl overflow-hidden" style="height: 80vh;">
                                <iframe src="{{ asset('storage/' . $currentLesson->content) }}#toolbar=0" 
                                        class="w-full h-full border-none">
                                </iframe>
                            </div>
                        </div>
                    @endif

                    {{-- HEADER JUDUL & TOMBOL KANAN --}}
                    <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-8 border-b border-gray-100 pb-8">
                        <div>
                            <div class="flex items-center text-sm text-gray-500 mb-2">
                                <span class="uppercase tracking-wider font-bold text-xs">{{ $course->category->name ?? 'Course' }}</span>
                                <span class="mx-2">•</span>
                                <span>{{ $currentLesson->duration_minutes ?? '5' }} Menit</span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-extrabold text-gray-900 tracking-tight leading-tight">
                                {{ $currentLesson->title }}
                            </h1>
                        </div>

                        <div class="flex-shrink-0">
                            @if(in_array($currentLesson->id, $lessonsCompletedIds))
                                <button disabled class="inline-flex items-center px-4 py-2 border border-gray-200 text-sm font-bold rounded-lg text-green-700 bg-green-50 cursor-default opacity-80">
                                    <svg class="w-4 h-4 mr-2 -ml-0.5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Sudah Selesai
                                </button>
                            @else
                                <button x-show="!videoCompleted" disabled
                                    class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-sm text-gray-400 bg-gray-100 cursor-not-allowed">
                                    <svg class="w-4 h-4 mr-2 -ml-0.5 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        @if($currentLesson->type == 'quiz')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                        @elseif($currentLesson->type == 'pdf')
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                        @else
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        @endif
                                    </svg>
                                    {{ $currentLesson->type == 'quiz' ? 'Selesaikan Kuis...' : ($currentLesson->type == 'pdf' ? 'Baca Sampai Selesai...' : 'Tonton Sampai Selesai...') }}
                                </button>
                                
                                <button x-show="videoCompleted" 
                                        wire:click="markAsComplete" 
                                        x-transition:enter="transition ease-out duration-300"
                                        x-transition:enter-start="opacity-0 transform scale-90"
                                        x-transition:enter-end="opacity-100 transform scale-100"
                                        class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-bold rounded-lg shadow-sm text-white bg-[#ED1C24] hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all transform active:scale-95 animate-bounce">
                                    <svg class="w-4 h-4 mr-2 -ml-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                    </svg>
                                    Selesai & Lanjutkan
                                </button>
                            @endif
                        </div>
                    </div>

                    {{-- KONTEN TEKS / DESKRIPSI (Hanya tampil jika bukan PDF) --}}
                    @if($currentLesson->content && $currentLesson->type !== 'pdf')
                        <div class="prose prose-lg prose-red max-w-none text-gray-700 leading-relaxed mb-10">
                            {!! $currentLesson->content !!}
                        </div>
                    @endif

                    {{-- ========================================================= --}}
                    {{-- BLOK RENDER KUIS INTERAKTIF JIKA TIPE NYA ADALAH "QUIZ" --}}
                    {{-- ========================================================= --}}
                    @if($currentLesson->type === 'quiz' && !empty($currentLesson->quiz_data))
                        @php
                            $quizData = is_array($currentLesson->quiz_data) 
                                        ? $currentLesson->quiz_data 
                                        : json_decode($currentLesson->quiz_data, true);
                            
                            $initialAttempts = DB::table('lesson_user')
                                ->where('user_id', Auth::id())
                                ->where('lesson_id', $currentLesson->id)
                                ->where('course_id', $course->id)
                                ->value('failed_attempts') ?? 0;
                        @endphp
                        
                        @if(is_array($quizData) && count($quizData) > 0)
                            <div x-data="{
                                questions: {{ json_encode($quizData) }},
                                selectedAnswers: {},
                                showResults: false,
                                score: 0,
                                passed: false,
                                attempts: {{ $initialAttempts }},
                                lockTimer: 0,
                                timerInterval: null,

                                selectAnswer(qIndex, answer) {
                                    if (this.showResults || this.lockTimer > 0) return;
                                    this.selectedAnswers[qIndex] = answer;
                                },
                                
                                submitQuiz() {
                                    if (Object.keys(this.selectedAnswers).length < this.questions.length) {
                                        alert('Harap jawab semua pertanyaan kuis terlebih dahulu!');
                                        return;
                                    }

                                    this.score = 0;
                                    this.questions.forEach((q, index) => {
                                        if (this.selectedAnswers[index] === q.correct_answer) {
                                            this.score++;
                                        }
                                    });
                                    
                                    this.lockTimer = 10;
                                    this.showResults = true;
                                    
                                    if (this.score === this.questions.length) {
                                        this.passed = true;
                                        $data.videoCompleted = true; 
                                    } else {
                                        this.startLockdown();
                                        
                                        this.$wire.recordFailedAttempt().then(result => {
                                            if (result !== null) {
                                                this.attempts = result;
                                                
                                                if (this.attempts >= 3) {
                                                    // Gagal 3x, tampilkan pesan error sesaat lalu redirect
                                                    this.lockTimer = 999; // Kunci permanen
                                                    if (this.timerInterval) clearInterval(this.timerInterval);
                                                    
                                                    // Jika Livewire mengembalikan URL redirect
                                                    window.location.reload(); // Atau bisa langsung redirect dari backend jika di-handle Livewire
                                                }
                                            }
                                        });
                                    }
                                },

                                startLockdown() {
                                    if (this.timerInterval) clearInterval(this.timerInterval);
                                    
                                    this.timerInterval = setInterval(() => {
                                        if (this.lockTimer > 1) {
                                            this.lockTimer--;
                                        } else {
                                            this.lockTimer = 0;
                                            clearInterval(this.timerInterval);
                                            this.timerInterval = null;
                                        }
                                    }, 1000);
                                },
                                
                                resetQuiz() {
                                    if (this.timerInterval) clearInterval(this.timerInterval);
                                    this.timerInterval = null;
                                    this.lockTimer = 0;
                                    this.selectedAnswers = {};
                                    this.showResults = false;
                                    this.score = 0;
                                    this.passed = false;
                                    $data.videoCompleted = false;
                                }
                            }" 
                            class="mt-10 bg-white border border-gray-200 shadow-sm rounded-2xl p-6 md:p-8">
                                
                                <h3 class="text-xl font-bold text-gray-900 border-b border-gray-200 pb-4 mb-6 flex items-center gap-2">
                                    <svg class="w-6 h-6 text-[#ED1C24]" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                                    </svg>
                                    Kuis Evaluasi Materi
                                </h3>

                                <div x-show="!showResults">
                                    <template x-for="(q, index) in questions" :key="index">
                                        <div class="mb-8 bg-gray-50/50 p-6 rounded-xl border border-gray-100">
                                            <p class="font-semibold text-gray-800 text-lg mb-4" x-text="(index + 1) + '. ' + q.question"></p>
                                            
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                                <template x-for="opt in ['a', 'b', 'c', 'd']">
                                                    <label class="flex items-center p-4 border rounded-xl cursor-pointer transition-all duration-200"
                                                           :class="selectedAnswers[index] === opt ? 'border-[#ED1C24] bg-red-50/50 ring-1 ring-[#ED1C24]' : 'border-gray-200 bg-white hover:border-gray-300 hover:bg-gray-50'">
                                                        <input type="radio" :name="'q_' + index" :value="opt" @click="selectAnswer(index, opt)" class="hidden">
                                                        <div class="w-5 h-5 rounded-full border-2 flex-shrink-0 mr-3 flex items-center justify-center transition-colors"
                                                              :class="selectedAnswers[index] === opt ? 'border-[#ED1C24]' : 'border-gray-300'">
                                                            <div class="w-2.5 h-2.5 rounded-full bg-[#ED1C24]" x-show="selectedAnswers[index] === opt"></div>
                                                        </div>
                                                        <span class="text-gray-700 font-medium" x-text="opt.toUpperCase() + '. ' + q['option_' + opt]"></span>
                                                    </label>
                                                </template>
                                            </div>
                                        </div>
                                    </template>

                                    <div class="flex justify-end mt-8 border-t border-gray-100 pt-6">
                                        <button @click="submitQuiz()" class="w-full sm:w-auto px-8 py-3 bg-gray-900 hover:bg-black text-white font-bold rounded-xl shadow-md transition-colors flex items-center justify-center gap-2">
                                            Kirim Jawaban
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- LAYAR HASIL KUIS --}}
                                <div x-show="showResults" style="display: none;" class="text-center py-8">
                                    <div x-show="passed" class="mx-auto w-20 h-20 bg-green-100 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                    <div x-show="!passed" class="mx-auto w-20 h-20 bg-red-100 rounded-full flex items-center justify-center mb-6">
                                        <svg class="w-10 h-10 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                    
                                    <h3 class="text-2xl font-black text-gray-900 mb-2" x-text="passed ? 'Luar Biasa!' : 'Ops! Belum Lulus.'"></h3>
                                    <p class="text-gray-600 mb-6 text-lg">Skor Kuis Anda: <span class="font-bold text-gray-900" x-text="Math.round((score / questions.length) * 100)"></span> / 100</p>
                                    
                                    <div x-show="!passed" class="bg-red-50 p-6 rounded-xl border border-red-100 max-w-md mx-auto shadow-sm">
                                        <p class="text-red-700 font-medium mb-6">
                                            <template x-if="attempts < 3">
                                                <span>
                                                    Jawaban salah! Kamu sudah gagal <span class="font-bold" x-text="attempts"></span> kali. 
                                                    Sisa <span class="font-black" x-text="3 - attempts"></span> kesempatan lagi sebelum remedial.
                                                </span>
                                            </template>
                                            <template x-if="attempts >= 3">
                                                <span class="font-bold text-red-800">
                                                    Gagal 3x! Mereset materi sebelumnya...
                                                </span>
                                            </template>
                                        </p>
                                        
                                        <template x-if="attempts < 3">
                                            <button @click="resetQuiz()" 
                                                    :disabled="lockTimer > 0"
                                                    class="px-8 py-3 bg-white border border-gray-300 hover:bg-gray-50 text-gray-800 font-bold rounded-xl shadow-sm transition-colors flex items-center justify-center gap-2 mx-auto disabled:opacity-50 disabled:cursor-not-allowed">
                                                <template x-if="lockTimer > 0">
                                                    <span x-text="'Tunggu ' + lockTimer + ' detik...'"></span>
                                                </template>
                                                <template x-if="lockTimer <= 0">
                                                    <span>Coba Lagi Sekarang</span>
                                                </template>
                                            </button>
                                        </template>

                                        <template x-if="attempts >= 3">
                                            <div class="flex justify-center mt-4">
                                                <svg class="animate-spin h-8 w-8 text-red-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </template>
                                    </div>

                                    <div x-show="passed" class="bg-green-50 p-6 rounded-xl border border-green-100 max-w-md mx-auto text-green-700 shadow-sm font-medium">
                                        Lulus dengan Sempurna! Silakan klik tombol <b>Selesai & Lanjutkan</b> di atas untuk materi berikutnya.
                                    </div>
                                </div>

                            </div>
                        @endif
                    @endif

                @else
                    <div class="flex flex-col items-center justify-center h-full py-20 text-center">
                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-10 h-10 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900">Materi Tidak Ditemukan</h3>
                        <p class="text-gray-500 mt-2">Silakan pilih materi lain dari menu di sebelah kiri.</p>
                    </div>
                @endif
                
            </div>
            
            <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-6 border-t border-gray-100 mt-10">
                <p class="text-center text-xs text-gray-400">
                    &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
                </p>
            </div>
        </div>
    </div>
</div>

