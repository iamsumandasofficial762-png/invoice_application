<x-layouts.app title="Edit Invoice">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-form.css') }}">
    @endpush

    @php
        $items = old('items', $invoice->items->map(fn ($item) => [
            'description' => $item->description,
            'sac_code' => $item->sac_code ?: '9983',
            'unit_price' => $item->unit_price,
        ])->toArray());
    @endphp

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Edit Invoice</h1>
        </div>
    </section>

    <form class="invoice-form" method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf
        @method('PUT')
        @include('invoices.partials.form', [
            'invoice' => $invoice,
            'customers' => $customers,
            'invoiceNumber' => $invoice->invoice_number,
            'signatures' => $signatures,
            'items' => $items,
        ])
        @include('invoices.partials.form-actions', ['mode' => 'edit', 'invoice' => $invoice])
    </form>

    @include('invoices.partials.customer-modal')

    @push('scripts')
        <script src="{{ asset('assets/js/invoice.js') }}?v={{ filemtime(public_path('assets/js/invoice.js')) }}"></script>
        <script src="{{ asset('assets/js/invoice-customer-select2.js') }}?v={{ filemtime(public_path('assets/js/invoice-customer-select2.js')) }}"></script>
        <script src="{{ asset('assets/js/invoice-number-check.js') }}?v={{ filemtime(public_path('assets/js/invoice-number-check.js')) }}"></script>
    @endpush
</x-layouts.app>
