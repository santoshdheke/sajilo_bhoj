<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Modules\Shop\Models\Customer;
use Modules\Shop\Models\CustomerTransaction;
use Modules\Shop\Models\Order;
use Modules\Shop\Support\Money;

/** Customers and their credit (udhar) account: credit sales, payments received. */
class CustomerController extends Controller
{
    private const FIELDS = ['first_name', 'last_name', 'email', 'phone_number', 'address_one', 'address_two', 'notes'];

    public function index(Request $request): View
    {
        $customers = Customer::withCount('orders')
            ->when($request->search, fn ($q, $s) => $q->where(fn ($q) => $q->where('first_name', 'like', "%{$s}%")
                ->orWhere('last_name', 'like', "%{$s}%")->orWhere('phone_number', 'like', "%{$s}%")))
            ->orderBy('first_name')->paginate($request->integer('items', 15) ?: 15)->withQueryString();

        return view('shop::admin.theme_one.customer.index', ['customers' => $customers]);
    }

    public function store(Request $request): RedirectResponse
    {
        Customer::create($this->validated($request));

        return back()->with('success', 'Customer added.');
    }

    public function show(Customer $customer): View
    {
        return view('shop::admin.theme_one.customer.show', [
            'customer'     => $customer,
            'transactions' => $customer->transactions()->latest()->paginate(20),
            'orders'       => Order::where('customer_id', $customer->id)
                ->orWhere(fn ($q) => $customer->phone_number ? $q->where('phone_number', $customer->phone_number) : $q->whereRaw('0 = 1'))
                ->latest()->limit(10)->get(),
        ]);
    }

    public function update(Request $request, Customer $customer): RedirectResponse
    {
        $customer->update($this->validated($request));

        return redirect()->route('shop.admin.customer.show', $customer)->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer): RedirectResponse
    {
        if ((float) $customer->balance < 0) {
            return back()->with('error', "{$customer->full_name} still owes ".Money::format($customer->udhar_amount).'. Settle the credit before deleting.');
        }

        $customer->delete(); // their orders stay, without the customer link

        return redirect()->route('shop.admin.customer.index')->with('success', 'Customer deleted.');
    }

    /** Add a customer from the POS screen without leaving it. */
    public function quickStore(Request $request): JsonResponse
    {
        $data = $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'phone_number' => 'nullable|string|max:20',
            'address_one'  => 'nullable|string|max:255',
        ]);

        $customer = Customer::create($data);

        return response()->json([
            'id'      => $customer->id,
            'name'    => $customer->full_name,
            'phone'   => $customer->phone_number ?? '',
            'address' => $customer->address_one ?? '',
            'balance' => (float) $customer->balance,
        ]);
    }

    /** Customers who owe the shop money. */
    public function credit(): View
    {
        $customers = Customer::where('balance', '<', 0)->orderBy('balance')->get();

        return view('shop::admin.theme_one.customer.credit', [
            'customers'   => $customers,
            'totalCredit' => $customers->sum('udhar_amount'),
        ]);
    }

    public function receivePayment(Request $request, Customer $customer): RedirectResponse
    {
        $data = $request->validate([
            'amount'      => 'required|numeric|min:0.01',
            'description' => 'nullable|string|max:255',
            'reference'   => 'nullable|string|max:100',
        ]);

        DB::connection('shop')->transaction(function () use ($customer, $data) {
            $customer = Customer::lockForUpdate()->find($customer->id);
            $customer->balance = (float) $customer->balance + (float) $data['amount'];
            $customer->save();

            CustomerTransaction::create([
                'customer_id'   => $customer->id,
                'type'          => 'payment',
                'amount'        => $data['amount'],
                'balance_after' => $customer->balance,
                'description'   => ($data['description'] ?? null) ?: 'Payment received',
                'reference'     => $data['reference'] ?? null,
            ]);
        });

        return back()->with('success', 'Payment of '.Money::format($data['amount']).' recorded.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'first_name'   => 'required|string|max:100',
            'last_name'    => 'nullable|string|max:100',
            'email'        => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:20',
            'address_one'  => 'nullable|string|max:255',
            'address_two'  => 'nullable|string|max:255',
            'notes'        => 'nullable|string|max:1000',
        ]);
    }
}
