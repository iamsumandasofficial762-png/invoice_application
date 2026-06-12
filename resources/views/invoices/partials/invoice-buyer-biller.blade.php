<section class="invoice-party-grid">
    <div>
        <h3>Buyer Details</h3>
        <p><strong>{{ $invoice->customer->name }}</strong></p>
        <p>{{ $invoice->customer->address }}</p>
        <p>{{ $invoice->customer->state }} - {{ $invoice->customer->pin }}</p>
        <p>Phone: {{ $invoice->customer->phone ?? '-' }}</p>
        @if (!empty($invoice->customer->gmail))
            <p>Email: {{ $invoice->customer->gmail }}</p>
        @endif
        <p>GSTIN: {{ $invoice->customer->gst }}</p>
    </div>
    <div>
        <h3>Biller Details</h3>
        <p><strong>EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</strong></p>
        <p>EKT-5/B; Ek Tower, New Town, Rajarhat</p>
        <p>Street Number 692, Action Area IID, WB</p>
        <p>PH. NO.- +91 98368 80080 / 8961275478</p>
        <p>CIN-U72900WB2021PTC245728</p>
        <p>PAN-AAGCE5047E</p>
        <p>GSTIN-19AAGCE5047E1ZO</p>
    </div>
</section>
