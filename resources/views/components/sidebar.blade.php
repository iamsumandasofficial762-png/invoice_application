<aside class="sidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
        <a href="{{ route('customers.index') }}" @class(['active' => request()->routeIs('customers.*')])>Customers</a>
        <a href="{{ route('invoices.index') }}" @class(['active' => request()->routeIs('invoices.*')])>Invoices</a>
    </nav>
</aside>
