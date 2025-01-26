
<div class="w-3/4 p-4">
    <div class="bg-white rounded-lg shadow-md p-4">
        <div class="flex items-center justify-between mb-4">
            <h2 class="text-xl font-bold">Akcje wykonane dzisiaj</h2>
            <span class="text-black font-bold">
        Łączny czas:
        {{
            \Carbon\CarbonInterval::seconds($todayTotalElapsedTime ?? 0)
                ->cascade()
                ->forHumans(['short' => true, 'minimumUnit' => 'seconds'])
        }}
    </span>
        </div>
        <div class="mt-6">
            <p class="text-gray-600">Wybierz akcję z listy po lewej stronie, aby rozpocząć lub zatrzymać czas.</br>
            </p>
            <!-- Tabela Flowbite z paginacją -->
            <div class="overflow-x-auto bg-white rounded-lg shadow-md">
                <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                    <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                    <tr>
                        <th class="px-4 py-2 text-center">Nazwa akcji</th>
                        <th class="px-4 py-2 text-center">Godzina rozpoczęcia</th>
                        <th class="px-4 py-2 text-center">Godzina zakończenia</th>
                        <th class="px-4 py-2 text-center">Czas trwania</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($todayActionHistories->forPage($currentPage, 15) as $history)
                        <tr class="border-b dark:border-gray-700">
                            <td class="px-4 py-2 text-center">{{ $history->action->name }}</td>
                            <td class="px-4 py-2 text-center">
                                {{ \Carbon\Carbon::parse($history->start_time)->format('H:i') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{ \Carbon\Carbon::parse($history->end_time)->format('H:i') }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                {{
                                    \Carbon\CarbonInterval::seconds($history->elapsed_time)
                                        ->cascade()
                                        ->forHumans(['short' => true, 'minimumUnit' => 'seconds'])
                                }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-gray-500 py-4">
                                Brak akcji wykonanych dzisiaj.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="flex justify-between items-center mt-4">
                <span class="text-sm text-gray-700 dark:text-gray-400">
                    Wyświetla {{ $todayActionHistories->forPage($currentPage, 15)->count() }} z {{ $todayActionHistories->count() }} akcji
                </span>
                <div class="inline-flex rounded-md shadow-sm" role="group">
                    <button wire:click="previousPage" @if($currentPage === 1) disabled @endif
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-l-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:text-blue-500 disabled:opacity-50">
                        Poprzednia
                    </button>
                    <button wire:click="nextPage" @if($currentPage * 15 >= $todayActionHistories->count()) disabled @endif
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-r-lg hover:bg-gray-100 hover:text-gray-900 focus:z-10 focus:ring-2 focus:ring-blue-500 focus:text-blue-500 disabled:opacity-50">
                        Następna
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
