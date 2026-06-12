<x-layouts.app title="Invoice {{ $invoice->invoice_number }}">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-share.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/invoice-status.js') }}"></script>
        <script src="{{ asset('assets/js/invoice-share.js') }}"></script>
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoice</p>
            <h1>{{ $invoice->invoice_number }}</h1>
        </div>
        <x-action-buttons
            :item="$invoice"
            route-prefix="invoices"
            :show-view="false"
            show-pdf="true"
            show-print="true"
            show-share="true"
            class="invoice-action-buttons"
        />
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
                data-status-text="paid"
                data-action="{{ route('invoices.status', $invoice) }}"
            >Paid</button>
            <button
                class="status-action-button status-action-cancel"
                type="button"
                data-status-trigger
                data-status="unpaid"
                data-status-text="cancel"
                data-action="{{ route('invoices.status', $invoice) }}"
            >Cancel</button>
        </div>
    </section>

    @include('invoices.partials.invoice-template', ['invoice' => $invoice])

    <div class="status-confirm-modal" id="statusConfirmModal" aria-hidden="true">
        <div class="status-confirm-card">
            <h2>Confirm Status Change</h2>
            <p>Are you sure you want to mark this invoice as <strong data-status-label>paid</strong>?</p>
            <form method="POST" id="statusConfirmForm">
                @csrf
                @method('PATCH')
                <input type="hidden" name="payment_status" id="statusConfirmValue">
                <div class="status-confirm-actions">
                    <button class="btn status-confirm-button status-confirm-cancel" type="button" data-status-cancel>Cancel</button>
                    <button class="btn status-confirm-button status-confirm-submit" type="submit" data-status-confirm>Confirm</button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
