@php
    $toDataUri = function (string $path): string {
        if (! file_exists($path)) {
            return '';
        }

        return 'data:'.mime_content_type($path).';base64,'.base64_encode(file_get_contents($path));
    };

    $invoiceHeaderPath = public_path('assets/images/invoice/invoice-header.png');
    $invoiceFooterPath = public_path('assets/images/invoice/invoice-footer.png');
    $signaturePath = public_path('assets/images/signatures/'.$invoice->signature_image);
    $invoiceHeaderSrc = $toDataUri($invoiceHeaderPath);
    $invoiceFooterSrc = $toDataUri($invoiceFooterPath);
    $signatureSrc = $toDataUri($signaturePath);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>{{ $invoice->invoice_number }}</title>
    <style>
        {!! file_get_contents(public_path('assets/css/invoice-pdf.css')) !!}
    </style>
</head>
<body>
    <div class="invoice-page invoice-pdf-wrapper">
        <div class="invoice-header">
            <img src="{{ $invoiceHeaderSrc }}" class="pdf-header-img" alt="Invoice header">
        </div>

        <div class="invoice-title">TAX INVOICE</div>

        <table class="two-column-table">
            <tr>
                <td>
                    <h3>Buyer Details</h3>
                    <p><strong>{{ $invoice->customer->name }}</strong></p>
                    <p>{{ $invoice->customer->address }}</p>
                    <p>{{ $invoice->customer->state }} - {{ $invoice->customer->pin }}</p>
                    <p>Phone: {{ $invoice->customer->phone ?? '-' }}</p>
                    @if (!empty($invoice->customer->gmail))
                        <p>Email: {{ $invoice->customer->gmail }}</p>
                    @endif
                    <p>GSTIN: {{ $invoice->customer->gst }}</p>
                </td>
                <td>
                    <h3>Biller Details</h3>
                    <p><strong>EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</strong></p>
                    <p>EKT-5/B; Ek Tower, New Town, Rajarhat</p>
                    <p>Street Number 692, Action Area IID, WB</p>
                    <p>PH. NO.- +91 98368 80080 / 8961275478</p>
                    <p>CIN-U72900WB2021PTC245728</p>
                    <p>PAN-AAGCE5047E</p>
                    <p>GSTIN-19AAGCE5047E1ZO</p>
                </td>
            </tr>
        </table>

        <table class="meta-table">
            <tr>
                <td><strong>Invoice No.:</strong> {{ $invoice->invoice_number }}</td>
                <td><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</td>
            </tr>
        </table>

        @include('invoices.partials.pdf-items-table', ['items' => $invoice->items])

        <table class="total-table">
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
        </table>

        <table class="info-table">
            <tr>
                <td><strong>Amount In Word:-</strong></td>
                <td><strong>{{ $invoice->amount_in_words }}</strong></td>
            </tr>
            <tr>
                <td><strong>Payment Method:</strong></td>
                <td>E. &amp; O.E.</td>
            </tr>
        </table>

        <table class="payment-signature-table">
            <tr>
                <td class="bank-cell">
                    <p>Cash / Cheque / NEFT/RTGS/IMPS</p>
                    <h3>Bank Details:-</h3>
                    <p>Bank: AXIS BANK</p>
                    <p>Payee: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
                    <p>A/C No. 925020019932587</p>
                    <p>IFSC Code: UTIB0001656</p>
                    <p>Branch: New Barrackpur Branch</p>
                </td>
                <td class="signature-cell">
                    <div class="for-row">
                        <span>For:</span>
                        <strong>EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</strong>
                    </div>
                    <img src="{{ $signatureSrc }}" alt="Authorized signature">
                    <strong class="authorized-signature-label">Authorized Signature</strong>
                </td>
            </tr>
        </table>

        <div class="invoice-footer">
            <img src="{{ $invoiceFooterSrc }}" class="pdf-footer-img" alt="Invoice footer">
        </div>
    </div>
</body>
</html>
