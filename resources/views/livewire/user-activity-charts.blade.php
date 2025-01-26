<div class="container mx-auto p-4 bg-gray-100 max-h-full">
    <!-- Sekcja przycisków zakresu czasu -->
    <div class="flex justify-between items-center mb-6">
        <div class="flex gap-2">
            <button wire:click="setDateRange('day')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Dzisiaj</button>
            <button wire:click="setDateRange('week')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Tydzień</button>
            <button wire:click="setDateRange('month')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Miesiąc</button>
            <button wire:click="setDateRange('year')" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">Rok</button>
        </div>
        <div>
            <label for="dateRange" class="text-sm font-medium text-gray-700 mr-2">Własny zakres:</label>
            <input
                x-data
                x-ref="dateRange"
                x-init="flatpickr($refs.dateRange, {
                    mode: 'range',
                    dateFormat: 'Y-m-d',
                    onClose: function(selectedDates, dateStr, instance) {
                        @this.set('customDateRange', dateStr);
                        @this.call('applyCustomDateRange');
                    }
                })"
                type="text"
                id="dateRange"
                placeholder="Wybierz zakres dat"
                class="rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-500 focus:ring-opacity-50 px-4 py-2"
            >
        </div>
    </div>

    <!-- Sekcja z wykresami -->
    <div class="flex flex-wrap justify-between gap-4 mb-6">
        <!-- Wykres kołowy -->
        <div class="w-full md:w-2/5 bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg text-black font-bold">Wykres aktywności</h2>
                <span class="text-black font-bold">
                    Zakres dat: {{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}
                </span>
                <span class="text-black font-bold">
                    Łączny czas: {{
                        sprintf('%02dh %02dm %02ds',
                            intdiv($totalElapsedTime, 3600), // godziny
                            intdiv($totalElapsedTime % 3600, 60), // minuty
                            $totalElapsedTime % 60 // sekundy
                        )
                    }}
                </span>
            </div>
            <canvas id="pieChart" style="max-width: 100%; height: auto;"></canvas>
        </div>

        <!-- Wykres słupkowy -->
        <div class="w-full md:w-7/12 bg-white rounded-lg shadow-md p-4">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg text-black font-bold">Czas trwania akcji</h2>
                <span class="text-black font-bold">
                    Zakres dat: {{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}
                </span>
                <span class="text-black font-bold">
                    Łączny czas: {{
                        sprintf('%02dh %02dm %02ds',
                            intdiv($totalElapsedTime, 3600), // godziny
                            intdiv($totalElapsedTime % 3600, 60), // minuty
                            $totalElapsedTime % 60 // sekundy
                        )
                    }}
                </span>
            </div>
            <canvas id="barChart" style="max-width: 100%; height: auto;"></canvas>
        </div>
    </div>

    <!-- Tabela szczegółowa -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl text-black font-bold">Szczegóły aktywności</h2>
            <span class="text-black font-bold">
            Zakres dat:
            {{ $startDate->format('Y-m-d') }} - {{ $endDate->format('Y-m-d') }}
        </span>
            <span class="text-black font-bold">
                    Łączny czas: {{
                        sprintf('%02dh %02dm %02ds',
                            intdiv($totalElapsedTime, 3600), // godziny
                            intdiv($totalElapsedTime % 3600, 60), // minuty
                            $totalElapsedTime % 60 // sekundy
                        )
                    }}
                </span>
        </div>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2 text-center">Nazwa akcji</th>
                    <th class="px-4 py-2 text-center">Czas rozpoczęcia</th>
                    <th class="px-4 py-2 text-center">Czas zakończenia</th>
                    <th class="px-4 py-2 text-center">Czas trwania</th>
                </tr>
                </thead>
                <tbody>
                @forelse($activityDetails->forPage($currentPage, 15) as $activity)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2 text-center">{{ $activity['action'] }}</td>
                        <td class="px-4 py-2 text-center">
                            {{ \Carbon\Carbon::parse($activity['start_time'])->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{ \Carbon\Carbon::parse($activity['end_time'])->format('Y-m-d H:i') }}
                        </td>
                        <td class="px-4 py-2 text-center">
                            {{
                                \Carbon\CarbonInterval::seconds($activity['elapsed_time'])
                                    ->cascade()
                                    ->forHumans(['short' => true, 'minimumUnit' => 'seconds'])
                            }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center text-gray-500 py-4">
                            Brak danych do wyświetlenia.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="flex justify-between items-center mt-4">
        <span class="text-sm text-gray-700 dark:text-gray-400">
            Wyświetla {{ $activityDetails->forPage($currentPage, 15)->count() }} z {{ $activityDetails->count() }} aktywności
        </span>
            <div class="inline-flex rounded-md shadow-sm" role="group">
                <button wire:click="previousPage" @if($currentPage === 1) disabled @endif
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:text-blue-500 disabled:opacity-50">
                    Poprzednia
                </button>
                <button wire:click="nextPage" @if($currentPage * 15 >= $activityDetails->count()) disabled @endif
                class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:text-blue-500 disabled:opacity-50">
                    Następna
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            let pieChart = null;
            let barChart = null;

            // Funkcja generowania unikalnych kolorów
            function generateUniqueColors(count) {
                const colors = [];
                for (let i = 0; i < count; i++) {
                    const hue = (i * 137.5) % 360; // Rozkład kolorów za pomocą złotej liczby
                    colors.push(hslToHex(hue, 70, 50));
                }
                return colors;
            }

            // Funkcja konwersji HSL do HEX
            function hslToHex(hue, saturation, lightness) {
                const c = (1 - Math.abs(2 * lightness / 100 - 1)) * (saturation / 100);
                const x = c * (1 - Math.abs((hue / 60) % 2 - 1));
                const m = lightness / 100 - c / 2;

                let r = 0, g = 0, b = 0;
                if (hue < 60) [r, g, b] = [c, x, 0];
                else if (hue < 120) [r, g, b] = [x, c, 0];
                else if (hue < 180) [r, g, b] = [0, c, x];
                else if (hue < 240) [r, g, b] = [0, x, c];
                else if (hue < 300) [r, g, b] = [x, 0, c];
                else [r, g, b] = [c, 0, x];

                const toHex = v => Math.round((v + m) * 255).toString(16).padStart(2, '0');
                return `#${toHex(r)}${toHex(g)}${toHex(b)}`;
            }

            // Funkcja do czyszczenia danych wykresu
            function clearChart(chart) {
                chart.data.labels = [];
                chart.data.datasets.forEach((dataset) => {
                    dataset.data = [];
                });
                chart.update();
            }

            // Funkcja do aktualizacji danych wykresu
            function updateChart(chart, labels, data) {
                const uniqueColors = generateUniqueColors(labels.length);
                chart.data.labels = labels;
                chart.data.datasets.forEach((dataset) => {
                    dataset.data = data;
                    dataset.backgroundColor = uniqueColors;
                });
                chart.update();
            }

            // Funkcja inicjalizacji wykresów
            const initCharts = (pieData, barData) => {
                const pieCtx = document.getElementById('pieChart');
                const barCtx = document.getElementById('barChart');

                // Generowanie kolorów
                const pieColors = generateUniqueColors(Object.keys(pieData).length);
                const barColors = generateUniqueColors(barData.length);

                // Wykres kołowy
                pieChart = new Chart(pieCtx, {
                    type: 'pie',
                    data: {
                        labels: Object.keys(pieData),
                        datasets: [{
                            data: Object.values(pieData),
                            backgroundColor: pieColors,
                        }]
                    }
                });

                // Wykres słupkowy
                barChart = new Chart(barCtx, {
                    type: 'bar',
                    data: {
                        labels: barData.map(item => item.action),
                        datasets: [{
                            label: 'Czas trwania (godziny:minuty)',
                            data: barData.map(item => item.total_time),
                            backgroundColor: barColors,
                        }]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true,
                                ticks: {
                                    callback: function (value) {
                                        const hours = Math.floor(value / 3600);
                                        const minutes = Math.floor((value % 3600) / 60);
                                        return `${hours}h ${minutes}m`;
                                    }
                                }
                            }
                        },
                        plugins: {
                            legend: {
                                display: false
                            },
                            tooltip: {
                                callbacks: {
                                    label: function (context) {
                                        const totalSeconds = context.raw;
                                        const hours = Math.floor(totalSeconds / 3600);
                                        const minutes = Math.floor((totalSeconds % 3600) / 60);
                                        const seconds = totalSeconds % 60;
                                        return `${hours}h ${minutes}m ${seconds}s`;
                                    }
                                }
                            }
                        }
                    }
                });
            };

            // Inicjalizacja wykresów z początkowymi danymi
            const initialPieData = @json($pieChartData ?? []);
            const initialBarData = @json($barChartData ?? []);
            initCharts(initialPieData, initialBarData);

            // Nasłuchiwanie na zdarzenie `chartsUpdated`
            window.addEventListener('chartsUpdated', function (event) {
                const detail = Array.isArray(event.detail) ? event.detail[0] : event.detail;
                const pieChartData = detail.pieChartData || {};
                const barChartData = detail.barChartData || [];

                // Czyszczenie i aktualizacja wykresów
                clearChart(pieChart);
                clearChart(barChart);

                updateChart(pieChart, Object.keys(pieChartData), Object.values(pieChartData));
                updateChart(barChart, barChartData.map(item => item.action), barChartData.map(item => item.total_time));
            });
        });

    </script>

@endpush

