@if (session('success'))
    <div class="alert alert-success" data-flash-message>
        <span>{{ session('success') }}</span>
        <button class="alert-close" type="button" data-flash-close aria-label="Close message">X</button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error" data-flash-message>
        <span>{{ session('error') }}</span>
        <button class="alert-close" type="button" data-flash-close aria-label="Close message">X</button>
    </div>
@endif
