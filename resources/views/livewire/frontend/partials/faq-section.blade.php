<section class="py-16 bg-gray-50" id="faq">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- Header Section --}}
        <div class="text-center max-w-3xl mx-auto mb-12">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-4">
                Frequently Asked <span class="text-[#ED1C24]">Questions</span>
            </h2>
            <p class="text-gray-500 text-lg">
                Pertanyaan umum seputar pendaftaran, materi, dan teknis pelaksanaan di Akademi Satu Hati.
            </p>
        </div>

        {{-- Grid FAQ --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 max-w-6xl mx-auto">
            
            {{-- Loop Data dari Database --}}
            @foreach($faqs as $faq)
                <div x-data="{ open: false }" class="bg-white rounded-2xl shadow-[0_2px_15px_rgb(0,0,0,0.03)] hover:shadow-lg transition-shadow duration-300 border border-gray-100 overflow-hidden h-fit">
                    
                    {{-- Tombol Judul --}}
                    <button @click="open = !open" class="w-full text-left px-6 py-5 flex items-center justify-between focus:outline-none">
                        <span class="text-base font-bold text-gray-900 group-hover:text-[#ED1C24] transition-colors" :class="{'text-[#ED1C24]': open}">
                            {{ $faq->question }} {{-- Menggunakan properti object --}}
                        </span>
                        
                        <span class="flex-shrink-0 ml-4 bg-gray-100 rounded-full p-2 transition-transform duration-300" :class="{'rotate-45 bg-red-50 text-red-600': open, 'text-gray-500': !open}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                        </span>
                    </button>

                    {{-- Isi Jawaban --}}
                    <div x-show="open" 
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-150"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-2"
                         class="px-6 pb-6 text-gray-600 leading-relaxed">
                        {{ $faq->answer }} {{-- Menggunakan properti object --}}
                    </div>
                </div>
            @endforeach

        </div>

        {{-- Footer Call to Action (Optional) --}}
        <div class="text-center mt-12">
            <p class="text-gray-500">Masih punya pertanyaan lain?</p>
            <a href="#" class="inline-block mt-2 text-[#ED1C24] font-semibold hover:underline">
                Hubungi Tim Support Kami &rarr;
            </a>
        </div>

    </div>
</section>