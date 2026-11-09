<?php

namespace Database\Seeders;

use App\Models\Branch;
use App\Models\Chair;
use App\Models\Company;
use App\Models\Customer;
use App\Models\Domain;
use App\Models\FloorTable;
use App\Models\InventoryCategory;
use App\Models\InventoryItem;
use App\Models\KitchenColumn;
use App\Models\KitchenTicket;
use App\Models\MenuCategory;
use App\Models\MenuItem;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Role;
use App\Models\Room;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\TableBooking;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed two demo restaurant tenants end to end so multi-tenant scoping,
     * role-gated admin nav, and the public storefront all have real data to show.
     *
     * To browse a tenant locally, map its domain to 127.0.0.1 in /etc/hosts, e.g.:
     *   127.0.0.1  spice-route.test
     *   127.0.0.1  momo-house.test
     */
    public function run(): void
    {
        $this->call(PlanSeeder::class);

        $this->seedCompany(
            name: 'Spice Route Kitchen',
            domain: 'spice-route.test',
            menu: [
                'Starters' => [
                    ['Chicken Momo', 'Steamed dumplings filled with minced chicken & herbs', 6.50],
                    ['Veg Spring Rolls', 'Crispy rolls stuffed with seasonal vegetables', 5.00],
                    ['Chili Chicken', 'Wok-tossed chicken in a spicy garlic-chili sauce', 8.00],
                ],
                'Mains' => [
                    ['Chicken Biryani', 'Fragrant basmati rice layered with spiced chicken', 11.50],
                    ['Veg Thali', 'Lentils, vegetables, rice, and bread on one platter', 9.00],
                    ['Grilled Burger', 'Char-grilled beef patty with cheese & house sauce', 10.00],
                    ['Margherita Pizza', 'Wood-fired pizza with tomato, mozzarella & basil', 12.00],
                ],
                'Drinks' => [
                    ['Cold Coffee', 'Chilled coffee blended with milk & ice cream', 4.00],
                    ['Masala Tea', 'Spiced milk tea brewed the traditional way', 2.50],
                    ['Fresh Lime Soda', 'Sparkling soda with fresh lime & mint', 3.00],
                ],
            ],
            customers: [
                ['Aarav Sharma', 'aarav@example.com', '9801000001'],
                ['Priya Gurung', 'priya@example.com', '9801000002'],
                ['John Mathew', 'john@example.com', '9801000003'],
            ],
            settings: [
                'restaurant_name' => 'Spice Route Kitchen',
                'tagline'         => 'Fresh taste, warm hospitality',
                'currency'        => 'NPR',
                'tax_percent'     => '13',
                'hero_title'      => 'Authentic Flavours, Cooked With Love',
                'hero_subtitle'   => 'From sizzling momos to wood-fired pizzas — taste the Spice Route difference.',
                'about_text'      => 'Spice Route Kitchen has been serving the neighbourhood since 2015, blending traditional recipes with a modern dining experience.',
                'contact_email'   => 'hello@spice-route.test',
                'contact_phone'   => '+977 1-4000111',
                'contact_address' => 'Putalisadak, Kathmandu, Nepal',
                'opening_hours'   => 'Sun – Fri: 10:00 AM – 10:00 PM',
                'facebook_url'    => 'https://facebook.com/spiceroutekitchen',
                'instagram_url'   => 'https://instagram.com/spiceroutekitchen',
                'twitter_url'     => '',
            ],
            suppliers: [
                ['Kathmandu Fresh Produce', '9851000111', 'sales@kfp.test'],
                ['Himalayan Beverages Pvt. Ltd.', '9851000222', 'orders@himalayanbev.test'],
            ],
            inventory: [
                'Vegetables' => [
                    ['Tomatoes', 'kg', 40, 15, 0.80],
                    ['Onions', 'kg', 55, 20, 0.60],
                    ['Potatoes', 'kg', 70, 25, 0.50],
                ],
                'Meat & Poultry' => [
                    ['Chicken Breast', 'kg', 30, 10, 4.20],
                    ['Minced Beef', 'kg', 18, 8, 5.00],
                ],
                'Beverages' => [
                    ['Cola Syrup', 'litre', 12, 5, 3.50],
                    ['Bottled Water', 'crate', 25, 10, 6.00],
                ],
            ],
        );

        $this->seedCompany(
            name: 'Momo House Express',
            domain: 'momo-house.test',
            menu: [
                'Momo Specials' => [
                    ['Steam Momo', 'Classic steamed dumplings served with tomato achar', 5.50],
                    ['Jhol Momo', 'Dumplings drenched in a tangy spiced soup', 6.50],
                    ['Kothey Momo', 'Pan-fried dumplings, crispy on one side', 6.00],
                ],
                'Snacks' => [
                    ['Chatamari', 'Rice crepe topped with minced meat & egg', 5.00],
                    ['Sukuti Sandheko', 'Spiced dried meat salad with herbs & lime', 7.50],
                ],
                'Beverages' => [
                    ['Lassi', 'Sweet yogurt drink, served chilled', 3.00],
                    ['Black Tea', 'Strong brewed tea, served hot', 1.50],
                ],
            ],
            customers: [
                ['Suman Thapa', 'suman@example.com', '9802000001'],
                ['Emma Stone', 'emma@example.com', '9802000002'],
            ],
            settings: [
                'restaurant_name' => 'Momo House Express',
                'tagline'         => 'Steamed, fried, or souped — your momo, your way',
                'currency'        => 'NPR',
                'tax_percent'     => '13',
                'hero_title'      => 'Nepal’s Favourite Momo, Made Fresh Daily',
                'hero_subtitle'   => 'Quick bites, bold flavours — perfect for a fast, satisfying meal.',
                'about_text'      => 'Momo House Express started as a small street stall and grew into a beloved quick-service favourite for momo lovers across the city.',
                'contact_email'   => 'hello@momo-house.test',
                'contact_phone'   => '+977 1-4000222',
                'contact_address' => 'New Road, Kathmandu, Nepal',
                'opening_hours'   => 'Daily: 11:00 AM – 9:00 PM',
                'facebook_url'    => 'https://facebook.com/momohouseexpress',
                'instagram_url'   => '',
                'twitter_url'     => '',
            ],
            suppliers: [
                ['Valley Meat Suppliers', '9851000333', 'contact@valleymeat.test'],
            ],
            inventory: [
                'Dumpling Essentials' => [
                    ['Flour', 'kg', 80, 30, 0.70],
                    ['Minced Chicken', 'kg', 35, 12, 4.00],
                    ['Cabbage', 'kg', 25, 10, 0.45],
                ],
                'Beverages' => [
                    ['Yogurt', 'litre', 20, 8, 1.80],
                ],
            ],
        );

        $this->call(SubscriptionSeeder::class);
    }

    private function seedCompany(
        string $name,
        string $domain,
        array $menu,
        array $customers,
        array $settings,
        array $suppliers,
        array $inventory,
    ): void {
        $company = Company::create([
            'name'   => $name,
            'slug'   => Str::slug($name),
            'email'  => $settings['contact_email'],
            'phone'  => $settings['contact_phone'],
            'address'=> $settings['contact_address'],
            'status' => true,
        ]);

        $branch = Branch::create([
            'company_id' => $company->id,
            'name'       => 'Main Branch',
            'phone'      => $settings['contact_phone'],
            'email'      => $settings['contact_email'],
            'address'    => $settings['contact_address'],
            'status'     => true,
        ]);

        Domain::create(['company_id' => $company->id, 'domain' => $domain, 'is_primary' => true]);

        // ── Roles & users ───────────────────────────────────────
        $ownerRole = Role::create([
            'company_id'  => $company->id,
            'name'        => 'Owner',
            'description' => 'Full access to every module.',
            'permissions' => ['*'],
        ]);

        $managerRole = Role::create([
            'company_id'  => $company->id,
            'name'        => 'Manager',
            'description' => 'Day-to-day operations: orders, tables, menu, kitchen, customers, reports.',
            'permissions' => [
                'dashboard.view', 'orders.manage', 'tables.manage', 'menu.manage',
                'kitchen.manage', 'customers.manage', 'reports.view',
                'billing.view', 'payments.manage', 'suppliers.manage', 'inventory.manage',
                'purchase_orders.manage',
            ],
        ]);

        $staffRole = Role::create([
            'company_id'  => $company->id,
            'name'        => 'Staff',
            'description' => 'Front-of-house: orders, tables, and the kitchen board.',
            'permissions' => ['dashboard.view', 'orders.manage', 'tables.manage', 'kitchen.manage'],
        ]);

        $slug = Str::slug($name);

        $owner = User::create([
            'company_id' => $company->id, 'branch_id' => $branch->id, 'role_id' => $ownerRole->id,
            'name' => 'Owner ' . $name, 'email' => "owner@{$slug}.test", 'phone' => $settings['contact_phone'],
            'password' => Hash::make('password'), 'is_active' => true,
        ]);

        $manager = User::create([
            'company_id' => $company->id, 'branch_id' => $branch->id, 'role_id' => $managerRole->id,
            'name' => 'Manager ' . $name, 'email' => "manager@{$slug}.test", 'phone' => $settings['contact_phone'],
            'password' => Hash::make('password'), 'is_active' => true,
        ]);

        $staff = User::create([
            'company_id' => $company->id, 'branch_id' => $branch->id, 'role_id' => $staffRole->id,
            'name' => 'Staff ' . $name, 'email' => "staff@{$slug}.test", 'phone' => $settings['contact_phone'],
            'password' => Hash::make('password'), 'is_active' => true,
        ]);

        // ── Settings ────────────────────────────────────────────
        foreach ($settings as $key => $value) {
            Setting::create(['company_id' => $company->id, 'key' => $key, 'value' => $value]);
        }

        // ── Floor plan: rooms, tables, chairs ───────────────────
        $tables = collect();
        foreach (['Main Hall', 'Garden Terrace'] as $i => $roomName) {
            $room = Room::create([
                'branch_id'   => $branch->id,
                'name'        => $roomName,
                'description' => $roomName === 'Main Hall' ? 'Air-conditioned indoor dining area.' : 'Open-air seating with garden views.',
            ]);

            for ($n = 1; $n <= 4; $n++) {
                $table = FloorTable::create([
                    'branch_id'   => $branch->id,
                    'room_id'     => $room->id,
                    'table_no'    => sprintf('%s-%02d', $i === 0 ? 'T' : 'G', $n),
                    'capacity'    => [2, 4, 4, 6][$n - 1],
                    'pos_x'       => 80 + ($n - 1) * 140,
                    'pos_y'       => 80 + $i * 160,
                    'status'      => 'available',
                ]);

                foreach (['top', 'bottom', 'left', 'right'] as $side) {
                    Chair::create(['table_id' => $table->id, 'side' => $side]);
                }

                $tables->push($table);
            }
        }

        // Mark a couple of tables as occupied / reserved so the dashboard has signal
        $tables[0]->update(['status' => 'occupied']);
        $tables[1]->update(['status' => 'occupied']);
        $tables[4]->update(['status' => 'reserved']);

        TableBooking::create([
            'table_id' => $tables[4]->id, 'name' => $customers[0][0], 'phone' => $customers[0][2],
            'email' => $customers[0][1], 'booking_date' => Carbon::tomorrow()->toDateString(),
            'booking_time' => '19:00:00', 'party_size' => 4, 'status' => 'confirmed',
            'notes' => 'Birthday celebration — please prepare a window-side table.',
        ]);

        // ── Kitchen board columns ───────────────────────────────
        $columns = [
            ['name' => 'Pending', 'slug' => 'pending', 'color' => 'orange'],
            ['name' => 'Preparing', 'slug' => 'preparing', 'color' => 'blue'],
            ['name' => 'Ready', 'slug' => 'ready', 'color' => 'green'],
        ];
        foreach ($columns as $i => $col) {
            KitchenColumn::create($col + ['branch_id' => $branch->id, 'sort_order' => $i]);
        }

        // ── Menu ────────────────────────────────────────────────
        $menuItems = collect();
        $catIndex = 0;
        foreach ($menu as $catName => $items) {
            $category = MenuCategory::create([
                'branch_id'  => $branch->id,
                'name'       => $catName,
                'sort_order' => $catIndex++,
                'is_active'  => true,
            ]);

            foreach ($items as $sortOrder => [$itemName, $description, $price]) {
                $menuItems->push(MenuItem::create([
                    'menu_category_id' => $category->id,
                    'name'             => $itemName,
                    'description'      => $description,
                    'price'            => $price,
                    'preparation_time' => rand(10, 25),
                    'is_available'     => true,
                    'sort_order'       => $sortOrder,
                ]));
            }
        }

        // ── Customers ───────────────────────────────────────────
        $customerModels = collect();
        foreach ($customers as [$cname, $cemail, $cphone]) {
            $customerModels->push(Customer::create([
                'company_id'     => $company->id,
                'name'           => $cname,
                'email'          => $cemail,
                'phone'          => $cphone,
                'loyalty_points' => 0,
                'password'       => Hash::make('password'),
            ]));
        }

        // ── Suppliers & inventory ───────────────────────────────
        $supplierModels = collect();
        foreach ($suppliers as [$sname, $sphone, $semail]) {
            $supplierModels->push(Supplier::create([
                'company_id' => $company->id, 'name' => $sname, 'phone' => $sphone, 'email' => $semail,
            ]));
        }

        $inventoryItems = collect();
        foreach ($inventory as $catName => $items) {
            $invCategory = InventoryCategory::create(['branch_id' => $branch->id, 'name' => $catName]);

            foreach ($items as [$itemName, $unit, $qty, $reorder, $cost]) {
                $inventoryItems->push(InventoryItem::create([
                    'category_id'   => $invCategory->id,
                    'name'          => $itemName,
                    'unit'          => $unit,
                    'quantity'      => $qty,
                    'reorder_level' => $reorder,
                    'cost_price'    => $cost,
                ]));
            }
        }

        // One received purchase order from the first supplier covering a few items
        $poItems = $inventoryItems->take(3);
        $po = PurchaseOrder::create([
            'supplier_id' => $supplierModels->first()->id,
            'branch_id'   => $branch->id,
            'invoice_no'  => 'INV-' . strtoupper(Str::random(6)),
            'status'      => 'received',
            'total_amount' => 0,
        ]);
        $poTotal = 0;
        foreach ($poItems as $item) {
            $qty = 20;
            $subtotal = round($qty * $item->cost_price, 2);
            $poTotal += $subtotal;
            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id, 'inventory_item_id' => $item->id,
                'quantity' => $qty, 'unit_price' => $item->cost_price, 'subtotal' => $subtotal,
            ]);
        }
        $po->update(['total_amount' => $poTotal]);

        // ── Orders, line items, payments, kitchen tickets ───────
        $orderTypes = ['dine_in', 'dine_in', 'takeaway', 'delivery'];
        $statuses   = ['served', 'served', 'served', 'preparing', 'ready', 'pending'];

        for ($i = 0; $i < 18; $i++) {
            $createdAt = now()->subDays(rand(0, 13))->setTime(rand(11, 21), [0, 15, 30, 45][rand(0, 3)]);
            $status    = $statuses[array_rand($statuses)];
            $type      = $orderTypes[array_rand($orderTypes)];
            $customer  = $customerModels->isNotEmpty() && rand(0, 1) ? $customerModels->random() : null;
            $table     = $type === 'dine_in' ? $tables->random() : null;

            $order = new Order([
                'company_id'    => $company->id,
                'branch_id'     => $branch->id,
                'table_id'      => $table?->id,
                'customer_id'   => $customer?->id,
                'customer_name' => $customer->name ?? collect(['Walk-in', 'Guest', 'Phone Order'])->random(),
                'order_type'    => $type,
                'status'        => $status,
                'created_by'    => collect([$owner, $manager, $staff])->random()->id,
            ]);
            $order->order_no = 'ORD-' . str_pad((string) (Order::withoutGlobalScopes()->max('id') + 1), 4, '0', STR_PAD_LEFT);
            $order->created_at = $createdAt;
            $order->updated_at = $createdAt;
            $order->save();

            $lineCount = rand(1, 4);
            $picked = $menuItems->random(min($lineCount, $menuItems->count()));
            foreach ($picked as $menuItem) {
                $qty = rand(1, 3);
                $order->items()->create([
                    'menu_item_id' => $menuItem->id,
                    'name'         => $menuItem->name,
                    'quantity'     => $qty,
                    'price'        => $menuItem->price,
                ]);
            }

            $subtotal = (float) $order->items()->sum(DB::raw('quantity * price'));
            $discount = rand(0, 4) === 0 ? round($subtotal * 0.1, 2) : 0;
            $tax = round(($subtotal - $discount) * 0.13, 2);
            $order->update([
                'subtotal'    => $subtotal,
                'discount'    => $discount,
                'tax'         => $tax,
                'grand_total' => max(0, $subtotal - $discount + $tax),
            ]);

            if ($status === 'served' && $customer) {
                $customer->increment('loyalty_points', (int) floor($order->grand_total / 100));
            }

            if ($status === 'served') {
                Payment::create([
                    'order_id'       => $order->id,
                    'payment_method' => collect(['cash', 'card', 'wallet'])->random(),
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(8)),
                    'amount'         => $order->grand_total,
                    'status'         => 'paid',
                    'paid_at'        => $createdAt->copy()->addMinutes(rand(5, 40)),
                ]);
            }

            if (in_array($status, ['pending', 'preparing', 'ready'], true)) {
                KitchenTicket::create([
                    'order_id'    => $order->id,
                    'status'      => $status,
                    'prepared_by' => $status === 'ready' ? $staff->id : null,
                    'prepared_at' => $status === 'ready' ? $createdAt->copy()->addMinutes(20) : null,
                ]);
            }
        }
    }
}
