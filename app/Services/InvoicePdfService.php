<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Throwable;

class InvoicePdfService
{
    public function generate(Invoice $invoice): Invoice
    {
        $invoice->loadMissing(['customer', 'items']);

        $path = $this->directory($invoice).'/'.$this->fileName($invoice);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        Storage::disk('public')->put($path, $pdf->output());

        $invoice->forceFill(['pdf_path' => $path])->saveQuietly();

        return $invoice;
    }

    public function deleteOldPdf(?string $path): void
    {
        if (! $path) {
            return;
        }

        try {
            $disk = Storage::disk('public');

            if ($disk->exists($path)) {
                $disk->delete($path);
            }
        } catch (Throwable) {
            // A missing or inaccessible old PDF must not block invoice changes.
        }
    }

    public function fileName(Invoice $invoice): string
    {
        $fileName = preg_replace('/[^A-Za-z0-9\-]+/', '-', $invoice->invoice_number);
        $fileName = trim((string) $fileName, '-');

        return ($fileName ?: 'invoice-'.$invoice->id).'.pdf';
    }

    public function directory(Invoice $invoice): string
    {
        return 'invoice/'.$invoice->invoice_date->format('Y/m');
    }
}
