<table class="invoice-table invoice-items-table">
    <thead>
        <tr>
            <th>SR. NO.</th>
            <th>DESCRIPTION</th>
            <th>SAC CODE</th>
            <th>UNIT PRICE</th>
            <th>AMOUNT(INR)</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($invoice->items as $item)
            <tr>
                <td>{{ $item->sr_no }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ $item->sac_code }}</td>
                <td>{{ number_format((float) $item->unit_price, 2) }}</td>
                <td>{{ number_format((float) $item->amount, 2) }}</td>
            </tr>
        @endforeach
        @if (($pdfMode ?? false) || ($printMode ?? false))
            @for ($i = $invoice->items->count(); $i < 4; $i++)
                <tr class="invoice-filler-row">
                    <td>&nbsp;</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            @endfor
        @endif
    </tbody>
</table>
