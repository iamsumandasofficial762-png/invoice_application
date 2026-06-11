<div class="invoice-party-box">
    <div class="party-heading-row">
        <h3>BUYER DETAILS</h3>
        <button class="btn btn-secondary btn-sm" type="button" data-modal-open="customer-modal">Create New Customer</button>
    </div>
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
    <div id="selected-customer-details" class="customer-preview invoice-customer-preview">Select a customer to show details.</div>
</div>
