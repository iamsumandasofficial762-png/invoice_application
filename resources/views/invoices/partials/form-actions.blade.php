<div class="invoice-form-actions">
    @if ($mode === 'create')
        <button class="btn btn-primary" type="submit" name="action" value="save">Save</button>
        <button class="btn btn-secondary" type="submit" name="action" value="save_print">Save &amp; Print</button>
        <a class="btn btn-light" href="{{ route('invoices.index') }}">Cancel</a>
    @else
        <button class="btn btn-primary" type="submit">Update Invoice</button>
        <a class="btn btn-light" href="{{ route('invoices.show', $invoice) }}">Cancel</a>
    @endif
</div>
