<div class="modal" id="customer-modal" aria-hidden="true">
    <div class="modal-panel">
        <div class="modal-header">
            <h2>Create New Customer</h2>
            <button class="icon-button" type="button" data-modal-close="customer-modal">X</button>
        </div>
        <form id="ajax-customer-form" action="{{ route('customers.ajaxStore') }}">
            <div class="form-grid">
                <label><span>Name</span><input type="text" name="name" required></label>
                <label><span>State</span><input type="text" name="state" required></label>
                <label><span>PIN</span><input type="text" name="pin" required></label>
                <label><span>Phone</span><input type="text" name="phone"></label>
                <label><span>Gmail</span><input type="email" name="gmail"></label>
                <label><span>GST</span><input type="text" name="gst" required></label>
                <label class="span-2"><span>Address</span><textarea name="address" rows="3" required></textarea></label>
            </div>
            <div class="modal-errors" data-modal-errors></div>
            <div class="form-actions">
                <button class="btn btn-light" type="button" data-modal-close="customer-modal">Cancel</button>
                <button class="btn btn-primary" type="submit">Save Customer</button>
            </div>
        </form>
    </div>
</div>
