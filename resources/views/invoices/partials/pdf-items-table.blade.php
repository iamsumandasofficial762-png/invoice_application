@php
    $itemCount = $items->count();

    if ($itemCount <= 1) {
        $fillerHeight = 150;
    } elseif ($itemCount === 2) {
        $fillerHeight = 110;
    } elseif ($itemCount === 3) {
        $fillerHeight = 70;
    } elseif ($itemCount === 4) {
        $fillerHeight = 35;
    } else {
        $fillerHeight = 0;
    }
@endphp

<table class="items-table pdf-items-table">
    <thead>
        <tr>
            <th>SR. NO.</th>
            <th>DESCRIPTION</th>
            <th>SAC CODE</th>
            <th>UNIT PRICE</th>
            <th>AMOUNT(INR)</th>
        </tr>
    </thead>
    <tbody class="pdf-items-body">
        @foreach ($items as $item)
            <tr class="pdf-item-row">
                <td>{{ $item->sr_no }}</td>
                <td class="pdf-item-description">{{ $item->description }}</td>
                <td>{{ $item->sac_code }}</td>
                <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                <td>{{ number_format((float) $item->amount, 2) }}</td>
            </tr>
        @endforeach

        @if ($fillerHeight > 0)
            <tr class="pdf-item-filler-row">
                <td style="height: {{ $fillerHeight }}px;"></td>
                <td style="height: {{ $fillerHeight }}px;"></td>
                <td style="height: {{ $fillerHeight }}px;"></td>
                <td style="height: {{ $fillerHeight }}px;"></td>
                <td style="height: {{ $fillerHeight }}px;"></td>
            </tr>
        @endif
    </tbody>
</table>
