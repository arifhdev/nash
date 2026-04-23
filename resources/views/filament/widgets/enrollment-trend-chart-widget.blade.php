<x-filament-widgets::widget>
    <x-filament::section heading="Daily Activity & Enrollment Trend">
        <div class="mb-6">
            {{ $this->form }}
        </div>

        <div class="w-full" style="min-height: 400px;" wire:ignore>
            <div id="chart-trend-{{ $this->getId() }}" 
                 class="w-full h-[400px]"
                 x-data="{
                    chart: null,
                    renderChart(data) {
                        if (this.chart) this.chart.destroy();
                        
                        let options = {
                            series: [
                                { name: 'Active Users (Login)', data: data.active },
                                { name: 'Enrolled Courses', data: data.enrolled },
                                { name: 'Completed Courses', data: data.completed }
                            ],
                            chart: { 
                                type: 'area', 
                                height: 400, 
                                toolbar: { show: true },
                                animations: { enabled: true }
                            },
                            colors: ['#8b5cf6', '#d946ef', '#f43f5e'],
                            stroke: { curve: 'smooth', width: 3 },
                            dataLabels: { 
                                enabled: true,
                                style: { fontSize: '9px' },
                                background: { enabled: true, padding: 2, borderRadius: 2 }
                            },
                            xaxis: { 
                                categories: data.labels,
                                labels: { style: { colors: '#9ca3af', fontSize: '10px' }, rotate: -45 }
                            },
                            yaxis: { 
                                labels: { 
                                    style: { colors: '#9ca3af' },
                                    formatter: (v) => Math.round(v)
                                } 
                            },
                            fill: { type: 'gradient', gradient: { opacityFrom: 0.4, opacityTo: 0.05 } },
                            legend: { position: 'top', horizontalAlign: 'right' }
                        };

                        this.chart = new ApexCharts(this.$el, options);
                        this.chart.render();
                    },
                    init() {
                        // Inisialisasi awal
                        if (typeof ApexCharts === 'undefined') {
                            const script = document.createElement('script');
                            script.src = 'https://cdn.jsdelivr.net/npm/apexcharts';
                            script.onload = () => this.renderChart(@js($chartData));
                            document.head.appendChild(script);
                        } else {
                            this.renderChart(@js($chartData));
                        }

                        // Tangkap data dari Livewire update
                        $wire.on('updateChart', (event) => {
                            const data = Array.isArray(event) ? event[0] : event;
                            this.renderChart(data);
                        });
                    }
                 }">
                <div class="flex items-center justify-center h-full text-gray-400">Memuat Grafik...</div>
            </div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>