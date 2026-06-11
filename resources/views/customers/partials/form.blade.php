<div class="form-grid">
    <label>
        <span>Name</span>
        <input type="text" name="name" value="{{ old('name', $customer?->name) }}" required>
        <x-form-error name="name" />
    </label>
    <label>
        <span>State</span>
        <input type="text" name="state" value="{{ old('state', $customer?->state) }}" required>
        <x-form-error name="state" />
    </label>
    <label>
        <span>PIN</span>
        <input type="text" name="pin" value="{{ old('pin', $customer?->pin) }}" required>
        <x-form-error name="pin" />
    </label>
    <label>
        <span>Phone</span>
        <input type="text" name="phone" value="{{ old('phone', $customer?->phone) }}">
        <x-form-error name="phone" />
    </label>
    <label>
        <span>Gmail</span>
        <input type="email" name="gmail" value="{{ old('gmail', $customer?->gmail) }}">
        <x-form-error name="gmail" />
    </label>
    <label>
        <span>GST</span>
        <input type="text" name="gst" value="{{ old('gst', $customer?->gst) }}" required>
        <x-form-error name="gst" />
    </label>
    <label class="span-2">
        <span>Address</span>
        <textarea name="address" rows="4" required>{{ old('address', $customer?->address) }}</textarea>
        <x-form-error name="address" />
    </label>
</div>
