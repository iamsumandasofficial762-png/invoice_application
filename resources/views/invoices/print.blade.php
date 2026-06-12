<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Print {{ $invoice->invoice_number }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/invoice.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/invoice-print.css') }}">
</head>
<body class="invoice-print-screen">
    <main class="print-page">
        <div class="print-actions">
            <a class="btn btn-light" href="{{ route('invoices.show', $invoice) }}">Back</a>
            <button class="btn btn-primary" type="button" data-print-button>Print Invoice</button>
        </div>
        @include('invoices.partials.invoice-template', ['invoice' => $invoice, 'printMode' => true])
    </main>
    <script src="{{ asset('assets/js/invoice-print.js') }}"></script>
</body>
</html>
