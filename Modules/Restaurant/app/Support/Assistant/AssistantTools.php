<?php

namespace Modules\Restaurant\Support\Assistant;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Modules\Restaurant\Models\Company;
use Modules\Restaurant\Models\Customer;
use Modules\Restaurant\Models\EarningTransaction;
use Modules\Restaurant\Models\FloorTable;
use Modules\Restaurant\Models\InventoryItem;
use Modules\Restaurant\Models\MenuItem;
use Modules\Restaurant\Models\Order;
use Modules\Restaurant\Models\OrderItem;
use Modules\Restaurant\Models\PurchaseOrder;
use Modules\Restaurant\Models\Role;
use Modules\Restaurant\Models\Supplier;
use Modules\Restaurant\Models\TableBooking;
use Modules\Restaurant\Models\User;
use Modules\Restaurant\Models\WalletTransaction;
use Modules\Restaurant\Models\WithdrawalRequest;
use Modules\Restaurant\Support\Billing;
use Modules\Restaurant\Support\Tenancy;

/**
 * Read-only look-ups the chat assistant can run on the signed-in restaurant's data.
 *
 * Safety rules for every tool:
 *  - Only fixed queries, never SQL from the model, and nothing that changes data.
 *  - Only the current restaurant: models scope themselves to the tenant (company / branch);
 *    models without their own scope (menu items, inventory items, bookings) are reached only
 *    through their scoped parent (category / table).
 *  - Only what the person may open: each tool needs the same permission as its admin page.
 *  - Small results (at most 25 rows).
 */
class AssistantTools
{
    private const MAX_ROWS = 25;

    private const PERIODS = ['today', 'yesterday', 'last_7_days', 'this_week', 'this_month', 'last_month', 'this_year', 'all_time'];

    public function __construct(private User $user) {}

    /** Tool definitions for the API — only the tools this person is allowed to use. */
    public function definitions(): array
    {
        $period = ['type' => 'string', 'enum' => self::PERIODS, 'description' => 'Time range. Defaults to today.'];

        $all = [
            'sales_summary' => ['dashboard.view', 'Revenue and order counts for a time range: revenue from served orders, number of orders by status and by type (dine-in / takeaway / delivery), average bill and new customers. Use for any question about sales, revenue, income or how business is going.', [
                'period' => $period,
            ]],
            'top_items' => ['dashboard.view', 'Best-selling menu items (from served orders) for a time range, with quantity sold and revenue.', [
                'period' => $period,
                'limit'  => ['type' => 'integer', 'description' => 'How many items (1-25). Default 5.'],
            ]],
            'list_orders' => ['orders.manage', 'Recent orders, newest first, optionally filtered. Returns order number, time, type, table, customer, status and total.', [
                'status'     => ['type' => 'string', 'enum' => ['pending', 'confirmed', 'preparing', 'ready', 'served', 'cancelled']],
                'order_type' => ['type' => 'string', 'enum' => ['dine_in', 'takeaway', 'delivery']],
                'period'     => $period,
                'limit'      => ['type' => 'integer', 'description' => '1-25. Default 10.'],
            ]],
            'order_details' => ['orders.manage', 'One order with its items, quantities, prices, notes, discount, tax and total.', [
                'order_no' => ['type' => 'string', 'description' => 'The order number, e.g. ORD-20261125-0001'],
            ], ['order_no']],
            'kitchen_queue' => ['kitchen.manage', 'Orders still in the kitchen (pending, confirmed, preparing, ready) with their items and how long ago they came in.', []],
            'tables_status' => ['tables.manage', 'Every table with its room, capacity and status (available / occupied / reserved / cleaning), plus counts per status.', []],
            'list_bookings' => ['tables.manage', 'Table bookings for a date (default today), with time, name, phone, party size, table and status.', [
                'date'   => ['type' => 'string', 'description' => 'YYYY-MM-DD. Default today.'],
                'status' => ['type' => 'string', 'enum' => ['pending', 'confirmed', 'cancelled']],
            ]],
            'search_menu' => ['menu.manage', 'Menu items with category, price, preparation time and availability. Search by name or category, or list items that are switched off.', [
                'search'           => ['type' => 'string', 'description' => 'Part of an item or category name.'],
                'only_unavailable' => ['type' => 'boolean', 'description' => 'Only items that are switched off.'],
            ]],
            'inventory_status' => ['inventory.manage', 'Stock items with quantity, unit, reorder level and cost price. Can list only low-stock items (quantity at or below reorder level).', [
                'search'         => ['type' => 'string'],
                'low_stock_only' => ['type' => 'boolean'],
            ]],
            'purchase_orders' => ['purchase_orders.manage', 'Recent purchase orders with supplier, invoice number, status and total.', [
                'status' => ['type' => 'string', 'enum' => ['pending', 'ordered', 'received', 'cancelled']],
            ]],
            'list_suppliers' => ['suppliers.manage', 'Suppliers with phone, email and address.', [
                'search' => ['type' => 'string'],
            ]],
            'customers' => ['customers.manage', 'Registered customers with phone, email, loyalty points, number of orders and total spent. Search by name/phone/email or sort.', [
                'search' => ['type' => 'string'],
                'sort'   => ['type' => 'string', 'enum' => ['newest', 'loyalty_points', 'top_spenders'], 'description' => 'Default newest.'],
            ]],
            'staff_and_roles' => ['users.manage', 'Staff members (name, email, role, active or not) and roles with the sections each role can open.', []],
            'subscription_and_balance' => ['users.manage', "The restaurant's SajiloBhoj plan, subscription status, trial days left, prepaid balance, how long it lasts, recent top-ups/charges/bonuses, and Refer & Earn clients, available earnings, total earned and withdrawal requests.", []],
        ];

        $tools = [];
        foreach ($all as $name => $def) {
            [$permission, $description, $properties] = $def;
            if (! $this->user->hasPermission($permission)) {
                continue;
            }
            $tools[] = [
                'name'        => $name,
                'description' => $description,
                'inputSchema' => [
                    'type'       => 'object',
                    'properties' => (object) $properties,
                    'required'   => $def[3] ?? [],
                ],
            ];
        }

        return $tools;
    }

