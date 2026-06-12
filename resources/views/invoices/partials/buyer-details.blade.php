@php
    $selectedCustomerId = old('customer_id', $invoice?->customer_id);
    $selectedCustomer = $customers->firstWhere('id', (int) $selectedCustomerId);
    $selectedCustomerLabel = $selectedCustomer
        ? $selectedCustomer->name.' - '.$selectedCustomer->gst
        : '';
@endphp

<div class="invoice-party-box">
    <div class="party-heading-row">
        <h3>BUYER DETAILS</h3>
        <button class="btn btn-secondary btn-sm" type="button" data-modal-open="customer-modal">Create New Customer</button>
    </div>
    <label class="customer-selector">
        <span>Customer</span>
        <select
            name="customer_id"
            id="customerSelect2"
            class="customer-select2"
            data-select2-url="{{ route('customers.select2Search') }}"
            required
        >
            <option value="">Select customer</option>
            @if ($selectedCustomer)
                <option
                    value="{{ $selectedCustomer->id }}"
                    selected
                    data-name="{{ $selectedCustomer->name }}"
                    data-address="{{ $selectedCustomer->address }}"
                    data-state="{{ $selectedCustomer->state }}"
                    data-pin="{{ $selectedCustomer->pin }}"
                    data-phone="{{ $selectedCustomer->phone }}"
                    data-gmail="{{ $selectedCustomer->gmail }}"
                    data-gst="{{ $selectedCustomer->gst }}"
                >{{ $selectedCustomerLabel }}</option>
            @endif
        </select>
        <x-form-error name="customer_id" />
    </label>
    <div
        id="buyerDetailsPreview"
        class="customer-preview invoice-customer-preview"
        data-buyer-details-preview
        data-name="{{ $selectedCustomer?->name }}"
        data-address="{{ $selectedCustomer?->address }}"
        data-state="{{ $selectedCustomer?->state }}"
        data-pin="{{ $selectedCustomer?->pin }}"
        data-phone="{{ $selectedCustomer?->phone }}"
        data-gmail="{{ $selectedCustomer?->gmail }}"
        data-gst="{{ $selectedCustomer?->gst }}"
    >
        @if ($selectedCustomer)
            <strong>{{ $selectedCustomer->name }}</strong>
            <span>{{ $selectedCustomer->address }}</span>
            <span>{{ $selectedCustomer->state }} - {{ $selectedCustomer->pin }}</span>
            <span>Phone: {{ $selectedCustomer->phone ?? '-' }}</span>
            <span>Gmail: {{ $selectedCustomer->gmail ?? '-' }}</span>
            <span>GSTIN: {{ $selectedCustomer->gst }}</span>
        @else
            Select a customer to show details.
        @endif
    </div>
</div>
