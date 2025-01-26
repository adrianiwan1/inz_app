<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use Carbon\Carbon;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index()
    {
        return view('b2b.invoice');
    }

    public function store(Request $request)
    {
        $request->validate([
            'seller_name' => 'required|string|max:255',
            'seller_address' => 'required|string|max:255',
            'seller_nip' => 'required|string|max:20',
            'buyer_name' => 'required|string|max:255',
            'buyer_address' => 'required|string|max:255',
            'buyer_nip' => 'required|string|max:20',
            'service_name' => 'required|string|max:255',
            'invoice_number' => 'required|string|max:50',
            'bank_account_number' => 'required|string|max:50',
            'tax_rate' => 'required|numeric|min:0|max:100',
        ]);

        $user = auth()->user();

        $totalSeconds = $user->actionHistories()
            ->whereBetween('start_time', [Carbon::now()->startOfMonth(), Carbon::now()->endOfMonth()])
            ->sum('elapsed_time');
        $totalHours = $totalSeconds / 3600;

        $netValue = $totalHours * ($user->hourly_rate / 100);
        $taxValue = $netValue * ($request->tax_rate / 100);
        $grossValue = $netValue + $taxValue;

        $invoice = Invoice::create([
            'user_id' => $user->id,
            'seller_name' => $request->seller_name,
            'seller_address' => $request->seller_address,
            'seller_nip' => $request->seller_nip,
            'buyer_name' => $request->buyer_name,
            'buyer_address' => $request->buyer_address,
            'buyer_nip' => $request->buyer_nip,
            'service_name' => $request->service_name,
            'invoice_number' => $request->invoice_number,
            'net_value' => $netValue,
            'tax_rate' => $request->tax_rate,
            'gross_value' => $grossValue,
            'bank_account_number' => $request->bank_account_number,
            'issue_date' => now(),
            'sale_date' => now()->addDays(14),
        ]);

        // Pobierz PDF
        $pdf = \PDF::loadView('b2b.invoice-pdf', compact('invoice'));

        return $pdf->download("invoice_{$invoice->invoice_number}.pdf");
    }
}
