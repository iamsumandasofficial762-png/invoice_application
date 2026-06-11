<div class="form-grid">
    <label>
        <span>Invoice Number</span>
        <input type="text" value="{{ $invoiceNumber }}" readonly>
    </label>
    <label>
        <span>Invoice Date</span>
        <input type="date" name="invoice_date" value="{{ old('invoice_date', optional($invoice?->invoice_date)->format('Y-m-d') ?? now()->format('Y-m-d')) }}" required>
        <x-form-error name="invoice_date" />
    </label>
    <label>
        <span>Customer</span>
        <select name="customer_id" id="customer_id" required>
            <option value="">Select customer</option>
            @foreach ($customers as $customer)
                <option
                    value="{{ $customer->id }}"
                    data-name="{{ $customer->name }}"
                    data-address="{{ $customer->address }}"
                    data-state="{{ $customer->state }}"
                    data-pin="{{ $customer->pin }}"
                    data-phone="{{ $customer->phone }}"
                    data-gmail="{{ $customer->gmail }}"
                    data-gst="{{ $customer->gst }}"
                    @selected((string) old('customer_id', $invoice?->customer_id) === (string) $customer->id)
                >{{ $customer->name }} - {{ $customer->gst }}</option>
            @endforeach
        </select>
        <x-form-error name="customer_id" />
    </label>
    <label>
        <span>Signature</span>
        <select name="signature_image" required>
            <option value="">Select signature</option>
            @foreach ($signatures as $file => $label)
                <option value="{{ $file }}" @selected(old('signature_image', $invoice?->signature_image) === $file)>{{ $label }}</option>
            @endforeach
        </select>
        <x-form-error name="signature_image" />
    </label>
</div>

<div class="customer-tools">
    <button class="btn btn-secondary" type="button" data-modal-open="customer-modal">Create New Customer</button>
</div>

<div id="selected-customer-details" class="customer-preview empty-state">Select a customer to show details.</div>

<section class="items-section">
    <div class="section-heading">
        <h2>Invoice Items</h2>
        <button class="btn btn-secondary" type="button" data-add-item>Add Item</button>
    </div>
    <div class="table-wrap">
        <table class="data-table invoice-items-table">
            <thead>
                <tr>
                    <th>SR. NO.</th>
                    <th>DESCRIPTION</th>
                    <th>SAC CODE</th>
                    <th>UNIT PRICE</th>
                    <th>AMOUNT(INR)</th>
                    <th></th>
                </tr>
            </thead>
            <tbody data-items-body>
                @foreach ($items as $index => $item)
                    @include('invoices.partials.item-row', ['index' => $index, 'item' => $item])
                @endforeach
            </tbody>
        </table>
    </div>
    <x-form-error name="items" />
</section>

@include('invoices.partials.calculation-summary')

<template id="item-row-template">
    @include('invoices.partials.item-row', ['index' => '__INDEX__', 'item' => ['description' => '', 'sac_code' => '', 'unit_price' => '']])
</template>
