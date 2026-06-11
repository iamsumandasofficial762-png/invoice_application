@php
    $assetSource = fn (string $path) => ($pdfMode ?? false) ? public_path($path) : asset($path);
    $invoiceHeaderPath = file_exists(public_path('assets/images/invoice-header.png'))
        ? 'assets/images/invoice-header.png'
        : 'assets/images/invoice/invoice-header.png';
    $invoiceFooterPath = file_exists(public_path('assets/images/invoice-footer.png'))
        ? 'assets/images/invoice-footer.png'
        : 'assets/images/invoice/invoice-footer.png';
@endphp

<article class="invoice-document">
    <header class="invoice-image-header">
        <img src="{{ $assetSource($invoiceHeaderPath) }}" alt="Invoice header">
    </header>
    <div class="invoice-print-title">TAX INVOICE</div>

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

    <section class="invoice-lower-grid">
        <table class="invoice-total-table">
            <tbody>
                <tr>
                    <td>Total:-</td>
                    <td>{{ number_format((float) $invoice->subtotal, 2) }}</td>
                </tr>
                <tr>
                    <td>Add CGST @9 %</td>
                    <td>{{ number_format((float) $invoice->cgst, 2) }}</td>
                </tr>
                <tr>
                    <td>Add SGST @ 9 %</td>
                    <td>{{ number_format((float) $invoice->sgst, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Tax:- @ 18 %</td>
                    <td>{{ number_format((float) $invoice->total_tax, 2) }}</td>
                </tr>
                <tr>
                    <td>Gross Amount:-</td>
                    <td>{{ number_format((float) $invoice->gross_amount, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Net Payble Amt.</strong></td>
                    <td><strong>{{ number_format((float) $invoice->net_payable_amount, 2) }}</strong></td>
                </tr>
            </tbody>
        </table>

        <div class="invoice-amount-word-row">
            <strong>Amount In Word:-</strong>
            <strong>{{ $invoice->amount_in_words }}</strong>
        </div>

        <div class="invoice-payment-row">
            <strong>Payment Method:</strong>
            <span>E. &amp; O.E.</span>
        </div>

        <div class="invoice-bank-signature-grid">
            <div class="bank-details-block">
                <p>Cash / Cheque / NEFT/RTGS/IMPS</p>
                <h3>Bank Details:-</h3>
                <p>Bank: AXIS BANK</p>
                <p>Payee: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
                <p>A/C No. 925020019932587</p>
                <p>IFSC Code: UTIB0001656</p>
                <p>Branch: New Barrackpur Branch</p>
            </div>
            <div class="signature-block">
                <div class="for-row">
                    <strong>For:</strong>
                    <strong>EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</strong>
                </div>
                <img src="{{ $assetSource('assets/images/signatures/'.$invoice->signature_image) }}" alt="Authorized signature">
                <strong class="authorized-signature-label">Authorized Signature</strong>
            </div>
        </div>
    </section>

    <footer class="invoice-image-footer">
        <img src="{{ $assetSource($invoiceFooterPath) }}" alt="Invoice footer">
    </footer>
</article>
