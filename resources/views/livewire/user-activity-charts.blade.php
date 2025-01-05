<div class="flex flex-wrap p-6">
    <!-- Sekcja z wykresami -->
    <div class="w-full md:w-2/3 p-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h1 class="text-2xl font-bold mb-4">Historia aktywności</h1>

            <div class="mb-4">
                <label for="selectedRange" class="block text-sm font-medium text-gray-700">Zakres czasu:</label>
                <select id="selectedRange" wire:model="selectedRange" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-500 focus:ring-opacity-50">
                    <option value="day">Dzień</option>
                    <option value="month">Miesiąc</option>
                    <option value="quarter">Kwartał</option>
                    <option value="year">Rok</option>
                    <option value="custom">Niestandardowy</option>
                </select>
            </div>

            <!-- Pola dla niestandardowego zakresu -->
            @if($selectedRange === 'custom')
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label for="startDate" class="block text-sm font-medium text-gray-700">Data początkowa:</label>
                        <input
                            x-data
                            x-ref="startDate"
                            x-init="flatpickr($refs.startDate, { enableTime: false, dateFormat: 'Y-m-d' })"
                            type="text"
                            id="startDate"
                            wire:model.lazy="startDate"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                        >
                    </div>
                    <div>
                        <label for="endDate" class="block text-sm font-medium text-gray-700">Data końcowa:</label>
                        <input
                            x-data
                            x-ref="endDate"
                            x-init="flatpickr($refs.endDate, { enableTime: false, dateFormat: 'Y-m-d' })"
                            type="text"
                            id="endDate"
                            wire:model.lazy="endDate"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:ring focus:ring-indigo-500 focus:ring-opacity-50"
                        >
                    </div>
                </div>
            @endif


            <!-- Wykres kołowy -->
            <div class="mb-6">
                <h2 class="text-lg font-bold mb-4">Wykres aktywności</h2>
                <canvas id="pieChart"></canvas>
            </div>

            <!-- Wykres słupkowy -->
            <div class="mb-6">
                <h2 class="text-lg font-bold mb-4">Czas trwania akcji</h2>
                <canvas id="barChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Tabela szczegółowa -->
    <div class="w-full md:w-1/3 p-6">
        <div class="bg-white rounded-lg shadow-md p-4">
            <h2 class="text-xl font-bold mb-4">Szczegóły aktywności</h2>
            <table class="table-auto w-full bg-white rounded-lg shadow-md">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-center">Nazwa akcji</th>
                    <th class="px-4 py-2 text-center">Czas</th>
                </tr>
                </thead>
                <tbody>
                @forelse($barChartData as $data)
                    <tr class="border-b">
                        <td class="px-4 py-2 text-center">{{ $data['action'] }}</td>
                        <td class="px-4 py-2 text-center">
                            {{
                                \Carbon\CarbonInterval::seconds($data['total_time'])
                                    ->cascade()
                                    ->forHumans(['short' => true, 'minimumUnit' => 'seconds'])
                            }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-center text-gray-500 py-4">
                            Brak danych do wyświetlenia.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.3.0/chart.umd.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Wykres kołowy
            const pieChart = new Chart(document.getElementById('pieChart'), {
                type: 'pie',
                data: {
                    labels: @json(array_keys($pieChartData)), // Nazwy akcji
                    datasets: [{
                        data: @json(array_values($pieChartData)), // Ilość akcji
                        backgroundColor: ['#ff6384', '#36a2eb', '#cc65fe', '#ffce56', '#33cc33'],
                    }]
                }
            });

            // Wykres słupkowy
            const barChart = new Chart(document.getElementById('barChart'), {
                type: 'bar',
                data: {
                    labels: @json(collect($barChartData)->pluck('action')), // Nazwy akcji
                    datasets: [{
                        label: 'Czas trwania (godziny:minuty)',
                        data: @json(collect($barChartData)->pluck('total_time')), // Czas trwania w sekundach
                        backgroundColor: ['#36a2eb', '#ff6384', '#cc65fe', '#ffce56', '#33cc33'],
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function(value) {
                                    const hours = Math.floor(value / 3600);
                                    const minutes = Math.floor((value % 3600) / 60);
                                    return `${hours}h ${minutes}m`; // Skala osi Y
                                }
                            }
                        }
                    },
                    plugins: {
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    const totalSeconds = context.raw; // Wartość w sekundach
                                    const hours = Math.floor(totalSeconds / 3600);
                                    const minutes = Math.floor((totalSeconds % 3600) / 60);
                                    const seconds = totalSeconds % 60;
                                    return `${hours}h ${minutes}m ${seconds}s`; // Tooltip
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>
@endpush

