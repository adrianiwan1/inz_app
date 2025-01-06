<x-filament-panels::page>
    <div class="flex items-center gap-4 mb-4">
        <!-- Wybór miesiąca -->
        <div>
            <label for="selectedMonth" class="block text-sm font-medium text-gray-700">Miesiąc</label>
            <select id="selectedMonth" wire:model.live="selectedMonth" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach (range(1, 12) as $month)
                    <option value="{{ sprintf('%02d', $month) }}">{{ \Carbon\Carbon::create()->month($month)->translatedFormat('F') }}</option>
                @endforeach
            </select>
        </div>
        <!-- Wybór roku -->
        <div>
            <label for="selectedYear" class="block text-sm font-medium text-gray-700">Rok</label>
            <select id="selectedYear" wire:model.live="selectedYear" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                @foreach (range(now()->year - 5, now()->year + 5) as $year)
                    <option value="{{ $year }}">{{ $year }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Tabela -->
    {{ $this->table }}
</x-filament-panels::page>
