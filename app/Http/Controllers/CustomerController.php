<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CustomerController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->string('search')->toString();

        $customers = Customer::query()
            ->when($search, function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('gst', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('customers.index', compact('customers', 'search'));
    }

    public function create(): View
    {
        return view('customers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->validatedData($request));

        return redirect()->route('customers.index')->with('success', 'Customer created successfully.');
    }

    public function show(Customer $customer): View
    {
        $customer->load('invoices');

        return view('customers.show', compact('customer'));
    }

    public function edit(Customer $customer): View
    {
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validatedData($request));

        return redirect()->route('customers.show', $customer)->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Customer deleted successfully.');
    }

    public function ajaxStore(Request $request): JsonResponse
    {
        $customer = Customer::create($this->validatedData($request));

        return response()->json([
            'message' => 'Customer created successfully.',
            'customer' => $customer,
        ], 201);
    }

    public function liveSearch(Request $request): JsonResponse
    {
        $search = trim($request->string('search')->toString());

        $customers = Customer::query()
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('gst', 'like', "%{$search}%")
                        ->orWhere('state', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%")
                        ->orWhere('gmail', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->get();

        return response()->json([
            'customers' => $customers->map(fn (Customer $customer) => [
                'id' => $customer->id,
                'name' => $customer->name,
                'gst' => $customer->gst,
                'state' => $customer->state,
                'phone' => $customer->phone,
                'gmail' => $customer->gmail,
                'show_url' => route('customers.show', $customer),
                'edit_url' => route('customers.edit', $customer),
                'delete_url' => route('customers.destroy', $customer),
            ])->values(),
            'count' => $customers->count(),
        ]);
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:191'],
            'address' => ['required', 'string'],
            'state' => ['required', 'string', 'max:191'],
            'pin' => ['required', 'string', 'max:20'],
            'phone' => ['nullable', 'string', 'max:30'],
            'gmail' => ['nullable', 'email', 'max:191'],
            'gst' => ['required', 'string', 'max:191'],
        ]);
    }
}
