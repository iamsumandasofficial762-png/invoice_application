<section class="invoice-items-section">
    <div class="invoice-section-heading">
        <h3>INVOICE ITEMS</h3>
        <button class="btn btn-secondary btn-sm" type="button" data-add-item>Add Item</button>
    </div>
    <div class="invoice-table-wrap">
        <table class="invoice-form-table">
            <thead>
                <tr>
                    <th>SR. NO.</th>
                    <th>DESCRIPTION</th>
                    <th>SAC CODE</th>
                    <th>UNIT PRICE</th>
                    <th>AMOUNT(INR)</th>
                    <th>ACTION</th>
                </tr>
            </thead>
            <tbody data-items-body>
                @foreach ($items as $index => $item)
                    @include('invoices.partials.item-row', ['index' => $index, 'item' => $item])
                @endforeach
            </tbody>
        </table>
    </div>
    <x-form-error name="items" />
</section>
