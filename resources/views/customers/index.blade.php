<x-layouts.app title="Customers">
    <section class="page-header">
        <div>
            <p class="eyebrow">Customers</p>
            <h1>Customer List</h1>
        </div>
        <a class="btn btn-primary" href="{{ route('customers.create') }}">Create Customer</a>
    </section>

    <form class="toolbar" method="GET" action="{{ route('customers.index') }}">
        <input type="search" name="search" value="{{ $search }}" placeholder="Search by customer name or GST">
        <button class="btn btn-secondary" type="submit">Search</button>
    </form>

    @if ($customers->count())
        <div class="table-wrap">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>GST</th>
                        <th>State</th>
                        <th>Phone</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($customers as $customer)
                        <tr>
                            <td>{{ $customer->name }}</td>
                            <td>{{ $customer->gst }}</td>
                            <td>{{ $customer->state }}</td>
                            <td>{{ $customer->phone ?? '-' }}</td>
                            <td><x-action-buttons :item="$customer" route-prefix="customers" /></td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        {{ $customers->links() }}
    @else
        <div class="empty-state">No customers found.</div>
    @endif
</x-layouts.app>
