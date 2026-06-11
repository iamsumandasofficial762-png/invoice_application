<tr data-item-row>
    <td class="sr-no">{{ is_numeric($index) ? $index + 1 : '' }}</td>
    <td>
        <textarea class="compact-description" name="items[{{ $index }}][description]" rows="2" required>{{ old("items.$index.description", $item['description'] ?? '') }}</textarea>
        <x-form-error name="items.{{ $index }}.description" />
    </td>
    <td>
        <input type="text" name="items[{{ $index }}][sac_code]" value="{{ old("items.$index.sac_code", $item['sac_code'] ?? '9983') }}" required>
        <x-form-error name="items.{{ $index }}.sac_code" />
    </td>
    <td>
        <input type="number" step="0.01" min="0" name="items[{{ $index }}][unit_price]" value="{{ old("items.$index.unit_price", $item['unit_price'] ?? '') }}" data-unit-price required>
        <x-form-error name="items.{{ $index }}.unit_price" />
    </td>
    <td>
        <input type="text" value="0.00" data-amount readonly>
    </td>
    <td>
        <button class="remove-item-button" type="button" data-remove-item>Remove</button>
    </td>
</tr>
