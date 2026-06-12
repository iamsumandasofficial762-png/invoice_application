<aside class="sidebar" id="appSidebar">
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" @class(['active' => request()->routeIs('dashboard')])>Dashboard</a>
        <a href="{{ route('customers.index') }}" @class(['active' => request()->routeIs('customers.*')])>Customers</a>
        <a href="{{ route('invoices.index') }}" @class(['active' => request()->routeIs('invoices.*')])>Invoices</a>
        <a href="{{ route('profile') }}" @class(['active' => request()->routeIs('profile')])>Profile</a>
    </nav>

    @isset($authUser)
        <div class="sidebar-mobile-account">
            <div class="sidebar-mobile-user">
                <strong>{{ $authUser->name }}</strong>
                <span>{{ $authUser->email }}</span>
            </div>
            <a class="btn btn-light btn-sm" href="{{ route('profile') }}">Profile</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="btn btn-danger btn-sm" type="submit">Logout</button>
            </form>
        </div>
    @endisset
</aside>
