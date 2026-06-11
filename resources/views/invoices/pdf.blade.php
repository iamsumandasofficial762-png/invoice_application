<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <link rel="stylesheet" href="{{ public_path('assets/css/invoice-pdf.css') }}">
</head>
<body>
    @include('invoices.partials.invoice-document', ['invoice' => $invoice, 'pdfMode' => true])
</body>
</html>
