@php
    $sacCode = old("items.$index.sac_code", $item['sac_code'] ?? '9983');
    $sacCode = trim((string) $sacCode) === '' ? '9983' : $sacCode;
@endphp

<tr data-item-row>
    <td class="sr-no" data-label="SR. NO.">{{ is_numeric($index) ? $index + 1 : '' }}</td>
    <td data-label="Description">
        <textarea class="compact-description" name="items[{{ $index }}][description]" rows="2" required>{{ old("items.$index.description", $item['description'] ?? '') }}</textarea>
        <x-form-error name="items.{{ $index }}.description" />
    </td>
    <td data-label="SAC Code">
        <input type="text" name="items[{{ $index }}][sac_code]" value="{{ $sacCode }}">
        <x-form-error name="items.{{ $index }}.sac_code" />
    </td>
    <td data-label="Unit Price">
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item['unit_price'] ?? '') }}" data-unit-price required>
        <x-form-error name="items.{{ $index }}.unit_price" />
    </td>
    <td data-label="Amount">
        <input type="text" value="0.00" data-amount readonly>
    </td>
    <td data-label="Action">
        <button class="remove-item-button" type="button" data-remove-item>Remove</button>
    </td>
</tr>
