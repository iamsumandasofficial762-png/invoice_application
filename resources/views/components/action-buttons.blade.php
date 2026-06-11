@props([
    'item',
    'routePrefix',
    'showPdf' => false,
    'showPrint' => false,
])

<div class="action-group">
    <a class="btn btn-light btn-sm" href="{{ route($routePrefix.'.show', $item) }}">View</a>
    <a class="btn btn-light btn-sm" href="{{ route($routePrefix.'.edit', $item) }}">Edit</a>
    @if ($showPdf)
        <a class="btn btn-light btn-sm" href="{{ route($routePrefix.'.pdf', $item) }}">Download PDF</a>
    @endif
    @if ($showPrint)
        <a class="btn btn-light btn-sm" href="{{ route($routePrefix.'.print', $item) }}">Print</a>
    @endif
    <form method="POST" action="{{ route($routePrefix.'.destroy', $item) }}" data-confirm="Are you sure you want to delete this record?">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm" type="submit">Delete</button>
    </form>
</div>
