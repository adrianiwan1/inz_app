<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fakturaaaa</title>
    <style>
        body { font-family: Arial, sans-serif; }
        .table { width: 100%; border-collapse: collapse; }
        .table, .th, .td { border: 1px solid black; padding: 8px; text-align: left; }
    </style>
</head>
<body>
<h1>Faktura</h1>
<p>Data wystawienia: {{ now()->format('Y-m-d') }}</p>
<p>Termin płatności: {{ \Carbon\Carbon::now()->addDays(14)->format('Y-m-d') }}</p>

<table class="table">
    <thead>
    <tr>
        <th class="th">Numer faktury</th>
        <th class="th">Nazwa usługi</th>
        <th class="th">Kwota brutto</th>
    </tr>
    </thead>
    <tbody>
    @foreach($invoices as $invoice)
        <tr>
            <td class="td">{{ $invoice->invoice_number }}</td>
            <td class="td">{{ $invoice->service_name }}</td>
            <td class="td">{{ number_format($invoice->gross_value, 2) }} zł</td>
        </tr>
    @endforeach
    </tbody>
</table>
</body>
</html>
