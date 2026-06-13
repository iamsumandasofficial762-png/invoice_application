<x-layouts.app title="Create Invoice">
    @push('styles')
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
        <script src="{{ asset('assets/js/invoice.js') }}?v={{ filemtime(public_path('assets/js/invoice.js')) }}"></script>
        <script src="{{ asset('assets/js/invoice-customer-select2.js') }}?v={{ filemtime(public_path('assets/js/invoice-customer-select2.js')) }}"></script>
        <script src="{{ asset('assets/js/invoice-number-check.js') }}?v={{ filemtime(public_path('assets/js/invoice-number-check.js')) }}"></script>
    @endpush
</x-layouts.app>
