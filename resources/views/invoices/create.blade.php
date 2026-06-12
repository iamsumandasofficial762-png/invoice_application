<x-layouts.app title="Create Invoice">
    @push('styles')
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-form.css') }}">
    @endpush

    @php
        $items = old('items', [['description' => '', 'sac_code' => '9983', 'unit_price' => '']]);
    @endphp

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Create Invoice</h1>
        </div>
    </section>

    <form class="invoice-form" method="POST" action="{{ route('invoices.store') }}">
        @csrf
        @include('invoices.partials.form', [
            'invoice' => null,
            'customers' => $customers,
            'invoiceNumber' => $invoiceNumber,
            'signatures' => $signatures,
            'items' => $items,
        ])
        @include('invoices.partials.form-actions', ['mode' => 'create', 'invoice' => null])
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
