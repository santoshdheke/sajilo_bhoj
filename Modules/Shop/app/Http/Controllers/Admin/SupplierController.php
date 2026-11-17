<?php

namespace Modules\Shop\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Modules\Shop\Models\Product;
use Modules\Shop\Models\PurchaseOrder;
use Modules\Shop\Models\PurchaseOrderItem;
use Modules\Shop\Models\Supplier;
use Modules\Shop\Support\ShopTenancy;

/** Suppliers and purchase orders (stock in), as in multiple_pasal's SupplierController. */
class SupplierController extends Controller
{
    public function index(): View
    {
        return view('shop::admin.theme_one.supplier.index', [
            'suppliers' => Supplier::withCount('purchaseOrders')->withSum('purchaseOrders as purchased', 'total')
                ->withSum('purchaseOrders as paid', 'paid_amount')->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Supplier::create($this->validated($request));

        return back()->with('success', 'Supplier added.');
    }

    public function show(Supplier $supplier): View
    {
        $supplier->load(['purchaseOrders' => fn ($q) => $q->withCount('items')->latest()]);

        return view('shop::admin.theme_one.supplier.show', ['supplier' => $supplier]);
    }

    public function update(Request $request, Supplier $supplier): RedirectResponse
    {
        $supplier->update($this->validated($request));

        return back()->with('success', 'Supplier updated.');
    }

    public function destroy(Supplier $supplier): RedirectResponse
    {
        $supplier->delete(); // its purchase orders stay (supplier link set to null)

        return redirect()->route('shop.admin.supplier.index')->with('success', 'Supplier deleted.');
    }

    public function purchases(): View
    {
        return view('shop::admin.theme_one.supplier.purchases', [
            'purchaseOrders' => PurchaseOrder::with('supplier')->withCount('items')->latest()->paginate(15),
        ]);
    }

    public function purchaseCreate(Request $request): View
    {
        return view('shop::admin.theme_one.supplier.purchase_create', [
            'suppliers'  => Supplier::orderBy('name')->get(['id', 'name']),
            'products'   => Product::orderBy('name')->get(['id', 'name', 'sku', 'cost_price', 'price', 'tax_rate', 'unit']),
            'supplierId' => $request->integer('supplier_id') ?: null,
        ]);
    }

    public function purchaseStore(Request $request): RedirectResponse
    {
        $shop = ShopTenancy::id();

        $data = $request->validate([
            'supplier_id'        => ['nullable', Rule::exists('shop.suppliers', 'id')->where('company_id', $shop)],
            'order_date'         => 'nullable|date',
            'tax_amount'         => 'nullable|numeric|min:0',
            'discount'           => 'nullable|numeric|min:0',
            'paid_amount'        => 'nullable|numeric|min:0',
            'notes'              => 'nullable|string|max:1000',
            'items'              => 'required|array|min:1',
            'items.*.product_id' => ['required', Rule::exists('shop.products', 'id')->where('company_id', $shop)],
            'items.*.quantity'   => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.tax_rate'   => 'nullable|numeric|min:0|max:100',
        ], ['items.required' => 'Add at least one product.']);

        $purchaseOrder = DB::connection('shop')->transaction(function () use ($data) {
            $subtotal = collect($data['items'])->sum(fn ($i) => $i['unit_price'] * $i['quantity']);
            $tax      = (float) ($data['tax_amount'] ?? 0);
            $discount = (float) ($data['discount'] ?? 0);
            $total    = max(0, $subtotal + $tax - $discount);
            $paid     = (float) ($data['paid_amount'] ?? 0);

            $po = PurchaseOrder::create([
                'supplier_id'    => $data['supplier_id'] ?? null,
                'po_number'      => $this->newPoNumber(),
                'subtotal'       => $subtotal,
                'tax_amount'     => $tax,
                'discount'       => $discount,
                'total'          => $total,
                'paid_amount'    => $paid,
                'status'         => 'received', // recorded on arrival: its quantities are stock in
                'payment_status' => $paid >= $total ? 'paid' : ($paid > 0 ? 'partial' : 'unpaid'),
                'order_date'     => $data['order_date'] ?? now(),
                'notes'          => $data['notes'] ?? null,
            ]);

            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $po->id,
                    'product_id'        => $item['product_id'],
                    'unit_price'        => $item['unit_price'],
                    'quantity'          => $item['quantity'],
                    'tax_rate'          => $item['tax_rate'] ?? 0,
                    'total'             => $item['unit_price'] * $item['quantity'],
                ]);
            }

            return $po;
        });

        return redirect()->route('shop.admin.supplier.purchase.show', $purchaseOrder)->with('success', 'Purchase recorded and stock added.');
    }

    public function purchaseShow(PurchaseOrder $purchaseOrder): View
    {
        $purchaseOrder->load('supplier', 'items.product');

        return view('shop::admin.theme_one.supplier.purchase_show', ['po' => $purchaseOrder]);
    }

    private function newPoNumber(): string
    {
        do {
            $number = 'PO-'.date('Ymd').'-'.random_int(1000, 9999);
        } while (PurchaseOrder::withoutGlobalScopes()->where('po_number', $number)->exists());

        return $number;
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name'         => 'required|string|max:255',
            'phone'        => 'nullable|string|max:20',
            'email'        => 'nullable|email|max:255',
            'address'      => 'nullable|string|max:255',
            'company_name' => 'nullable|string|max:255',
            'pan_vat'      => 'nullable|string|max:50',
            'notes'        => 'nullable|string|max:1000',
        ]);
    }
}
