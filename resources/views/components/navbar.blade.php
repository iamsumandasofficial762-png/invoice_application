<header class="topbar">
    <button class="mobile-menu-toggle" type="button" data-mobile-menu-toggle aria-label="Open navigation" aria-controls="appSidebar" aria-expanded="false">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <a class="brand" href="{{ route('dashboard') }}">
        <img class="brand-logo" src="{{ asset('assets/images/logo.png') }}" alt="Ebluesoft logo">
        <span>Invoice Manager</span>
    </a>

    @isset($authUser)
        <div class="topbar-user">
            <div>
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
</header>
