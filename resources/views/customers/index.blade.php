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
        <form class="customer-list-toolbar list-control-form" method="GET" action="{{ route('customers.index') }}">
            <div class="list-search-control">
                <label class="search-label" for="customerLiveSearch">Search customers</label>
                <input
                    type="search"
                    id="customerLiveSearch"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search by name, GST, state, phone, or Gmail"
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
            <p class="customer-count" id="customerCount">
                Showing {{ $customers->total() }} {{ $customers->total() === 1 ? 'customer' : 'customers' }}
            </p>
        </form>

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
                            <td data-label="Name">{{ $customer->name }}</td>
                            <td data-label="GST"><span class="gst-badge">{{ $customer->gst }}</span></td>
                            <td data-label="State">{{ $customer->state }}</td>
                            <td data-label="Phone">{{ $customer->phone ?? '-' }}</td>
                            <td data-label="Actions">
                                <div class="customer-actions">
                                    <a class="customer-action customer-action-view icon-btn icon-btn-primary" href="{{ route('customers.show', $customer) }}" title="View customer" aria-label="View customer">
                                        <i class="far fa-eye"></i>
                                    </a>
                                    <a class="customer-action customer-action-edit icon-btn icon-btn-warning" href="{{ route('customers.edit', $customer) }}" title="Edit customer" aria-label="Edit customer">
                                        <i class="fas fa-pen"></i>
                                    </a>
                                    <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="Are you sure you want to delete this customer?">
                                        @csrf
                                        @method('DELETE')
                                        <button class="customer-action customer-action-delete icon-btn icon-btn-danger" type="submit" title="Delete customer" aria-label="Delete customer">
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

        <div class="customer-empty-state {{ $customers->count() ? '' : 'is-visible' }}" id="customerEmptyState">
            No customers found.
        </div>

        <div id="customerPagination" class="list-pagination">
            {{ $customers->links() }}
        </div>
    </section>
</x-layouts.app>
