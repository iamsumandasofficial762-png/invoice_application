@props([
    'item',
    'routePrefix',
    'showView' => true,
    'showPdf' => false,
    'showPrint' => false,
    'showShare' => false,
])

<div {{ $attributes->class(['action-group']) }}>
    @if ($showView)
        <a class="btn btn-light btn-sm icon-btn icon-btn-primary action-button-view" href="{{ route($routePrefix.'.show', $item) }}" title="View invoice" aria-label="View invoice">
            <i class="far fa-eye"></i>
        </a>
    @endif
    <a class="btn btn-light btn-sm icon-btn icon-btn-warning action-button-edit" href="{{ route($routePrefix.'.edit', $item) }}" title="Edit invoice" aria-label="Edit invoice">
        <i class="fas fa-pen"></i>
    </a>
    @if ($showPdf)
        <a class="btn btn-light btn-sm icon-btn icon-btn-info action-button-pdf" href="{{ route($routePrefix.'.pdf', $item) }}" title="Download PDF" aria-label="Download PDF">
            <i class="far fa-file-pdf"></i>
        </a>
    @endif
    @if ($showPrint)
        <a class="btn btn-light btn-sm icon-btn icon-btn-primary action-button-print" href="{{ route($routePrefix.'.print', $item) }}" title="Print invoice" aria-label="Print invoice">
            <i class="fas fa-print"></i>
        </a>
    @endif
    @if ($showShare)
        <button
            class="btn btn-light btn-sm icon-btn icon-btn-success action-button-share invoice-native-share"
            type="button"
            data-invoice-number="{{ $item->invoice_number }}"
            data-customer-name="{{ $item->customer->name }}"
            data-net-payable="{{ number_format((float) $item->net_payable_amount, 0) }}"
            data-pdf-url="{{ route($routePrefix.'.pdf', $item) }}"
            title="Share invoice"
            aria-label="Share invoice"
        >
            <i class="fas fa-share-alt"></i>
        </button>
    @endif
    <form method="POST" action="{{ route($routePrefix.'.destroy', $item) }}" data-confirm="Are you sure you want to delete this record?">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm icon-btn icon-btn-danger action-button-delete" type="submit" title="Delete invoice" aria-label="Delete invoice">
            <i class="fas fa-trash"></i>
        </button>
    </form>
</div>
