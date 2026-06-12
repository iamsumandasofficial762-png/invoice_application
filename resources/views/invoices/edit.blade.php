<x-layouts.app title="Edit Invoice">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
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
        <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
        <script src="{{ asset('assets/js/invoice.js') }}"></script>
        <script src="{{ asset('assets/js/invoice-customer-select2.js') }}"></script>
        <script src="{{ asset('assets/js/invoice-number-check.js') }}"></script>
    @endpush
</x-layouts.app>
