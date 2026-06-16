<div class="form-grid">
    @php
        $indianStates = [
            'Andhra Pradesh',
            'Arunachal Pradesh',
            'Assam',
            'Bihar',
            'Chhattisgarh',
            'Goa',
            'Gujarat',
            'Haryana',
            'Himachal Pradesh',
            'Jharkhand',
            'Karnataka',
            'Kerala',
            'Madhya Pradesh',
            'Maharashtra',
            'Manipur',
            'Meghalaya',
            'Mizoram',
            'Nagaland',
            'Odisha',
            'Punjab',
            'Rajasthan',
            'Sikkim',
            'Tamil Nadu',
            'Telangana',
            'Tripura',
            'Uttar Pradesh',
            'Uttarakhand',
            'West Bengal',
            'Andaman and Nicobar Islands',
            'Chandigarh',
            'Dadra and Nagar Haveli and Daman and Diu',
            'Delhi',
            'Jammu and Kashmir',
            'Ladakh',
            'Lakshadweep',
            'Puducherry',
        ];
    @endphp

    <label>
        <span class="required-label">Name</span>
        <input type="text" name="name" value="{{ old('name', $customer?->name) }}" required>
        <x-form-error name="name" />
    </label>
    <label>
        <span class="required-label">State</span>
        <select name="state" required>
            <option value="">Select State</option>
            @foreach ($indianStates as $state)
                <option value="{{ $state }}" @selected(old('state', $customer?->state) === $state)>{{ $state }}</option>
            @endforeach
        </select>
        <x-form-error name="state" />
    </label>
    <label>
        <span class="required-label">PIN</span>
        <input type="text" name="pin" value="{{ old('pin', $customer?->pin) }}" required>
        <x-form-error name="pin" />
    </label>
    <label>
        <span>Phone</span>
        <input type="text" name="phone" value="{{ old('phone', $customer?->phone) }}">
        <x-form-error name="phone" />
    </label>
    <label>
        <span>Email</span>
        <input type="email" name="gmail" value="{{ old('gmail', $customer?->gmail) }}">
        <x-form-error name="gmail" />
    </label>
    <label>
        <span class="required-label">GST</span>
        <input type="text" name="gst" value="{{ old('gst', $customer?->gst) }}" required>
        <x-form-error name="gst" />
    </label>
    <label class="span-2">
        <span class="required-label">Address</span>
        <textarea name="address" rows="4" required>{{ old('address', $customer?->address) }}</textarea>
        <x-form-error name="address" />
    </label>
</div>
