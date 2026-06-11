<x-layouts.app title="Invoices">
    <section class="page-header">
        <div>
            <p class="eyebrow">Invoices</p>
            <h1>Invoice List</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('invoices.create') }}">Create Invoice</a>
    </section>

    <form class="toolbar" method="GET" action="{{ route('invoices.index') }}">
        <input type="search" name="search" value="{{ $search }}" placeholder="Search by customer, GST, invoice number, or date">
        <button class="btn btn-secondary" type="submit">Search</button>
    </form>

    @if ($invoices->count())
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Invoice No.</th>
                        <th>Date</th>
                        <th>Customer</th>
                        <th>GST</th>
                        <th>Net Payable</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $invoice)
                        <tr>
                            <td>{{ $invoice->invoice_number }}</td>
                            <td>{{ $invoice->invoice_date->format('d-m-Y') }}</td>
                            <td>{{ $invoice->customer->name }}</td>
                            <td>{{ $invoice->customer->gst }}</td>
                            <td>Rs. {{ number_format((float) $invoice->net_payable_amount, 2) }}</td>
                            <td><x-action-buttons :item="$invoice" route-prefix="invoices" show-pdf="true" show-print="true" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $invoices->links() }}
    @else
        <div class="empty-state">No invoices found.</div>
    @endif
</x-layouts.app>
