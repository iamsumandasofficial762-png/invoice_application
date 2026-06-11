<div class="invoice-form-card">
    <div class="invoice-form-title">TAX INVOICE</div>

    <section class="invoice-party-form-grid">
        @include('invoices.partials.buyer-details', [
            'invoice' => $invoice,
            'customers' => $customers,
        ])
        @include('invoices.partials.biller-details')
    </section>

    <section class="invoice-meta-row">
        <label>
            <span>Invoice Number</span>
            <input type="text" value="{{ $invoiceNumber }}" readonly>
        </label>
        <label>
            <span>Invoice Date</span>
            <input type="date" name="invoice_date" value="{{ old('invoice_date', optional($invoice?->invoice_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
            <x-form-error name="invoice_date" />
        </label>
    </section>

    @include('invoices.partials.items-table', ['items' => $items])

    @include('invoices.partials.calculation-summary')

    @include('invoices.partials.payment-signature', [
        'invoice' => $invoice,
        'signatures' => $signatures,
    ])
</div>

<template id="item-row-template">
    @include('invoices.partials.item-row', ['index' => '__INDEX__', 'item' => ['description' => '', 'sac_code' => '9983', 'unit_price' => '']])
</template>
