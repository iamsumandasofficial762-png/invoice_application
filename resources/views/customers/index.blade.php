<x-layouts.app title="Customers">
    @push('scripts')
        <script src="{{ asset('assets/js/customer-search.js') }}"></script>
    @endpush

    <section class="page-header">
        <div>
            <p class="eyebrow">Customers</p>
            <h1>Customer List</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('customers.create') }}">Create Customer</a>
    </section>

    <section class="customer-list-card" data-customer-search-url="{{ route('customers.liveSearch', [], false) }}">
        <div class="customer-list-toolbar">
            <div>
                <label class="search-label" for="customerLiveSearch">Search customers</label>
                <input
                    type="search"
                    id="customerLiveSearch"
                    value="{{ $search }}"
                    placeholder="Search by name, GST, state, phone, or Gmail"
                    autocomplete="off"
                >
            </div>
            <p class="customer-count" id="customerCount">
                Showing {{ $customers->total() }} {{ $customers->total() === 1 ? 'customer' : 'customers' }}
            </p>
        </div>

        <div class="customer-table-wrap">
            <table class="customer-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>GST</th>
                        <th>State</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="customerTableBody">
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td><span class="gst-badge">{{ $customer->gst }}</span></td>
                            <td>{{ $customer->state }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td>
                                <div class="customer-actions">
                                    <a class="customer-action customer-action-view" href="{{ route('customers.show', $customer) }}">View</a>
                                    <a class="customer-action customer-action-edit" href="{{ route('customers.edit', $customer) }}">Edit</a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="Are you sure you want to delete this customer?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="customer-action customer-action-delete" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="customer-empty-state {{ $customers->count() ? '' : 'is-visible' }}" id="customerEmptyState">
            No customers found.
        </div>

        <div id="customerPagination">
            {{ $customers->links() }}
        </div>
    </section>
</x-layouts.app>
