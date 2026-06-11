@php
    $assetSource = fn (string $path) => ($pdfMode ?? false) ? public_path($path) : asset($path);
@endphp

<article class="invoice-document">
    <header class="invoice-header">
        <div class="invoice-brand">
            <img src="{{ $assetSource('assets/images/logo.png') }}" alt="Ebluesoft logo">
            <div>
                <h2>EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</h2>
                <p>Smart Digital Business Solutions</p>
            </div>
        </div>
        <h1>TAX INVOICE</h1>
    </header>

    <section class="invoice-party-grid">
        <div>
            <h3>Buyer Details</h3>
            <p><strong>{{ $invoice->customer->name }}</strong></p>
            <p>{{ $invoice->customer->address }}</p>
            <p>{{ $invoice->customer->state }} - {{ $invoice->customer->pin }}</p>
            <p>Phone: {{ $invoice->customer->phone ?? '-' }}</p>
            <p>Gmail: {{ $invoice->customer->gmail ?? '-' }}</p>
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

    <section class="invoice-meta">
        <div><strong>Invoice No.:</strong> {{ $invoice->invoice_number }}</div>
        <div><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</div>
    </section>

    <table class="invoice-table">
        <thead>
            <tr>
                <th>SR. NO.</th>
                <th>DESCRIPTION</th>
                <th>SAC CODE</th>
                <th>UNIT PRICE</th>
                <th>AMOUNT(INR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($invoice->items as $item)
                <tr>
                    <td>{{ $item->sr_no }}</td>
                    <td>{{ $item->description }}</td>
                    <td>{{ $item->sac_code }}</td>
                    <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                    <td>{{ number_format((float) $item->amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <section class="invoice-bottom-grid">
        <div class="amount-words">
            <h3>Amount In Word</h3>
            <p>{{ $invoice->amount_in_words }}</p>
            <h3>Payment Method:</h3>
            <p>Cash / Cheque / NEFT/RTGS/IMPS</p>
            <h3>Bank Details:</h3>
            <p>Bank: AXIS BANK</p>
            <p>Payee: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
            <p>A/C No. 925020019932587</p>
            <p>IFSC Code: UTIB0001656</p>
            <p>Branch: New Barrackpur Branch</p>
        </div>
        <div class="invoice-totals">
            <div><span>Total</span><strong>{{ number_format((float) $invoice->subtotal, 2) }}</strong></div>
            <div><span>Add CGST @9 %</span><strong>{{ number_format((float) $invoice->cgst, 2) }}</strong></div>
            <div><span>Add SGST @ 9 %</span><strong>{{ number_format((float) $invoice->sgst, 2) }}</strong></div>
            <div><span>Total Tax:- @ 18 %</span><strong>{{ number_format((float) $invoice->total_tax, 2) }}</strong></div>
            <div><span>Gross Amount:-</span><strong>{{ number_format((float) $invoice->gross_amount, 2) }}</strong></div>
            <div><span>Net Payble Amt.</span><strong>{{ number_format((float) $invoice->net_payable_amount, 2) }}</strong></div>
        </div>
    </section>

    <section class="signature-block">
        <p>For: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
        <img src="{{ $assetSource('assets/images/signatures/'.$invoice->signature_image) }}" alt="Authorized signature">
        <strong>Authorized Signature</strong>
    </section>

    <footer class="invoice-footer">
        <span>www.ebluesoft.com</span>
    </footer>
</article>
