<div class="w-3/4 p-6">
    <div class="bg-white rounded-lg shadow-md p-4">
        <h1 class="text-2xl font-bold mb-4">Dashboard</h1>
        <p class="text-gray-600">Wybierz akcję z listy po lewej stronie, aby rozpocząć lub zatrzymać czas.</p>

        <div class="mt-6">
            <h2 class="text-xl font-bold mb-4">Akcje wykonane dzisiaj</h2>

            <table class="table-auto w-full bg-white rounded-lg shadow-md">
                <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-2 text-center">Nazwa akcji</th>
                    <th class="px-4 py-2 text-center">Godzina rozpoczęcia</th>
                    <th class="px-4 py-2 text-center">Godzina zakończenia</th>
                    <th class="px-4 py-2 text-center">Czas trwania</th>
                </tr>
                </thead>
                <tbody>
                @forelse($todayActionHistories as $history)
                    <tr class="border-b">
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
    </div>
</div>
