<x-filament-widgets::widget>
    <x-filament::section>
        <form wire:submit.prevent="save">
            <div class="space-y-4">
                <!-- Nazwa firmy nabywcy -->
                <div>
                    <label for="buyer_name" class="block text-sm font-medium text-gray-700">
                        Nazwa firmy nabywcy
                    </label>
                    <input
                        id="buyer_name"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        wire:model.defer="buyer_name"
                    />
                    @error('buyer_name')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Adres firmy nabywcy -->
                <div>
                    <label for="buyer_address" class="block text-sm font-medium text-gray-700">
                        Adres firmy nabywcy
                    </label>
                    <input
                        id="buyer_address"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        wire:model.defer="buyer_address"
                    />
                    @error('buyer_address')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP firmy nabywcy -->
                <div>
                    <label for="buyer_nip" class="block text-sm font-medium text-gray-700">
                        NIP firmy nabywcy
                    </label>
                    <input
                        id="buyer_nip"
                        type="text"
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                        wire:model.defer="buyer_nip"
                    />
                    @error('buyer_nip')
                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Komunikat o sukcesie -->
                @if (session()->has('success'))
                    <div class="mt-4 text-sm text-green-600">
                        {{ session('success') }}
                    </div>
                @endif

                <!-- Przycisk zapisu -->
                <div class="flex justify-end">
                    <button
                        type="submit"
                        class="px-4 py-2 bg-blue-600 text-black text-sm font-medium rounded-md shadow-sm hover:bg-blue-700 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                    >
                        Zapisz
                    </button>
                </div>
            </div>
        </form>
    </x-filament::section>
</x-filament-widgets::widget>
