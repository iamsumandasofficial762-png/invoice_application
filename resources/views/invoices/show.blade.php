<x-layouts.app title="Invoice {{ $invoice->invoice_number }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/invoice-status.js') }}"></script>
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoice</p>
            <h1>{{ $invoice->invoice_number }}</h1>
        </div>
        <x-action-buttons :item="$invoice" route-prefix="invoices" show-pdf="true" show-print="true" />
    </section>

    <section class="invoice-status-panel">
        <div>
            <span class="invoice-status-label">Payment Status</span>
            <strong class="status-badge status-{{ $invoice->payment_status ?? 'unpaid' }}">
                {{ ucfirst($invoice->payment_status ?? 'unpaid') }}
            </strong>
        </div>
        <div class="invoice-status-actions">
            <button
                class="status-action-button status-action-paid"
                type="button"
                data-status-trigger
                data-status="paid"
                data-action="{{ route('invoices.status', $invoice) }}"
            >Paid</button>
            <button
                class="status-action-button status-action-unpaid"
                type="button"
                data-status-trigger
                data-status="unpaid"
                data-action="{{ route('invoices.status', $invoice) }}"
            >Unpaid</button>
        </div>
    </section>

    @include('invoices.partials.invoice-document', ['invoice' => $invoice])

    <div class="status-confirm-modal" id="statusConfirmModal" aria-hidden="true">
        <div class="status-confirm-card">
            <h2>Confirm Status Change</h2>
            <p>Are you sure you want to mark this invoice as <strong data-status-label>paid</strong>?</p>
            <form method="POST" id="statusConfirmForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="payment_status" id="statusConfirmValue">
                <div class="status-confirm-actions">
                    <button class="btn btn-light" type="button" data-status-cancel>Cancel</button>
                    <button class="btn btn-primary" type="submit">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
