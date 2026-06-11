<x-layouts.app title="Customer Details">
    <section class="page-header">
        <div>
            <p class="eyebrow">Customer Details</p>
            <h1>{{ $customer->name }}</h1>
        </div>
        <x-action-buttons :item="$customer" route-prefix="customers" />
    </section>

    <div class="detail-grid">
        <div><span>Address</span><strong>{{ $customer->address }}</strong></div>
        <div><span>State</span><strong>{{ $customer->state }}</strong></div>
        <div><span>PIN</span><strong>{{ $customer->pin }}</strong></div>
        <div><span>Phone</span><strong>{{ $customer->phone ?? '-' }}</strong></div>
        <div><span>Gmail</span><strong>{{ $customer->gmail ?? '-' }}</strong></div>
        <div><span>GST</span><strong>{{ $customer->gst }}</strong></div>
    </div>
</x-layouts.app>
