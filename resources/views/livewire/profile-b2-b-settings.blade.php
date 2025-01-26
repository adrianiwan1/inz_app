<x-form-section submit="save">
    <x-slot name="title">
        {{ __('Ustawienia B2B') }}
    </x-slot>

    <x-slot name="description">
        {{ __('Zaktualizuj dane firmy B2B, które będą używane do wystawiania faktur.') }}
    </x-slot>

    <x-slot name="form">
        <div class="col-span-6 sm:col-span-4">
            <x-label for="seller_name" value="{{ __('Nazwa firmy') }}" />
            <x-input id="seller_name" type="text" class="mt-1 block w-full" wire:model="seller_name" />
            <x-input-error for="seller_name" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="seller_address" value="{{ __('Adres firmy') }}" />
            <x-input id="seller_address" type="text" class="mt-1 block w-full" wire:model="seller_address" />
            <x-input-error for="seller_address" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="seller_nip" value="{{ __('NIP firmy') }}" />
            <x-input id="seller_nip" type="text" class="mt-1 block w-full" wire:model="seller_nip" />
            <x-input-error for="seller_nip" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="bank_account_number" value="{{ __('Numer rachunku bankowego') }}" />
            <x-input id="bank_account_number" type="text" class="mt-1 block w-full" wire:model="bank_account_number" />
            <x-input-error for="bank_account_number" class="mt-2" />
        </div>

        <div class="col-span-6 sm:col-span-4">
            <x-label for="vat_rate" value="{{ __('Stawka VAT (%)') }}" />
            <select id="vat_rate" wire:model="vat_rate" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                <option value="23">23%</option>
                <option value="8">8%</option>
                <option value="5">5%</option>
                <option value="0">0%</option>
            </select>
            <x-input-error for="vat_rate" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-action-message class="me-3" on="saved">
            {{ __('Zapisano.') }}
        </x-action-message>

        <x-button>
            {{ __('Zapisz') }}
        </x-button>
    </x-slot>
</x-form-section>
