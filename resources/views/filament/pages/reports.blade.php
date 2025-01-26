<x-filament-panels::page>
    <div class="p-4">
        {{-- Globalny formularz filtrów --}}
        {{ $this->form }}
    </div>
    <div class="p-4">
        <h1 class="text-2xl font-bold mb-4">Akcje pracowników</h1>
        {{ $this->table }}
    </div>
</x-filament-panels::page>
