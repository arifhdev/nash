<div class="min-h-screen bg-[#F8F9FA] font-sans pb-16">
    {{-- Decorative Background --}}
    <div class="absolute inset-0 z-0 bg-gradient-to-b from-[#ED1C24]/5 to-transparent h-96 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 pt-12">
        <div class="flex flex-col lg:flex-row gap-8 lg:gap-12">
            
            {{-- PANGGIL KOMPONEN SIDEBAR DI SINI --}}
            <x-learning-sidebar />

            {{-- Main Content Area --}}
            <main class="flex-1 min-w-0">
                
                {{-- Header Section --}}
                <div class="bg-white rounded-3xl p-8 sm:p-10 shadow-sm border border-gray-100 mb-8 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-10 -mr-10 text-red-50 opacity-50 transform rotate-12">
                        <svg class="w-64 h-64" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path></svg>
                    </div>
                    
                    <div class="relative z-10 flex items-start gap-6">
                        <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center flex-shrink-0 border border-red-100">
                            <svg class="w-8 h-8 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Sertifikat Saya</h1>
                            <p class="text-gray-500 text-lg leading-relaxed max-w-2xl">
                                Daftar sertifikat kelulusan yang telah Anda raih dari Akademi Satu Hati.
                            </p>
                        </div>
                    </div>
                </div>

                {{-- Content Area --}}
                @if($certificates->isEmpty())
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-12 text-center mt-8">
                        <div class="w-24 h-24 bg-red-50 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Belum Ada Sertifikat</h3>
                        <p class="text-gray-600 mb-8 leading-relaxed max-w-lg mx-auto">Anda belum mendapatkan sertifikat. Selesaikan kursus hingga mencapai progress 100% dan lulus kuis untuk mendapatkan sertifikat kelulusan.</p>
                        <a href="{{ route('courses.index') }}" class="inline-flex items-center px-8 py-3 bg-[#ED1C24] hover:bg-red-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95 gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Jelajahi Kursus
                        </a>
                    </div>
                @else
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                        @foreach($certificates as $cert)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-200 overflow-hidden flex flex-col transition-all duration-300 hover:-translate-y-1 hover:shadow-xl group">
                                
                                <div class="h-32 bg-gray-900 relative p-6 flex flex-col justify-end overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-10 transform group-hover:scale-110 transition-transform duration-500">
                                        <svg class="w-20 h-20 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                    </div>
                                    <span class="text-xs font-bold text-red-400 uppercase tracking-wider mb-1 z-10">No. Registrasi</span>
                                    <span class="text-lg font-mono font-bold text-white leading-tight truncate z-10">{{ $cert->certificate_number }}</span>
                                </div>

                                <div class="p-6 flex-1 flex flex-col">
                                    <div class="flex-1">
                                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 group-hover:text-[#ED1C24] transition-colors">{{ $cert->course->title }}</h3>
                                        
                                        <div class="flex items-center text-sm text-gray-500 mb-6 bg-gray-50 px-3 py-2 rounded-lg border border-gray-100">
                                            <svg class="w-4 h-4 mr-2 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                            Diterbitkan: <span class="font-bold text-gray-700 ml-1">{{ \Carbon\Carbon::parse($cert->issued_at)->translatedFormat('d F Y') }}</span>
                                        </div>
                                    </div>

                                    <a href="{{ route('certificate.download', ['certNumber' => urlencode($cert->certificate_number)]) }}" 
                                       target="_blank" 
                                       class="w-full py-3 bg-gray-100 hover:bg-[#ED1C24] text-gray-800 hover:text-white font-bold rounded-xl transition-colors flex items-center justify-center gap-2 border border-gray-200 hover:border-transparent">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                        Download PDF
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
                
            </main>
        </div>
    </div>
</div>