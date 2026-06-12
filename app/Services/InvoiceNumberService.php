<?php

namespace App\Services;

use App\Models\Invoice;
use Carbon\CarbonInterface;

class InvoiceNumberService
{
    public function next(?CarbonInterface $date = null): string
    {
        $date ??= now();
        $year = (int) $date->format('Y');

        if ((int) $date->format('n') < 4) {
            $startYear = $year - 1;
            $endYear = $year;
        } else {
            $startYear = $year;
            $endYear = $year + 1;
        }

        $prefix = sprintf('EBS/%02d-%02d/', $startYear % 100, $endYear % 100);
        $lastInvoice = Invoice::where('invoice_number', 'like', $prefix.'%')
            ->orderByDesc('invoice_number')
            ->first();

        $nextNumber = 1;

        if ($lastInvoice) {
            $nextNumber = ((int) substr($lastInvoice->invoice_number, -3)) + 1;
        }

        return $prefix.sprintf('%03d', $nextNumber);
    }

    public function suggestAvailableNumber(?string $preferredNumber = null): string
    {
        if ($preferredNumber && preg_match('/^(.*?)(\d+)$/', $preferredNumber, $matches)) {
            $prefix = $matches[1];
            $number = (int) $matches[2];
            $padding = max(3, strlen($matches[2]));

            do {
                $number++;
                $suggested = $prefix.str_pad((string) $number, $padding, '0', STR_PAD_LEFT);
            } while (Invoice::where('invoice_number', $suggested)->exists());

            return $suggested;
        }

        $suggested = $this->next();

        while (Invoice::where('invoice_number', $suggested)->exists()) {
            $suggested = $this->suggestAvailableNumber($suggested);
        }

        return $suggested;
    }
}
