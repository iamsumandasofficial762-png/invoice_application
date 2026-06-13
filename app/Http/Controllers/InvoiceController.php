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
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class InvoiceController extends Controller
{
    private array $signatures = [
        'signature-1.png' => 'Victor bhattacharjee',
        'signature-2.png' => 'Subhajit sen',
    ];

    public function __construct(
        private readonly InvoiceCalculationService $calculationService,
        private readonly InvoiceNumberService $invoiceNumberService,
        private readonly NumberToWordsService $numberToWordsService,
    ) {}

    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();
        $perPage = $this->perPage($request);

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
            ->paginate($perPage)
            ->withQueryString();

        return view('invoices.index', compact('invoices', 'search', 'perPage'));
    }

    public function create(Request $request): View
    {
        $selectedCustomerId = $request->old('customer_id');

        return view('invoices.create', [
            'customers' => $selectedCustomerId ? Customer::whereKey($selectedCustomerId)->get() : collect(),
            'invoiceNumber' => $this->invoiceNumberService->next(),
            'signatures' => $this->signatures,
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $this->ensureUniqueInvoiceNumber($validated['invoice_number']);
        $invoice = null;

        DB::transaction(function () use ($validated, &$invoice) {
            $calculation = $this->calculationService->calculate($validated['items']);
            $invoice = Invoice::create([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
                'invoice_date' => $validated['invoice_date'],
                'subtotal' => $calculation['subtotal'],
                'cgst' => $calculation['cgst'],
                'sgst' => $calculation['sgst'],
                'total_tax' => $calculation['total_tax'],
                'gross_amount' => $calculation['gross_amount'],
                'net_payable_amount' => $calculation['net_payable_amount'],
                'amount_in_words' => $this->numberToWordsService->rupees($calculation['net_payable_amount']),
                'signature_image' => $validated['signature_image'] ?? 'signature-1.png',
            ]);

            $invoice->items()->createMany($calculation['items']);
        });

        if ($request->input('action') === 'save_print') {
            return redirect()->route('invoices.print', $invoice)->with('success', 'Invoice created successfully.');
        }

        return redirect()->route('invoices.show', $invoice)->with('success', 'Invoice created successfully.');
    }

    public function show(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.show', compact('invoice'));
    }

    public function edit(Request $request, Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);
        $selectedCustomerId = $request->old('customer_id', $invoice->customer_id);

        return view('invoices.edit', [
            'invoice' => $invoice,
            'customers' => Customer::whereKey($selectedCustomerId)->get(),
            'signatures' => $this->signatures,
        ]);
    }

    public function update(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $this->validatedData($request);
        $this->ensureUniqueInvoiceNumber($validated['invoice_number'], $invoice->id);

        DB::transaction(function () use ($validated, $invoice) {
            $calculation = $this->calculationService->calculate($validated['items']);
            $invoice->update([
                'customer_id' => $validated['customer_id'],
                'invoice_number' => $validated['invoice_number'],
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
        $fileName = preg_replace('/[^A-Za-z0-9\-]/', '-', $invoice->invoice_number).'.pdf';

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'))
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
            ]);

        return $pdf->download($fileName);
    }

    public function print(Invoice $invoice): View
    {
        $invoice->load(['customer', 'items']);

        return view('invoices.print', compact('invoice'));
    }

    public function image(Invoice $invoice): RedirectResponse
    {
        return redirect()->route('invoices.pdf', $invoice);
    }

    public function updateStatus(Request $request, Invoice $invoice): RedirectResponse
    {
        $validated = $request->validate([
            'payment_status' => ['required', Rule::in(['paid', 'unpaid'])],
        ]);

        $invoice->update([
            'payment_status' => $validated['payment_status'],
        ]);

        return redirect()
            ->route('invoices.show', $invoice)
            ->with('success', 'Invoice status updated successfully.');
    }

    public function checkNumber(Request $request)
    {
        $invoiceNumber = trim($request->string('invoice_number')->toString());
        $ignoreId = $request->integer('invoice_id') ?: null;

        if ($invoiceNumber === '') {
            return response()->json([
                'exists' => false,
                'message' => '',
                'suggested' => null,
            ]);
        }

        $exists = $this->invoiceNumberExists($invoiceNumber, $ignoreId);

        return response()->json([
            'exists' => $exists,
            'message' => $exists ? 'This invoice number is already used.' : '',
            'suggested' => $exists ? $this->invoiceNumberService->suggestAvailableNumber($invoiceNumber) : null,
        ]);
    }

    public function liveSearch(Request $request)
    {
        $search = trim($request->string('search')->toString());
        $perPage = $this->perPage($request);

        $invoices = Invoice::with('customer')
            ->when($search !== '', function ($query) use ($search) {
                $query->where('invoice_number', 'like', "%{$search}%")
                    ->orWhereDate('invoice_date', $search)
                    ->orWhere('payment_status', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%")
                            ->orWhere('gst', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate($perPage)
            ->withQueryString();

        return response()->json([
            'invoices' => $invoices->getCollection()->map(fn (Invoice $invoice) => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'invoice_date' => $invoice->invoice_date->format('d-m-Y'),
                'customer_name' => $invoice->customer->name,
                'customer_gst' => $invoice->customer->gst,
                'net_payable_amount' => number_format((float) $invoice->net_payable_amount, 0),
                'payment_status' => $invoice->payment_status ?? 'unpaid',
                'show_url' => route('invoices.show', $invoice),
                'edit_url' => route('invoices.edit', $invoice),
                'pdf_url' => route('invoices.pdf', $invoice),
                'print_url' => route('invoices.print', $invoice),
                'delete_url' => route('invoices.destroy', $invoice),
                'share_url' => route('invoices.pdf', $invoice),
                'image_url' => route('invoices.image', $invoice),
            ])->values(),
            'count' => $invoices->total(),
        ]);
    }

    private function validatedData(Request $request): array
    {
        $validated = $request->validate([
            'customer_id' => ['required', 'exists:customers,id'],
            'invoice_number' => ['required', 'string', 'max:100'],
            'invoice_date' => ['required', 'date'],
            'signature_image' => ['required', Rule::in(array_keys($this->signatures))],
            'items' => ['required', 'array', 'min:1'],
            'items.*.description' => ['required', 'string'],
            'items.*.sac_code' => ['nullable', 'string', 'max:191'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        $validated['items'] = collect($validated['items'])
            ->map(function (array $item) {
                $item['sac_code'] = trim((string) ($item['sac_code'] ?? '')) ?: '9983';

                return $item;
            })
            ->all();

        return $validated;
    }

    private function ensureUniqueInvoiceNumber(string $invoiceNumber, ?int $ignoreId = null): void
    {
        if (! $this->invoiceNumberExists($invoiceNumber, $ignoreId)) {
            return;
        }

        $suggested = $this->invoiceNumberService->suggestAvailableNumber($invoiceNumber);

        throw ValidationException::withMessages([
            'invoice_number' => 'This invoice number is already used. Please use a different invoice number. Suggested invoice number: '.$suggested,
        ]);
    }

    private function invoiceNumberExists(string $invoiceNumber, ?int $ignoreId = null): bool
    {
        return Invoice::where('invoice_number', $invoiceNumber)
            ->when($ignoreId, fn ($query) => $query->whereKeyNot($ignoreId))
            ->exists();
    }

    private function perPage(Request $request): int
    {
        $perPage = (int) $request->query('per_page', 10);

        return in_array($perPage, [10, 25, 50, 100], true) ? $perPage : 10;
    }
}
