<?php

namespace App\Services;

class InvoiceCalculationService
{
    public function calculate(array $items): array
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
        $cgst = round($subtotal * 0.09, 2);
        $sgst = round($subtotal * 0.09, 2);
        $totalTax = round($cgst + $sgst, 2);
        $grossAmount = round($subtotal + $totalTax, 2);
        $netPayableAmount = round($grossAmount, 0, PHP_ROUND_HALF_UP);

        return [
            'items' => $normalizedItems,
            'subtotal' => $subtotal,
            'cgst' => $cgst,
            'sgst' => $sgst,
            'total_tax' => $totalTax,
            'gross_amount' => $grossAmount,
            'net_payable_amount' => $netPayableAmount,
        ];
    }
}
