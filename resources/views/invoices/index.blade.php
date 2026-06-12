<x-layouts.app title="Invoices">
    @push('styles')
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-list.css') }}">
        <link rel="stylesheet" href="{{ asset('assets/css/invoice-share.css') }}">
    @endpush

    @push('scripts')
        <script src="{{ asset('assets/js/invoice-search.js') }}"></script>
        <script src="{{ asset('assets/js/invoice-share.js') }}"></script>
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Invoice List</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('invoices.create') }}">Create Invoice</a>
    </section>

    <section class="invoice-list-card" data-invoice-search-url="{{ route('invoices.liveSearch', [], false) }}">
        <form class="invoice-list-toolbar list-control-form" method="GET" action="{{ route('invoices.index') }}">
            <div class="list-search-control">
                <label class="search-label" for="invoiceLiveSearch">Search invoices</label>
                <input
                    type="search"
                    id="invoiceLiveSearch"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by customer, GST, invoice number, status, or date"
                    autocomplete="off"
                >
            </div>
            <label class="per-page-control">
                <span>Show</span>
                <select name="per_page" data-per-page-select>
                    @foreach ([10, 25, 50, 100] as $option)
                        <option value="{{ $option }}" @selected($perPage === $option)>{{ $option }}</option>
                    @endforeach
                </select>
                <span>entries per page</span>
            </label>
            <p class="invoice-count" id="invoiceCount">
                Showing {{ $invoices->total() }} {{ $invoices->total() === 1 ? 'invoice' : 'invoices' }}
            </p>
        </form>

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
                            <td data-label="Invoice No.">{{ $invoice->invoice_number }}</td>
                            <td data-label="Date">{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td data-label="Customer">{{ $invoice->customer->name }}</td>
                            <td data-label="GST"><span class="gst-badge">{{ $invoice->customer->gst }}</span></td>
                            <td data-label="Net Payable">Rs. {{ number_format((float) $invoice->net_payable_amount, 2) }}</td>
                            <td data-label="Status">
                                <span class="status-badge status-{{ $invoice->payment_status ?? 'unpaid' }}">
                                    {{ ucfirst($invoice->payment_status ?? 'unpaid') }}
                                </span>
                            </td>
                            <td data-label="Actions">
                                <div class="invoice-list-actions">
                                    <a class="invoice-list-action invoice-action-view icon-btn icon-btn-primary" href="{{ route('invoices.show', $invoice) }}" title="View invoice" aria-label="View invoice">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <a class="invoice-list-action invoice-action-edit icon-btn icon-btn-warning" href="{{ route('invoices.edit', $invoice) }}" title="Edit invoice" aria-label="Edit invoice">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <a class="invoice-list-action invoice-action-pdf icon-btn icon-btn-info" href="{{ route('invoices.pdf', $invoice) }}" title="Download PDF" aria-label="Download PDF">
                                        <i class="far fa-file-pdf"></i>
                                    </a>
                                    <a class="invoice-list-action invoice-action-print icon-btn icon-btn-primary" href="{{ route('invoices.print', $invoice) }}" title="Print invoice" aria-label="Print invoice">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    <button
                                        class="invoice-list-action invoice-action-share icon-btn icon-btn-success invoice-native-share"
                                        type="button"
                                        data-invoice-number="{{ $invoice->invoice_number }}"
                                        data-customer-name="{{ $invoice->customer->name }}"
                                        data-net-payable="{{ number_format((float) $invoice->net_payable_amount, 2) }}"
                                        data-pdf-url="{{ route('invoices.pdf', $invoice) }}"
                                        title="Share invoice"
                                        aria-label="Share invoice"
                                    >
                                        <i class="fas fa-share-alt"></i>
                                    </button>
                                    <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" data-confirm="Are you sure you want to delete this invoice?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="invoice-list-action invoice-action-delete icon-btn icon-btn-danger" type="submit" title="Delete invoice" aria-label="Delete invoice">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="invoice-mobile-cards" id="invoiceMobileCards">
            @foreach ($invoices as $invoice)
                @php
                    $status = $invoice->payment_status ?? 'unpaid';
                    $netPayable = number_format((float) $invoice->net_payable_amount, 2);
                @endphp
                <article class="invoice-mobile-card">
                    <header class="invoice-mobile-card-header">
                        <h2>{{ $invoice->invoice_number }}</h2>
                        <span class="status-badge status-{{ $status }}">{{ ucfirst($status) }}</span>
                    </header>

                    <div class="invoice-mobile-info">
                        <div class="invoice-mobile-row invoice-mobile-row-customer">
                            <span class="invoice-mobile-icon invoice-mobile-icon-customer"><i class="far fa-user"></i></span>
                            <strong>{{ $invoice->customer->name }}</strong>
                        </div>
                        <div class="invoice-mobile-row invoice-mobile-row-gst">
                            <span class="invoice-mobile-icon invoice-mobile-icon-gst"><i class="fas fa-receipt"></i></span>
                            <span>GST</span>
                            <span class="gst-badge">{{ $invoice->customer->gst }}</span>
                        </div>
                        <div class="invoice-mobile-row invoice-mobile-row-date">
                            <span class="invoice-mobile-icon invoice-mobile-icon-date"><i class="far fa-calendar-alt"></i></span>
                            <span>Date</span>
                            <strong>{{ $invoice->invoice_date->format('d-m-Y') }}</strong>
                        </div>
                        <div class="invoice-mobile-row invoice-mobile-row-payable">
                            <span class="invoice-mobile-icon invoice-mobile-icon-payable"><i class="fas fa-rupee-sign"></i></span>
                            <span>Net Payable</span>
                            <strong>Rs. {{ $netPayable }}</strong>
                        </div>
                    </div>

                    <div class="invoice-mobile-actions">
                        <a class="invoice-mobile-action invoice-mobile-action-view icon-btn icon-btn-primary" href="{{ route('invoices.show', $invoice) }}" aria-label="View invoice" title="View invoice">
                            <i class="far fa-eye"></i>
                        </a>
                        <a class="invoice-mobile-action invoice-mobile-action-pdf icon-btn icon-btn-info" href="{{ route('invoices.pdf', $invoice) }}" aria-label="Download PDF" title="Download PDF">
                            <i class="far fa-file-pdf"></i>
                        </a>
                        <a class="invoice-mobile-action invoice-mobile-action-print icon-btn icon-btn-primary" href="{{ route('invoices.print', $invoice) }}" aria-label="Print invoice" title="Print invoice">
                            <i class="fas fa-print"></i>
                        </a>
                        <a class="invoice-mobile-action invoice-mobile-action-edit icon-btn icon-btn-warning" href="{{ route('invoices.edit', $invoice) }}" aria-label="Edit invoice" title="Edit invoice">
                            <i class="fas fa-pen"></i>
                        </a>
                        <button
                            class="invoice-mobile-action invoice-mobile-action-share icon-btn icon-btn-success invoice-native-share"
                            type="button"
                            data-invoice-number="{{ $invoice->invoice_number }}"
                            data-customer-name="{{ $invoice->customer->name }}"
                            data-net-payable="{{ $netPayable }}"
                            data-pdf-url="{{ route('invoices.pdf', $invoice) }}"
                            aria-label="Share invoice"
                            title="Share invoice"
                        >
                            <i class="fas fa-share-alt"></i>
                        </button>
                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" data-confirm="Are you sure you want to delete this invoice?">
                            @csrf
                            @method('DELETE')
                            <button class="invoice-mobile-action invoice-mobile-action-delete icon-btn icon-btn-danger" type="submit" aria-label="Delete invoice" title="Delete invoice">
                                <i class="fas fa-trash"></i>
                            </button>
                        </form>
                    </div>
                </article>
            @endforeach
        </div>

        <div class="invoice-empty-state {{ $invoices->count() ? '' : 'is-visible' }}" id="invoiceEmptyState">
            No invoices found.
        </div>

        <div id="invoicePagination" class="list-pagination">
            {{ $invoices->links() }}
        </div>
    </section>

</x-layouts.app>
