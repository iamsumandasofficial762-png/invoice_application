<section class="invoice-summary invoice-lower-grid">
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
</section>
