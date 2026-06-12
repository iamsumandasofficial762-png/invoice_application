@include('invoices.partials.invoice-template', [
    'invoice' => $invoice,
    'pdfMode' => $pdfMode ?? false,
    'printMode' => $printMode ?? false,
])
