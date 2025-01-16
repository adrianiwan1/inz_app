@if (session()->has('success'))
    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
        <strong class="font-bold">Sukces!</strong>
        <span class="block sm:inline">{{ session('success') }}</span>
    </div>
@endif
@if(Auth::user()->employment_type === 'b2b')
<div class="container mx-auto p-4 bg-gray-100">
    <!-- Sekcja tabeli z wystawionymi fakturami -->
    <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <h2 class="text-xl font-bold mb-4">Wystawione Faktury</h2>
        <div class="overflow-x-auto bg-white rounded-lg shadow-md">
            <table class="w-full text-sm text-left text-gray-500 dark:text-gray-400">
                <thead class="text-xs text-gray-700 uppercase bg-gray-100 dark:bg-gray-700 dark:text-gray-400">
                <tr>
                    <th class="px-4 py-2 text-center">Numer faktury</th>
                    <th class="px-4 py-2 text-center">Wartość brutto</th>
                    <th class="px-4 py-2 text-center">Data wystawienia</th>
                    <th class="px-4 py-2 text-center">Termin płatności</th>
                    <th class="px-4 py-2 text-center">Akcje</th>
                </tr>
                </thead>
                <tbody>
                @forelse($invoices as $invoice)
                    <tr class="border-b dark:border-gray-700">
                        <td class="px-4 py-2 text-center">{{ $invoice->invoice_number }}</td>
                        <td class="px-4 py-2 text-center">{{ number_format($invoice->gross_value, 2, ',', ' ') }} zł</td>
                        <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($invoice->issue_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-center">{{ \Carbon\Carbon::parse($invoice->sale_date)->format('Y-m-d') }}</td>
                        <td class="px-4 py-2 text-center">
                            <button
                                wire:click="downloadInvoice({{ $invoice->id }})"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                                Pobierz
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-gray-500 py-4">
                            Brak wystawionych faktur.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Formularz generowania faktury -->
    <div class="bg-white rounded-lg shadow-md p-4">
        <h1 class="text-xl font-bold mb-4">Generowanie faktury B2B</h1>
        <form wire:submit.prevent="generateInvoice">
            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- Dane Sprzedawcy -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">Dane Sprzedawcy</h2>
                    <label class="block">
                        Nazwa:
                        <input type="text" wire:model="sellerName" class="mt-1 block w-full border rounded-md" readonly />
                    </label>
                    <label class="block">
                        Adres:
                        <input type="text" wire:model="sellerAddress" class="mt-1 block w-full border rounded-md" />
                    </label>
                    <label class="block">
                        NIP:
                        <input type="text" wire:model="sellerNip" class="mt-1 block w-full border rounded-md" />
                    </label>
                </div>

                <!-- Dane Nabywcy -->
                <div>
                    <h2 class="text-lg font-semibold mb-2">Dane Nabywcy</h2>
                    <label class="block">
                        Nazwa:
                        <input type="text" wire:model="buyerName" class="mt-1 block w-full border rounded-md" />
                    </label>
                    <label class="block">
                        Adres:
                        <input type="text" wire:model="buyerAddress" class="mt-1 block w-full border rounded-md" />
                    </label>
                    <label class="block">
                        NIP:
                        <input type="text" wire:model="buyerNip" class="mt-1 block w-full border rounded-md" />
                    </label>
                </div>
            </div>

            <!-- Szczegóły faktury -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <label class="block">
                    Nazwa usługi:
                    <input type="text" wire:model="serviceName" class="mt-1 block w-full border rounded-md" />
                </label>
                <label class="block">
                    Numer faktury:
                    <input type="text" wire:model="invoiceNumber" class="mt-1 block w-full border rounded-md" />
                </label>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-6">
                <label class="block">
                    Wartość netto:
                    <input type="text" wire:model="netValue" class="mt-1 block w-full border rounded-md" readonly />
                </label>
                <label class="block">
                    Stawka VAT (%):
                    <input type="number" wire:model="taxRate" class="mt-1 block w-full border rounded-md" />
                </label>
                <label class="block">
                    Wartość brutto:
                    <input type="text" wire:model="grossValue" class="mt-1 block w-full border rounded-md" readonly />
                </label>
            </div>

            <!-- Inne -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <label class="block">
                    Numer rachunku:
                    <input type="text" wire:model="bankAccountNumber" class="mt-1 block w-full border rounded-md" />
                </label>
                <label class="block">
                    Data wystawienia:
                    <input type="date" wire:model="issueDate" class="mt-1 block w-full border rounded-md" />
                </label>
                <label class="block">
                    Termin płatności:
                    <input type="date" wire:model="saleDate" class="mt-1 block w-full border rounded-md" readonly />
                </label>
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                Generuj fakturę
            </button>
        </form>
    </div>
</div>
@else
    <p>Nie masz dostępu do tej sekcji.</p>
@endif