    /** Run one tool; returns JSON text for the tool_result. Throws on unknown or forbidden tools. */
    public function run(string $name, array $input): string
    {
        $allowed = array_column($this->definitions(), 'name');
        if (! in_array($name, $allowed, true)) {
            throw new \InvalidArgumentException("The tool {$name} is not available to this user.");
        }

        $method = lcfirst(str_replace('_', '', ucwords($name, '_')));

        return json_encode($this->{$method}($input), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    // ───────────────────────── tools ─────────────────────────

    private function salesSummary(array $in): array
    {
        [$from, $to, $label] = $this->period($in['period'] ?? 'today');
        $orders = Order::whereBetween('created_at', [$from, $to]);
        $served = (clone $orders)->where('status', 'served');

        return [
            'period'           => $label,
            'revenue_served'   => $this->money((clone $served)->sum('grand_total')),
            'orders_total'     => (clone $orders)->count(),
            'orders_by_status' => (clone $orders)->select('status', DB::raw('COUNT(*) AS n'))->groupBy('status')->pluck('n', 'status'),
            'orders_by_type'   => (clone $orders)->select('order_type', DB::raw('COUNT(*) AS n'))->groupBy('order_type')->pluck('n', 'order_type'),
            'average_bill'     => $this->money((clone $served)->avg('grand_total') ?? 0),
            'discounts_given'  => $this->money((clone $served)->sum('discount')),
            'new_customers'    => Customer::whereBetween('created_at', [$from, $to])->count(),
        ];
    }

    private function topItems(array $in): array
    {
        [$from, $to, $label] = $this->period($in['period'] ?? 'today');

        $items = OrderItem::select('name', DB::raw('SUM(quantity) AS qty'), DB::raw('SUM(subtotal) AS revenue'))
            ->whereHas('order', fn ($q) => $q->where('status', 'served')->whereBetween('created_at', [$from, $to]))
            ->groupBy('name')->orderByDesc('qty')->limit($this->limit($in, 5))->get()
            ->map(fn ($i) => ['item' => $i->name, 'quantity_sold' => (int) $i->qty, 'revenue' => $this->money($i->revenue)]);

        return ['period' => $label, 'items' => $items];
    }

    private function listOrders(array $in): array
    {
        $q = Order::with('table')->latest('id');
        if (! empty($in['status'])) {
            $q->where('status', $in['status']);
        }
        if (! empty($in['order_type'])) {
            $q->where('order_type', $in['order_type']);
        }
        if (! empty($in['period'])) {
            [$from, $to] = $this->period($in['period']);
            $q->whereBetween('created_at', [$from, $to]);
        }

        return ['orders' => $q->limit($this->limit($in, 10))->get()->map(fn (Order $o) => [
            'order_no' => $o->order_no,
            'time'     => $o->created_at?->format('d M Y h:i A'),
            'type'     => $o->order_type,
            'table'    => $o->table?->table_no,
            'customer' => $o->customer_name,
            'status'   => $o->status,
            'total'    => $this->money($o->grand_total),
        ])];
    }

    private function orderDetails(array $in): array
    {
        $order = Order::with(['items', 'table'])->where('order_no', trim((string) ($in['order_no'] ?? '')))->first();
        if (! $order) {
            return ['error' => 'No order with that number.'];
        }

        return [
            'order_no' => $order->order_no,
            'time'     => $order->created_at?->format('d M Y h:i A'),
            'type'     => $order->order_type,
            'table'    => $order->table?->table_no,
            'customer' => $order->customer_name,
            'status'   => $order->status,
            'notes'    => $order->notes,
            'items'    => $order->items->map(fn ($i) => ['name' => $i->name, 'quantity' => $i->quantity, 'price' => $this->money($i->price), 'subtotal' => $this->money($i->subtotal), 'note' => $i->note]),
            'subtotal' => $this->money($order->subtotal),
            'discount' => $this->money($order->discount),
            'tax'      => $this->money($order->tax),
            'total'    => $this->money($order->grand_total),
        ];
    }

    private function kitchenQueue(array $in): array
    {
        $orders = Order::with(['items', 'table'])->whereIn('status', ['pending', 'confirmed', 'preparing', 'ready'])
            ->oldest('id')->limit(self::MAX_ROWS)->get();

        return [
            'count'  => $orders->count(),
            'orders' => $orders->map(fn (Order $o) => [
                'order_no' => $o->order_no,
                'status'   => $o->status,
                'type'     => $o->order_type,
                'table'    => $o->table?->table_no,
                'waiting'  => $o->created_at?->diffForHumans(),
                'items'    => $o->items->map(fn ($i) => $i->quantity . ' × ' . $i->name . ($i->note ? " ({$i->note})" : ''))->all(),
            ]),
        ];
    }

    private function tablesStatus(array $in): array
    {
        $tables = FloorTable::with('room')->orderBy('table_no')->limit(100)->get();

        return [
            'counts' => $tables->countBy('status'),
            'tables' => $tables->map(fn ($t) => ['table' => $t->table_no, 'room' => $t->room?->name, 'capacity' => $t->capacity, 'status' => $t->status])->values(),
        ];
    }

    private function listBookings(array $in): array
    {
        $date = $this->date($in['date'] ?? null) ?? today();
        // Bookings have no tenant scope of their own: only through this restaurant's tables.
        $q = TableBooking::with('floorTable')->whereIn('table_id', FloorTable::select('id'))
            ->whereDate('booking_date', $date)->orderBy('booking_time');
        if (! empty($in['status'])) {
            $q->where('status', $in['status']);
        }

        return ['date' => $date->format('d M Y'), 'bookings' => $q->limit(self::MAX_ROWS)->get()->map(fn ($b) => [
            'time' => $b->time_formatted, 'name' => $b->name, 'phone' => $b->phone, 'party_size' => $b->party_size,
            'table' => $b->floorTable?->table_no, 'status' => $b->status, 'notes' => $b->notes,
        ])];
    }

    private function searchMenu(array $in): array
    {
        // Menu items have no tenant scope of their own: only through this restaurant's categories.
        $q = MenuItem::with('category')->whereHas('category')->orderBy('name');
        if ($s = trim((string) ($in['search'] ?? ''))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$s}%")));
        }
        if (! empty($in['only_unavailable'])) {
            $q->where('is_available', false);
        }

        return [
            'total_items' => MenuItem::whereHas('category')->count(),
            'items'       => $q->limit(self::MAX_ROWS)->get()->map(fn ($i) => [
                'name' => $i->name, 'category' => $i->category?->name, 'price' => $this->money($i->price),
                'preparation_minutes' => $i->preparation_time, 'available' => (bool) $i->is_available,
            ]),
        ];
    }

    private function inventoryStatus(array $in): array
    {
        // Inventory items have no tenant scope of their own: only through this restaurant's categories.
        $q = InventoryItem::with('category')->whereHas('category')->orderBy('name');
        if ($s = trim((string) ($in['search'] ?? ''))) {
            $q->where('name', 'like', "%{$s}%");
        }
        if (! empty($in['low_stock_only'])) {
            $q->whereColumn('quantity', '<=', 'reorder_level');
        }

        return [
            'low_stock_count' => InventoryItem::whereHas('category')->whereColumn('quantity', '<=', 'reorder_level')->count(),
            'items'           => $q->limit(self::MAX_ROWS)->get()->map(fn ($i) => [
                'name' => $i->name, 'category' => $i->category?->name, 'quantity' => (float) $i->quantity, 'unit' => $i->unit,
                'reorder_level' => (float) $i->reorder_level, 'cost_price' => $this->money($i->cost_price), 'low_stock' => $i->is_low_stock,
            ]),
        ];
    }

    private function purchaseOrders(array $in): array
    {
        $q = PurchaseOrder::with('supplier')->latest('id');
        if (! empty($in['status'])) {
            $q->where('status', $in['status']);
        }

        return ['purchase_orders' => $q->limit(self::MAX_ROWS)->get()->map(fn ($p) => [
            'invoice_no' => $p->invoice_no, 'supplier' => $p->supplier?->name, 'status' => $p->status,
            'total' => $this->money($p->total_amount), 'date' => $p->created_at?->format('d M Y'),
        ])];
    }

    private function listSuppliers(array $in): array
    {
        $q = Supplier::orderBy('name');
        if ($s = trim((string) ($in['search'] ?? ''))) {
            $q->where('name', 'like', "%{$s}%");
        }

        return ['suppliers' => $q->limit(self::MAX_ROWS)->get(['name', 'phone', 'email', 'address'])];
    }

    private function customers(array $in): array
    {
        $q = Customer::withCount('orders')->withSum(['orders as spent' => fn ($o) => $o->where('status', 'served')], 'grand_total');
        if ($s = trim((string) ($in['search'] ?? ''))) {
            $q->where(fn ($w) => $w->where('name', 'like', "%{$s}%")->orWhere('phone', 'like', "%{$s}%")->orWhere('email', 'like', "%{$s}%"));
        }
        match ($in['sort'] ?? 'newest') {
            'loyalty_points' => $q->orderByDesc('loyalty_points'),
            'top_spenders'   => $q->orderByDesc('spent'),
            default          => $q->latest('id'),
        };

        return [
            'total_customers' => Customer::count(),
            'customers'       => $q->limit(self::MAX_ROWS)->get()->map(fn ($c) => [
                'name' => $c->name, 'phone' => $c->phone, 'email' => $c->email, 'loyalty_points' => $c->loyalty_points,
                'orders' => $c->orders_count, 'spent' => $this->money($c->spent ?? 0), 'joined' => $c->created_at?->format('d M Y'),
            ]),
        ];
    }

    private function staffAndRoles(array $in): array
    {
        $labels = Role::PERMISSIONS;

        return [
            'staff' => User::with('role')->where('company_id', $this->user->company_id)->orderBy('name')->limit(50)->get()
                ->map(fn ($u) => ['name' => $u->name, 'email' => $u->email, 'role' => $u->role?->name, 'active' => (bool) $u->is_active]),
            'roles' => Role::orderBy('name')->get()->map(fn ($r) => [
                'role'     => $r->name,
                'sections' => $r->isOwner() ? ['Everything'] : collect($r->permissions ?? [])->map(fn ($p) => $labels[$p] ?? $p)->values(),
            ]),
        ];
    }

    private function subscriptionAndBalance(array $in): array
    {
        $sub     = Billing::settle(Tenancy::company());
        $company = Company::find(Tenancy::company()->id); // fresh balance after settling
        $plan    = $sub?->plan;
        $runway  = Billing::runwayDays($company, $plan);

        return [
            'plan'               => $plan?->name,
            'monthly_price'      => $plan ? $this->money($plan->monthly_price) : null,
            'per_day'            => $plan ? $this->money($plan->hourlyRate() * 24) : null,
            'status'             => $sub ? ($sub->isTrialing() ? 'free trial' : $sub->status) : 'no plan chosen',
            'trial_days_left'    => $sub?->isTrialing() ? $sub->trialDaysLeft() : null,
            'balance'            => $this->money($company->balance),
            'credit_limit'       => $this->money(Billing::creditLimit()),
            'balance_lasts_days' => $runway !== null ? round($runway, 1) : null,
            'recent_history'     => WalletTransaction::latest('id')->limit(8)->get()->map(fn ($t) => [
                'type' => $t->type, 'amount' => $this->money($t->amount), 'note' => $t->note, 'date' => $t->created_at?->format('d M Y h:i A'),
            ]),
            'refer_and_earn'     => [
                'bonus_percent'      => Billing::referralPercent(),
                'clients'            => Company::where('referred_by_company_id', $company->id)->count(),
                'available_earnings' => $this->money($company->referral_earnings),
                'total_earned'       => $this->money(EarningTransaction::where('type', 'bonus')->sum('amount')),
                'withdrawals'        => WithdrawalRequest::latest('id')->limit(5)->get()->map(fn ($w) => [
                    'amount' => $this->money($w->amount), 'to' => $w->destination(), 'status' => $w->status,
                    'reference' => $w->payout_reference, 'note' => $w->admin_note, 'date' => $w->created_at?->format('d M Y'),
                ]),
            ],
        ];
    }

    // ───────────────────────── helpers ─────────────────────────

    /** @return array{0: Carbon, 1: Carbon, 2: string} */
    private function period(string $period): array
    {
        $now = now();

        return match ($period) {
            'yesterday'   => [$now->copy()->subDay()->startOfDay(), $now->copy()->subDay()->endOfDay(), 'yesterday'],
            'last_7_days' => [$now->copy()->subDays(6)->startOfDay(), $now->copy()->endOfDay(), 'last 7 days'],
            'this_week'   => [$now->copy()->startOfWeek(), $now->copy()->endOfDay(), 'this week'],
            'this_month'  => [$now->copy()->startOfMonth(), $now->copy()->endOfDay(), $now->format('F Y')],
            'last_month'  => [$now->copy()->subMonthNoOverflow()->startOfMonth(), $now->copy()->subMonthNoOverflow()->endOfMonth(), $now->copy()->subMonthNoOverflow()->format('F Y')],
            'this_year'   => [$now->copy()->startOfYear(), $now->copy()->endOfDay(), $now->format('Y')],
            'all_time'    => [Carbon::create(2000), $now->copy()->endOfDay(), 'all time'],
            default       => [$now->copy()->startOfDay(), $now->copy()->endOfDay(), 'today'],
        };
    }

    private function date(?string $value): ?Carbon
    {
        try {
            return $value ? Carbon::createFromFormat('Y-m-d', $value)->startOfDay() : null;
        } catch (\Throwable) {
            return null;
        }
    }

    private function limit(array $in, int $default): int
    {
        return max(1, min(self::MAX_ROWS, (int) ($in['limit'] ?? $default)));
    }

    private function money(mixed $amount): string
    {
        return 'Nrs ' . number_format((float) $amount, 2);
    }
}
