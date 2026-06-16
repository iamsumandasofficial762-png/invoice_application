<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('invoices')
            ->join('customers', 'customers.id', '=', 'invoices.customer_id')
            ->select('invoices.id', 'invoices.total_tax', 'customers.state')
            ->orderBy('invoices.id')
            ->get()
            ->each(function ($invoice): void {
                $isIntraState = trim((string) $invoice->state) === 'West Bengal';
                $values = [
                    'tax_type' => $isIntraState ? 'intra_state' : 'inter_state',
                    'igst' => $isIntraState ? 0 : $invoice->total_tax,
                ];

                if (! $isIntraState) {
                    $values['cgst'] = 0;
                    $values['sgst'] = 0;
                }

                DB::table('invoices')->where('id', $invoice->id)->update($values);
            });
    }

    public function down(): void
    {
        DB::table('invoices')->update([
            'tax_type' => 'intra_state',
            'igst' => 0,
        ]);
    }
};
