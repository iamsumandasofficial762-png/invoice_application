<section class="invoice-summary invoice-lower-grid">
    @php
        $isIntraState = trim((string) $invoice->customer?->state) === 'West Bengal';
        $igst = (float) ($invoice->igst ?? 0);
        $igst = $igst > 0 ? $igst : (float) $invoice->total_tax;
    @endphp

    <table class="invoice-total-table">
        <tbody>
            <tr>
                <td>Total:-</td>
                <td>{{ number_format((float) $invoice->subtotal, 2) }}</td>
            </tr>
            @if ($isIntraState)
                <tr>
                    <td>CGST @ 9 %</td>
                    <td>{{ number_format((float) $invoice->cgst, 2) }}</td>
                </tr>
                <tr>
                    <td>SGST @ 9 %</td>
                    <td>{{ number_format((float) $invoice->sgst, 2) }}</td>
                </tr>
                <tr>
                    <td>Total Tax:- @ 18 %</td>
                    <td>{{ number_format((float) $invoice->total_tax, 2) }}</td>
                </tr>
            @else
                <tr>
                    <td>IGST @ 18 %</td>
                    <td>{{ number_format($igst, 2) }}</td>
                </tr>
            @endif
            <tr>
                <td>Gross Amount:-</td>
                <td>{{ number_format((float) $invoice->gross_amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Net Payble Amt.</strong></td>
                <td><strong>{{ number_format((float) $invoice->net_payable_amount, 0) }}</strong></td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-amount-word-row">
        <strong>Amount In Word:-</strong>
        <strong class="invoice-amount-word-value">{{ $invoice->amount_in_words }}</strong>
    </div>

</section>
