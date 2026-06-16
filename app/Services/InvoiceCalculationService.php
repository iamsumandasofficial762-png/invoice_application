<?php

namespace App\Services;

class InvoiceCalculationService
{
    private const COMPANY_STATE = 'West Bengal';
    private const TAX_TYPE_INTRA_STATE = 'intra_state';
    private const TAX_TYPE_INTER_STATE = 'inter_state';

    public function calculate(array $items, ?string $customerState = null): array
    {
        $normalizedItems = [];
        $subtotal = 0;

        foreach (array_values($items) as $index => $item) {
            $unitPrice = round((float) ($item['unit_price'] ?? 0), 2);
            $amount = round($unitPrice, 2);
            $subtotal += $amount;

            $normalizedItems[] = [
                'sr_no' => $index + 1,
                'description' => $item['description'] ?? '',
                'sac_code' => $item['sac_code'] ?? '',
                'unit_price' => $unitPrice,
                'amount' => $amount,
            ];
        }

        $subtotal = round($subtotal, 2);
        $taxType = $this->taxTypeForState($customerState);
        $cgst = 0.00;
        $sgst = 0.00;
        $igst = 0.00;

        if ($taxType === self::TAX_TYPE_INTRA_STATE) {
            $cgst = round($subtotal * 0.09, 2);
            $sgst = round($subtotal * 0.09, 2);
        } else {
            $igst = round($subtotal * 0.18, 2);
        }

        $totalTax = round($cgst + $sgst + $igst, 2);
        $grossAmount = round($subtotal + $totalTax, 2);
        $netPayableAmount = round($grossAmount, 0, PHP_ROUND_HALF_UP);

        return [
            'items' => $normalizedItems,
            'subtotal' => $subtotal,
            'tax_type' => $taxType,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'igst' => $igst,
            'total_tax' => $totalTax,
            'gross_amount' => $grossAmount,
            'net_payable_amount' => $netPayableAmount,
        ];
    }

    public function taxTypeForState(?string $customerState): string
    {
        return trim((string) $customerState) === self::COMPANY_STATE
            ? self::TAX_TYPE_INTRA_STATE
            : self::TAX_TYPE_INTER_STATE;
    }
}
