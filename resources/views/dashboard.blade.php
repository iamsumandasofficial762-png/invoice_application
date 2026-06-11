<x-layouts.app title="Dashboard">
    <section class="page-header">
        <div>
            <p class="eyebrow">EBLUESOFT INFOTECT SOLUTIONS PRIVATE LIMITED</p>
            <h1>Invoice Dashboard</h1>
        </div>
    </section>

    <div class="dashboard-grid">
        <a class="dashboard-card" href="{{ route('customers.index') }}">
            <span>Customers</span>
            <strong>Manage customer profiles</strong>
        </a>
        <a class="dashboard-card" href="{{ route('invoices.index') }}">
            <span>Invoices</span>
            <strong>Create, print, and download invoices</strong>
        </a>
    </div>
</x-layouts.app>
