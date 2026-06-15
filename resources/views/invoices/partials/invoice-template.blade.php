@php
    $isPdf = $pdfMode ?? false;
    $isPrint = $printMode ?? false;
    $assetSource = fn (string $path) => $isPdf ? public_path($path) : asset($path);
    $invoiceHeaderPath = file_exists(public_path('assets/images/invoice-header.png'))
        ? 'assets/images/invoice-header.png'
        : 'assets/images/invoice/invoice-header.png';
    $invoiceFooterPath = file_exists(public_path('assets/images/invoice-footer.png'))
        ? 'assets/images/invoice-footer.png'
        : 'assets/images/invoice/invoice-footer.png';
@endphp

<article class="invoice-document invoice-page">
    <table class="invoice-wrapper">
        <tr>
            <td class="invoice-header-cell">
                @include('invoices.partials.invoice-header', [
                    'assetSource' => $assetSource,
                    'invoiceHeaderPath' => $invoiceHeaderPath,
                ])
            </td>
        </tr>
        <tr>
            <td class="invoice-content">
                <div class="invoice-print-title">TAX INVOICE</div>
                @include('invoices.partials.invoice-buyer-biller', ['invoice' => $invoice])
                <section class="invoice-meta">
                    <div><strong>Invoice No.:</strong> {{ $invoice->invoice_number }}</div>
                    <div><strong>Invoice Date:</strong> {{ $invoice->invoice_date->format('d-m-Y') }}</div>
                </section>
                @include('invoices.partials.invoice-items', [
                    'invoice' => $invoice,
                    'pdfMode' => $isPdf,
                    'printMode' => $isPrint,
                ])
                @include('invoices.partials.invoice-summary', ['invoice' => $invoice])
                @include('invoices.partials.invoice-payment-signature', [
                    'invoice' => $invoice,
                    'assetSource' => $assetSource,
                ])
            </td>
        </tr>
        <tr>
            <td class="invoice-footer-cell">
                @include('invoices.partials.invoice-footer', [
                    'assetSource' => $assetSource,
                    'invoiceFooterPath' => $invoiceFooterPath,
                ])
            </td>
        </tr>
    </table>
</article>
