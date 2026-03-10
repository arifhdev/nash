<section class="py-16 lg:py-24 bg-white overflow-hidden" id="testimonial">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center">
            
            {{-- BAGIAN KIRI: SLIDER TESTIMONIAL --}}
            <div class="relative" 
                 x-data="{ 
                    active: 0,
                    testimonials: {{ $testimonials->count() > 0 
                        ? $testimonials->map(fn($t) => [
                            'name' => $t->name,
                            'role' => $t->role,
                            'quote' => $t->quote,
                            'image' => Storage::url($t->avatar),
                            'rating' => (int)$t->rating
                        ])->toJson() 
                        : json_encode([
                            [
                                'name' => 'Jay Idzes',
                                'role' => 'Mekanik Senior - Astra Motor NTB',
                                'quote' => 'Materi di Akademi Satu Hati sangat update dengan teknologi terbaru Honda. Sertifikasinya sangat membantu jenjang karir saya.',
                                'image' => 'https://placehold.co/400x500/e2e8f0/1e293b?text=Mechanic+1',
                                'rating' => 5
                            ]
                        ]) 
                    }},
                    next() { this.active = (this.active + 1) % this.testimonials.length },
                    prev() { this.active = (this.active - 1 + this.testimonials.length) % this.testimonials.length }
                 }"
                 x-init="if(testimonials.length > 1) setInterval(() => next(), 5000)">

                {{-- Background Blob Decoration --}}
                <div class="absolute top-10 left-10 w-64 h-64 bg-red-100 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob"></div>
                <div class="absolute top-10 right-10 w-64 h-64 bg-gray-200 rounded-full mix-blend-multiply filter blur-3xl opacity-70 animate-blob animation-delay-2000"></div>

                {{-- Slider Container --}}
                <div class="relative z-10 mx-auto max-w-sm lg:max-w-md">
                    
                    <template x-for="(item, index) in testimonials" :key="index">
                        <div x-show="active === index"
                             x-transition:enter="transition ease-out duration-500"
                             x-transition:enter-start="opacity-0 translate-x-8"
                             x-transition:enter-end="opacity-100 translate-x-0"
                             x-transition:leave="transition ease-in duration-300 absolute top-0 left-0 w-full"
                             x-transition:leave-start="opacity-100 translate-x-0"
                             x-transition:leave-end="opacity-0 -translate-x-8">
                            
                            {{-- Foto Orang --}}
                            <div class="rounded-3xl overflow-hidden shadow-2xl border-4 border-white h-[450px] w-full bg-gray-100">
                                <img :src="item.image" :alt="item.name" class="w-full h-full object-cover object-top">
                            </div>

                            {{-- Kartu Testimonial (Floating) --}}
                            <div class="absolute bottom-6 -left-4 right-4 bg-gray-100/95 backdrop-blur-md p-6 rounded-2xl shadow-lg border border-white/50">
                                <div class="flex flex-col gap-2">
                                    {{-- Nama & Role --}}
                                    <div>
                                        <h4 class="text-lg font-bold text-gray-900" x-text="item.name"></h4>
                                        <p class="text-xs text-[#ED1C24] font-semibold uppercase tracking-wider" x-text="item.role"></p>
                                    </div>
                                    
                                    {{-- Quote --}}
                                    <p class="text-gray-600 text-sm italic leading-relaxed">
                                        "<span x-text="item.quote"></span>"
                                    </p>

                                    {{-- Stars Dinamis --}}
                                    <div class="flex text-yellow-400 gap-1 mt-1">
                                        <template x-for="i in item.rating">
                                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                                        </template>
                                    </div>
                                </div>
                            </div>

                        </div>
                    </template>

                    {{-- Navigation Dots --}}
                    <div class="absolute -bottom-10 left-0 right-0 flex justify-center space-x-2" x-show="testimonials.length > 1">
                        <template x-for="(item, index) in testimonials" :key="index">
                            <button @click="active = index" 
                                    class="w-2.5 h-2.5 rounded-full transition-all duration-300"
                                    :class="active === index ? 'bg-[#ED1C24] w-8' : 'bg-gray-300 hover:bg-gray-400'"></button>
                        </template>
                    </div>

                </div>
            </div>

            {{-- BAGIAN KANAN: LIST BENEFIT (VALUE PROPOSITION) --}}
            <div class="mt-12 lg:mt-0">
                <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6 leading-tight">
                    Mengapa Bergabung di <br>
                    <span class="text-[#ED1C24]">Akademi Satu Hati?</span>
                </h2>
                
                <p class="text-gray-500 text-lg mb-8 leading-relaxed">
                    Kami berkomitmen mencetak SDM unggul di industri otomotif dengan standar kualitas Astra Honda Motor. Dapatkan akses eksklusif ke berbagai keuntungan berikut:
                </p>

                {{-- List Keuntungan --}}
                <ul class="space-y-5 mb-10">
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ml-4 text-gray-700 font-medium">Kurikulum Standar Global Honda</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ml-4 text-gray-700 font-medium">Sertifikasi Resmi Diakui Industri</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ml-4 text-gray-700 font-medium">Materi Teknologi Terbaru (eSP+, EV, Hybrid)</span>
                    </li>
                    <li class="flex items-start">
                        <div class="flex-shrink-0 w-6 h-6 rounded-full bg-red-100 flex items-center justify-center mt-1">
                            <svg class="w-4 h-4 text-[#ED1C24]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <span class="ml-4 text-gray-700 font-medium">Akses Belajar Fleksibel (Hybrid Learning)</span>
                    </li>
                </ul>

                {{-- Button CTA --}}
                <div class="flex flex-col sm:flex-row gap-4">
                    <a href="{{ route('courses.index') }}" wire:navigate class="inline-flex items-center justify-center px-8 py-3 text-base font-semibold text-white bg-[#ED1C24] rounded-full hover:bg-red-700 transition shadow-lg hover:shadow-red-500/30">
                        Pelajari Selengkapnya
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>