<x-layouts.app title="Create Customer">
    <section class="page-header">
        <div>
            <p class="eyebrow">Customers</p>
            <h1>Create Customer</h1>
        </div>
    </section>

    <form class="form-card" method="POST" action="{{ route('customers.store') }}">
        @csrf
        @include('customers.partials.form', ['customer' => null])
        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('customers.index') }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Save Customer</button>
        </div>
    </form>
</x-layouts.app>
