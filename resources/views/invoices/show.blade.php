<x-layouts.app title="Invoice {{ $invoice->invoice_number }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice.css') }}">
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoice</p>
            <h1>{{ $invoice->invoice_number }}</h1>
        </div>
        <x-action-buttons :item="$invoice" route-prefix="invoices" show-pdf="true" show-print="true" />
    </section>

    @include('invoices.partials.invoice-document', ['invoice' => $invoice])
</x-layouts.app>
