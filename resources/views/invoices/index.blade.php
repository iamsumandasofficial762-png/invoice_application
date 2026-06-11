<x-layouts.app title="Invoices">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-list.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/invoice-search.js') }}"></script>
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Invoice List</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('invoices.create') }}">Create Invoice</a>
    </section>

    <section class="invoice-list-card" data-invoice-search-url="{{ route('invoices.liveSearch', [], false) }}">
        <div class="invoice-list-toolbar">
            <div>
                <label class="search-label" for="invoiceLiveSearch">Search invoices</label>
                <input
                    type="search"
                    id="invoiceLiveSearch"
                    value="{{ $search }}"
                    placeholder="Search by customer, GST, invoice number, status, or date"
                    autocomplete="off"
                >
            </div>
            <p class="invoice-count" id="invoiceCount">
                Showing {{ $invoices->total() }} {{ $invoices->total() === 1 ? 'invoice' : 'invoices' }}
            </p>
        </div>

        <div class="invoice-table-wrap">
            <table class="invoice-list-table">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>GST</th>
                        <th>Net Payable</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="invoiceTableBody">
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td><span class="gst-badge">{{ $invoice->customer->gst }}</span></td>
                            <td>Rs. {{ number_format((float) $invoice->net_payable_amount, 2) }}</td>
                            <td>
                                <span class="status-badge status-{{ $invoice->payment_status ?? 'unpaid' }}">
                                    {{ ucfirst($invoice->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td>
                                <div class="invoice-list-actions">
                                    <a class="invoice-list-action invoice-action-view" href="{{ route('invoices.show', $invoice) }}">View</a>
                                    <a class="invoice-list-action invoice-action-edit" href="{{ route('invoices.edit', $invoice) }}">Edit</a>
                                    <a class="invoice-list-action invoice-action-pdf" href="{{ route('invoices.pdf', $invoice) }}">Download PDF</a>
                                    <a class="invoice-list-action invoice-action-print" href="{{ route('invoices.print', $invoice) }}">Print</a>
                                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" data-confirm="Are you sure you want to delete this invoice?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="invoice-list-action invoice-action-delete" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-empty-state {{ $invoices->count() ? '' : 'is-visible' }}" id="invoiceEmptyState">
            No invoices found.
        </div>

        <div id="invoicePagination">
            {{ $invoices->links() }}
        </div>
    </section>
</x-layouts.app>
