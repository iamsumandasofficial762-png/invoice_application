<x-layouts.app title="Edit Customer">
    <section class="page-header">
        <div>
            <p class="eyebrow">Customers</p>
            <h1>Edit Customer</h1>
        </div>
    </section>

    <form class="form-card" method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')
        @include('customers.partials.form', ['customer' => $customer])
        <div class="form-actions">
            <a class="btn btn-light" href="{{ route('customers.show', $customer) }}">Cancel</a>
            <button class="btn btn-primary" type="submit">Update Customer</button>
        </div>
    </form>
</x-layouts.app>
