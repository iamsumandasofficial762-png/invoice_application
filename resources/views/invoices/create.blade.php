<x-layouts.app title="Create Invoice">
    @php
        $items = old('items', [['description' => '', 'sac_code' => '', 'unit_price' => '']]);
    @endphp

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Create Invoice</h1>
        </div>
    </section>

    <form class="form-card invoice-form" method="POST" action="{{ route('invoices.store') }}">
        @csrf
        @include('invoices.partials.form', [
            'invoice' => null,
            'customers' => $customers,
            'invoiceNumber' => $invoiceNumber,
            'signatures' => $signatures,
            'items' => $items,
        ])
        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('invoices.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Save Invoice</button>
        </div>
    </form>

    @include('invoices.partials.customer-modal')

    @push('scripts')
        <script src="{{ asset('assets/js/invoice.js') }}"></script>
    @endpush
</x-layouts.app>
