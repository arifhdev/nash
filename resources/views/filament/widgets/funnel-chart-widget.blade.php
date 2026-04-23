<x-filament-widgets::widget>
    <x-filament::section heading="Users Funnel Analysis">
        
        <div class="mb-8">
            {{ $this->form }}
        </div>

        <div class="w-full mt-6 overflow-x-auto pb-10">
            @php
                $maxValue = max(1, max(array_column($funnelData, 'value')));
                $baseValue = max(1, $funnelData[0]['value']); 
                
                $count = count($funnelData);
                $svgWidth = 1200; 
                $svgHeight = 160; 
                $sectionWidth = $svgWidth / $count;
            @endphp

            <div class="min-w-[900px] flex flex-col">
                
                <svg viewBox="0 0 {{ $svgWidth }} {{ $svgHeight }}" class="w-full h-auto drop-shadow-sm" preserveAspectRatio="xMidYMid meet">
                    @foreach($funnelData as $index => $stage)
                        @php
                            $currentVal = $stage['value'];
                            $nextVal = $index < ($count - 1) ? $funnelData[$index + 1]['value'] : $currentVal;
                            
                            $currentH = ($currentVal / $maxValue) * $svgHeight;
                            $nextH = ($nextVal / $maxValue) * $svgHeight;
                            
                            $currentH = max($currentH, 6);
                            $nextH = max($nextH, 6);

                            $x1 = $index * $sectionWidth;
                            $x2 = ($index + 1) * $sectionWidth;

                            $y1_top = ($svgHeight - $currentH) / 2;
                            $y1_bottom = $y1_top + $currentH;

                            $y2_top = ($svgHeight - $nextH) / 2;
                            $y2_bottom = $y2_top + $nextH;

                            $hasUrl = !empty($stage['url']);
                        @endphp

                        @if($hasUrl)
                            <a href="{{ $stage['url'] }}" class="cursor-pointer outline-none">
                        @endif

                            <polygon 
                                points="{{ $x1 }},{{ $y1_top }} {{ $x2 }},{{ $y2_top }} {{ $x2 }},{{ $y2_bottom }} {{ $x1 }},{{ $y1_bottom }}" 
                                fill="{{ $stage['color'] }}" 
                                stroke="#ffffff" 
                                stroke-width="2"
                                class="transition-all duration-300 hover:opacity-80 {{ $hasUrl ? 'cursor-pointer' : '' }}"
                            >
                                <title>{{ $stage['label'] }}: {{ number_format($currentVal) }}{{ $hasUrl ? ' (Klik untuk melihat detail)' : '' }}</title>
                            </polygon>

                        @if($hasUrl)
                            </a>
                        @endif
                    @endforeach
                </svg>

                <div class="flex w-full mt-6 border-t border-gray-100 dark:border-gray-800 pt-6">
                    @foreach($funnelData as $index => $stage)
                        @php
                            $percent = ($stage['value'] / $baseValue) * 100;
                            // Menangani kasus angka sangat kecil agar tidak tampil 0% jika aslinya ada data
                            $percentFormatted = ($percent > 0 && $percent < 0.01) ? '< 0.01' : round($percent, 2);
                            
                            $hasUrl = !empty($stage['url']);
                        @endphp

                        <div class="flex-1 flex flex-col items-center text-center px-2 group {{ $hasUrl ? 'cursor-pointer hover:opacity-90' : '' }}"
                             @if($hasUrl) onclick="window.location.href='{{ $stage['url'] }}'" @endif>
                            
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-400 dark:text-gray-500 mb-2 {{ $hasUrl ? 'group-hover:text-primary-500 transition-colors' : '' }}">
                                {{ $stage['label'] }}
                            </span>
                            
                            <span class="text-3xl font-black text-gray-800 dark:text-gray-100 mb-3 transition-all duration-300 {{ $hasUrl ? 'group-hover:scale-110 group-hover:text-primary-600' : 'group-hover:scale-105' }}">
                                {{ number_format($stage['value']) }}
                            </span>

                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-opacity-10 shadow-sm border border-opacity-20" 
                                 style="background-color: {{ $stage['color'] }}20; border-color: {{ $stage['color'] }}40;">
                                <div class="w-1.5 h-1.5 rounded-full" style="background-color: {{ $stage['color'] }};"></div>
                                <span class="text-xs font-black tracking-tighter" style="color: {{ $stage['color'] }};">
                                    {{ $percentFormatted }}%
                                </span>
                            </div>

                        </div>
                    @endforeach
                </div>

            </div>
        </div>

    </x-filament::section>
</x-filament-widgets::widget>