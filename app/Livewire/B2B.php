<?php

namespace App\Livewire;

use App\Models\Buyer;
use App\Models\User;
use App\Models\ActionHistory;
use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class B2B extends Component
{
    use WithPagination;

    public $sellerName;
    public $sellerAddress;
    public $sellerNip;
    public $buyerName = 'Nazwa Firmy';
    public $buyerAddress = 'Adres Firmy';
    public $buyerNip = 'NIP Firmy';
    public $serviceName = 'Usługi IT';
    public $invoiceNumber;
    public $netValue = 0.00;
    public $taxRate = 0;
    public $grossValue = 0.00;
    public $bankAccountNumber;
    public $issueDate;
    public $saleDate;

    public function mount()
    {
        // Ustawianie domyślnej daty wystawienia i sprzedaży
        $this->issueDate = Carbon::today()->toDateString();
        $this->saleDate = Carbon::today()->addDays(14)->toDateString();

        $user = auth()->user();

        // Sprawdzenie, czy użytkownik ma employment_type ustawiony na b2b
        if (!$user || $user->employment_type !== 'b2b') {
            abort(403, 'Nie masz dostępu do tej strony.');
        }

        // Automatyczne uzupełnienie pól sprzedawcy
        $this->sellerName = $user->seller_name;
        $this->sellerAddress = $user->seller_address ?? 'Adres użytkownika';
        $this->sellerNip = $user->seller_nip ?? 'NIP użytkownika';
        $this->bankAccountNumber = $user->bank_account_number ?? 'Bank account';
        $this->taxRate = $user->vat_rate ?? 0;

        // Pobierz dane nabywcy z tabeli buyers (tylko pierwszy rekord)
        $buyer = Buyer::first();
        if ($buyer) {
            $this->buyerName = $buyer->name;
            $this->buyerAddress = $buyer->address;
            $this->buyerNip = $buyer->nip;
        }

        // Oblicz wartość netto na podstawie przepracowanych godzin (dla bieżącego miesiąca)
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $totalSeconds = ActionHistory::where('user_id', $user->id)
            ->whereBetween('start_time', [$startOfMonth, $endOfMonth])
            ->sum('elapsed_time');

        $totalHours = $totalSeconds / 3600;
        // Przykładowo zakładamy, że stawka godzinowa jest przechowywana w groszach -> dzielimy przez 100
        $this->netValue = round(($user->hourly_rate ?? 0) * $totalHours / 100, 2);
        $this->calculateGrossValue();

        // ----------------- AUTOMATYCZNE UZUPEŁNIANIE NUMERU FAKTURY DLA DANEGO UŻYTKOWNIKA I BIEŻĄCEGO MIESIĄCA -----------------
        $currentMonth = Carbon::parse($this->issueDate)->format('m');
        $currentYear = Carbon::parse($this->issueDate)->format('Y');

        // Szukamy ostatniej faktury wystawionej przez tego użytkownika w tym samym miesiącu i roku (po dacie issue_date)
        $lastInvoice = Invoice::where('user_id', $user->id)
            ->whereMonth('issue_date', $currentMonth)
            ->whereYear('issue_date', $currentYear)
            ->orderBy('id', 'desc')
            ->first();

        if (!$lastInvoice) {
            // Jeśli nie wystawił jeszcze faktury w tym miesiącu -> numer = 1
            $nextNumber = 1;
        } else {
            // Parsujemy numer faktury w formacie "numer/miesiąc/rok"
            $parts = explode('/', $lastInvoice->invoice_number);

            if (count($parts) === 3) {
                // Pierwsza część to poprzedni numer
                $previousNumber = (int)$parts[0];
                $nextNumber = $previousNumber + 1;
            } else {
                // Fallback jeśli format był niepoprawny
                $nextNumber = 1;
            }
        }

        $this->invoiceNumber = "{$nextNumber}/{$currentMonth}/{$currentYear}";
    }

    public function updatedTaxRate()
    {
        $this->calculateGrossValue();
    }

    public function calculateGrossValue()
    {
        $this->grossValue = round($this->netValue + ($this->netValue * ($this->taxRate / 100)), 2);
    }

    public function generateInvoice()
    {
        $this->validate([
            'sellerName' => 'required|string|max:255',
            'sellerAddress' => 'required|string|max:255',
            'sellerNip' => 'required|string|max:255',
            'buyerName' => 'required|string|max:255',
            'buyerAddress' => 'required|string|max:255',
            'buyerNip' => 'required|string|max:255',
            'serviceName' => 'required|string|max:255',
            'invoiceNumber' => 'required|string|max:255',
            'netValue' => 'required|numeric|min:0',
            'taxRate' => 'required|numeric|min:0|max:100',
            'grossValue' => 'required|numeric|min:0',
            'bankAccountNumber' => 'required|string|max:255',
            'issueDate' => 'required|date',
            'saleDate' => 'required|date|after_or_equal:issueDate',
        ]);

        // Zapisz fakturę do bazy danych
        Invoice::create([
            'user_id' => auth()->id(),
            'seller_name' => $this->sellerName,
            'seller_address' => $this->sellerAddress,
            'seller_nip' => $this->sellerNip,
            'buyer_name' => $this->buyerName,
            'buyer_address' => $this->buyerAddress,
            'buyer_nip' => $this->buyerNip,
            'service_name' => $this->serviceName,
            'invoice_number' => $this->invoiceNumber,
            'net_value' => round($this->netValue, 2),
            'tax_rate' => $this->taxRate,
            'gross_value' => round($this->grossValue, 2),
            'bank_account_number' => $this->bankAccountNumber,
            'issue_date' => $this->issueDate,
            'sale_date' => $this->saleDate,
        ]);

        session()->flash('success', 'Faktura została wygenerowana i zapisana.');
        return redirect()->route('b2b');
    }

    public function downloadInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        // Generowanie PDF
        $safeInvoiceNumber = str_replace(['/', '\\'], '-', $invoice->invoice_number);

        $pdf = \Pdf::loadView('pdf.invoice', compact('invoice'));
        return response()->streamDownload(
            fn() => print($pdf->output()),
            "Faktura_{$safeInvoiceNumber}.pdf"
        );
    }

    public function render()
    {
        $invoices = Invoice::where('user_id', auth()->id())
            ->orderBy('issue_date', 'desc')
            ->paginate(10);

        return view('livewire.b2-b', [
            'invoices' => $invoices,
        ])->layout('layouts.app');
    }
}
