<section class="payment-signature-grid">
    <div class="payment-box">
        <h3>Payment Method:</h3>
        <p>Cash / Cheque / NEFT/RTGS/IMPS</p>
        <h3>Bank Details:</h3>
        <p>Bank: AXIS BANK</p>
        <p>Payee: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
        <p>A/C No. 925020019932587</p>
        <p>IFSC Code: UTIB0001656</p>
        <p>Branch: New Barrackpur Branch</p>
    </div>
    <div class="signature-preview-box">
        <p><strong>For: EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</strong></p>
        <label>
            <span>Signature</span>
            <select name="signature_image" id="signature_image" required>
                @php
                    $selectedSignature = old('signature_image', $invoice?->signature_image ?? 'signature-1.png');
                @endphp
                @foreach ($signatures as $file => $label)
                    <option
                        value="{{ $file }}"
                        data-signature-src="{{ asset('assets/images/signatures/'.$file) }}"
                        @selected($selectedSignature === $file)
                    >{{ $label }}</option>
                @endforeach
            </select>
            <x-form-error name="signature_image" />
        </label>
        <div class="signature-image-frame">
            <img id="signaturePreview" alt="Selected authorized signature">
            <span id="signaturePlaceholder">Select signature</span>
        </div>
        <strong class="authorized-text">Authorized Signature</strong>
    </div>
</section>
