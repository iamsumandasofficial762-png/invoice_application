<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\InvoiceCalculationService;
use App\Services\InvoiceNumberService;
use App\Services\NumberToWordsService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private array $signatures = [
        'signature-1.png' => 'Signature 1',
        'signature-2.png' => 'Signature 2',
    ];

    public function __construct(
        private readonly InvoiceCalculationService $calculationService,
        private readonly InvoiceNumberService $invoiceNumberService,
        private readonly NumberToWordsService $numberToWordsService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $invoices = Invoice::with('customer')
            ->when($search, function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereDate('invoice_date', $search)
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('gst', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search'));
    }

    public function create(): View
    {
        return view('invoices.create', [
            'customers' => Customer::orderBy('name')->get(),
            'invoiceNumber' => $this->invoiceNumberService->next(),
            'signatures' => $this->signatures,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $invoice = null;

        DB::transaction(function () use ($validated, &$invoice) {
            $calculation = $this->calculationService->calculate($validated['items']);
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $this->invoiceNumberService->next(),
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $calculation['subtotal'],
                'cgst' => $calculation['cgst'],
                'sgst' => $calculation['sgst'],
                'total_tax' => $calculation['total_tax'],
                'gross_amount' => $calculation['gross_amount'],
                'net_payable_amount' => $calculation['net_payable_amount'],
                'amount_in_words' => $this->numberToWordsService->rupees($calculation['net_payable_amount']),
                'signature_image' => $validated['signature_image'],
            ]);

            $invoice->items()->createMany($calculation['items']);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.edit', [
            'invoice' => $invoice,
            'customers' => Customer::orderBy('name')->get(),
            'signatures' => $this->signatures,
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $this->validatedData($request);

        DB::transaction(function () use ($validated, $invoice) {
            $calculation = $this->calculationService->calculate($validated['items']);
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $calculation['subtotal'],
                'cgst' => $calculation['cgst'],
                'sgst' => $calculation['sgst'],
                'total_tax' => $calculation['total_tax'],
                'gross_amount' => $calculation['gross_amount'],
                'net_payable_amount' => $calculation['net_payable_amount'],
                'amount_in_words' => $this->numberToWordsService->rupees($calculation['net_payable_amount']),
                'signature_image' => $validated['signature_image'],
            ]);

            $invoice->items()->delete();
            $invoice->items()->createMany($calculation['items']);
        });

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice updated successfully.');
    }

    public function destroy(Invoice $invoice): RedirectResponse
    {
        $invoice->delete();

        return redirect()->route('invoices.index')->with('success', 'Invoice deleted successfully.');
    }

    public function downloadPdf(Invoice $invoice)
    {
        $invoice->load(['customer', 'items']);

        return Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4')
            ->download(str_replace('/', '-', $invoice->invoice_number).'.pdf');
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.print', compact('invoice'));
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_date' => ['required', 'date'],
            'signature_image' => ['required', Rule::in(array_keys($this->signatures))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.sac_code' => ['required', 'string', 'max:191'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);
    }
}
