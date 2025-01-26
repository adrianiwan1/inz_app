<!doctype html>
<html lang="pl">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Faktura VAT</title>
    <style>
        h4 {
            margin: 0;
        }
        body {
            font-family: 'DejaVu Sans', sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
            color: #333;
        }
        .w-full {
            width: 100%;
        }
        .w-half {
            width: 50%;
        }
        .margin-top {
            margin-top: 1.25rem;
        }
        .footer {
            font-size: 0.875rem;
            padding: 1rem;
            background-color: rgb(241 245 249);
            border-top: 1px solid #ddd;
        }
        table {
            width: 100%;
            border-spacing: 0;
        }
        table.products {
            font-size: 0.875rem;
            border: 1px solid #ddd;
        }
        table.products th {
            background-color: #007bff;
            color: #ffffff;
            text-align: left;
            padding: 0.5rem;
        }
        table.products td {
            background-color: #f9f9f9;
            padding: 0.5rem;
        }
        table.products tr.items td {
            background-color: rgb(241 245 249);
        }
        .total {
            text-align: right;
            margin-top: 1rem;
            font-size: 0.875rem;
        }
    </style>
</head>
<body>
<!-- Nagłówek faktury -->
<table class="w-full">
    <tr>
        <td class="w-half">
            <h2>Faktura VAT</h2>
            <p><strong>Numer faktury:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Data wystawienia:</strong> {{ $invoice->issue_date }}</p>
            <p><strong>Termin płatności:</strong> {{ $invoice->sale_date }}</p>
        </td>
    </tr>
</table>

<!-- Dane sprzedawcy i nabywcy -->
<div class="margin-top">
    <table class="w-full">
        <tr>
            <td class="w-half">
                <h4>Sprzedawca:</h4>
                <p>{{ $invoice->seller_name }}</p>
                <p>{{ $invoice->seller_address }}</p>
                <p>NIP: {{ $invoice->seller_nip }}</p>
            </td>
            <td class="w-half" style="text-align: right;">
                <h4>Nabywca:</h4>
                <p>{{ $invoice->buyer_name }}</p>
                <p>{{ $invoice->buyer_address }}</p>
                <p>NIP: {{ $invoice->buyer_nip }}</p>
            </td>
        </tr>
    </table>
</div>

<!-- Szczegóły usługi -->
<div class="margin-top">
    <table class="products">
        <thead>
        <tr>
            <th>#</th>
            <th>Opis</th>
            <th>Cena netto</th>
            <th>VAT</th>
            <th>Kwota brutto</th>
        </tr>
        </thead>
        <tbody>
        <tr class="items">
            <td>1</td>
            <td>{{ $invoice->service_name }}</td>
            <td>{{ number_format($invoice->net_value, 2, ',', ' ') }} zł</td>
            <td>{{ $invoice->tax_rate }}%</td>
            <td>{{ number_format($invoice->gross_value, 2, ',', ' ') }} zł</td>
        </tr>
        </tbody>
    </table>
</div>

<!-- Podsumowanie -->
<div class="total">
    <p><strong>Wartość netto:</strong> {{ number_format($invoice->net_value, 2, ',', ' ') }} zł</p>
    <p><strong>Podatek VAT:</strong> {{ number_format($invoice->net_value * ($invoice->tax_rate / 100), 2, ',', ' ') }} zł</p>
    <p><strong>Wartość brutto:</strong> {{ number_format($invoice->gross_value, 2, ',', ' ') }} zł</p>
</div>

<!-- Dane płatności -->
<div class="footer margin-top">
    <h4>Dane płatności:</h4>
    <p><strong>Numer rachunku:</strong> {{ $invoice->bank_account_number }}</p>
    <p>Wygenerowano automatycznie za pomocą systemu fakturowego.</p>
</div>
</body>
</html>
