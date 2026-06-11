<x-layouts.app title="Customer Details">
    <section class="customer-profile-card">
        <div class="customer-profile-header">
            <div class="customer-profile-main">
                <img class="customer-profile-avatar" src="{{ asset('assets/images/customer-avater.png') }}" alt="Customer avatar">
                <div>
                    <h1>{{ $customer->name }}</h1>
                    <span class="customer-profile-badge">Customer Profile</span>
                </div>
            </div>

            <div class="customer-profile-actions">
                <a class="profile-action profile-action-edit" href="{{ route('customers.edit', $customer) }}">Edit</a>
                <form method="POST" action="{{ route('customers.destroy', $customer) }}" data-confirm="Are you sure you want to delete this customer?">
                    @csrf
                    @method('DELETE')
                    <button class="profile-action profile-action-delete" type="submit">Delete</button>
                </form>
            </div>
        </div>

        <div class="customer-profile-details">
            <div class="customer-profile-column">
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="far fa-address-card"></i></span>
                    <div>
                        <span>Address</span>
                        <strong>{{ $customer->address }}</strong>
                    </div>
                </div>
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="fas fa-university"></i></span>
                    <div>
                        <span>State</span>
                        <strong>{{ $customer->state }}</strong>
                    </div>
                </div>
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="fas fa-map-pin"></i></span>
                    <div>
                        <span>PIN</span>
                        <strong>{{ $customer->pin }}</strong>
                    </div>
                </div>
            </div>

            <div class="customer-profile-column">
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="fas fa-envelope"></i></span>
                    <div>
                        <span>Gmail</span>
                        <strong>{{ $customer->gmail ?? '-' }}</strong>
                    </div>
                </div>
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="fas fa-phone"></i></span>
                    <div>
                        <span>Phone</span>
                        <strong>{{ $customer->phone ?? '-' }}</strong>
                    </div>
                </div>
                <div class="customer-info-item">
                    <span class="customer-info-icon"><i class="fas fa-receipt"></i></span>
                    <div>
                        <span>GST</span>
                        <strong>{{ $customer->gst }}</strong>
                    </div>
                </div>
            </div>
        </div>
    </section>
</x-layouts.app>
