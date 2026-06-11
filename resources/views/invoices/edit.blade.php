<x-layouts.app title="Edit Invoice">
    @php
        $items = old('items', $invoice->items->map(fn ($item) => [
            'description' => $item->description,
            'sac_code' => $item->sac_code,
            'unit_price' => $item->unit_price,
        ])->toArray());
    @endphp

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Edit Invoice</h1>
        </div>
    </section>

    <form class="form-card invoice-form" method="POST" action="{{ route('invoices.update', $invoice) }}">
        @csrf
        @method('PUT')
        @include('invoices.partials.form', [
            'invoice' => $invoice,
            'customers' => $customers,
            'invoiceNumber' => $invoice->invoice_number,
            'signatures' => $signatures,
            'items' => $items,
        ])
        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('invoices.show', $invoice) }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Update Invoice</button>
        </div>
    </form>

    @include('invoices.partials.customer-modal')

    @push('scripts')
        <script src="{{ asset('assets/js/invoice.js') }}"></script>
    @endpush
</x-layouts.app>
