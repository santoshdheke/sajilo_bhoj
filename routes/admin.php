<?php

use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\BillingController;
use App\Http\Controllers\Admin\CouponController;
use App\Http\Controllers\Admin\CustomerController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InventoryController;
use App\Http\Controllers\Admin\KitchenController;
use App\Http\Controllers\Admin\MenuCategoryController;
use App\Http\Controllers\Admin\OrderController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PurchaseOrderController;
use App\Http\Controllers\Admin\SupplierController;
use App\Http\Controllers\Admin\TableController;
use App\Http\Controllers\Admin\MenuController;
use App\Http\Controllers\Admin\NavMenuItemController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Middleware\EnsureSubscriptionChosen;
use Illuminate\Support\Facades\Route;

Route::withoutMiddleware(EnsureSubscriptionChosen::class)->group(function () {
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('subscription/choose', [SubscriptionController::class, 'choose'])->name('subscription.choose');
    Route::post('subscription/choose', [SubscriptionController::class, 'store'])->name('subscription.store');
});

Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update_password');

Route::middleware('permission:dashboard.view')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('dashboard/chat-facts', [DashboardController::class, 'chatFacts'])->name('dashboard.chat_facts');
});

// Table floor-plan management
Route::middleware('permission:tables.manage')->group(function () {
    Route::get('tables', [TableController::class, 'index'])->name('table.index');
    Route::post('tables', [TableController::class, 'store'])->name('table.store');
    Route::put('tables/{table}', [TableController::class, 'update'])->name('table.update');
    Route::delete('tables/{table}', [TableController::class, 'destroy'])->name('table.destroy');
    Route::patch('tables/{table}/status', [TableController::class, 'updateTableStatus'])->name('table.status');

    // Bookings
    Route::post('bookings', [TableController::class, 'storeBooking'])->name('booking.store');
    Route::patch('bookings/{booking}/status', [TableController::class, 'updateBookingStatus'])->name('booking.status');
    Route::delete('bookings/{booking}', [TableController::class, 'destroyBooking'])->name('booking.destroy');

    // Rooms
    Route::post('rooms', [TableController::class, 'storeRoom'])->name('room.store');
    Route::put('rooms/{room}', [TableController::class, 'updateRoom'])->name('room.update');
    Route::post('rooms/{room}/photo', [TableController::class, 'uploadRoomPhoto'])->name('room.photo');
    Route::delete('rooms/{room}', [TableController::class, 'destroyRoom'])->name('room.destroy');

    // Table photos (multiple)
    Route::post('tables/{table}/photos', [TableController::class, 'storeTablePhotos'])->name('table.photos.store');
    Route::delete('table-photos/{tablePhoto}', [TableController::class, 'destroyTablePhoto'])->name('table.photo.destroy');

    // Chairs
    Route::post('chairs', [TableController::class, 'storeChair'])->name('chair.store');
    Route::delete('chairs/{chair}', [TableController::class, 'destroyChair'])->name('chair.destroy');
});

Route::middleware('permission:orders.manage')->group(function () {
    Route::get('orders', [OrderController::class, 'index'])->name('order.index');
    Route::post('orders', [OrderController::class, 'store'])->name('order.store');
    Route::get('orders/{order}', [OrderController::class, 'show'])->name('order.show');
    Route::patch('orders/{order}/status', [OrderController::class, 'updateStatus'])->name('order.status');
    Route::delete('orders/{order}', [OrderController::class, 'destroy'])->name('order.destroy');
});

Route::middleware('permission:kitchen.manage')->group(function () {
    Route::get('kitchen', [KitchenController::class, 'index'])->name('kitchen.index');
    Route::post('kitchen/orders', [KitchenController::class, 'store'])->name('kitchen.order.store');
    Route::patch('kitchen/orders/{order}/status', [KitchenController::class, 'updateStatus'])->name('kitchen.order.status');
    Route::delete('kitchen/orders/{order}', [KitchenController::class, 'destroy'])->name('kitchen.order.destroy');
    Route::post('kitchen/columns/reorder', [KitchenController::class, 'reorderColumns'])->name('kitchen.column.reorder');
    Route::post('kitchen/columns', [KitchenController::class, 'storeColumn'])->name('kitchen.column.store');
    Route::put('kitchen/columns/{column}', [KitchenController::class, 'updateColumn'])->name('kitchen.column.update');
    Route::delete('kitchen/columns/{column}', [KitchenController::class, 'destroyColumn'])->name('kitchen.column.destroy');
});

Route::middleware('permission:billing.view')->group(function () {
    Route::get('billing', [BillingController::class, 'index'])->name('billing.index');
});

Route::middleware('permission:menu.manage')->group(function () {
    Route::get('menu', [MenuController::class, 'index'])->name('menu.index');
    Route::post('menu', [MenuController::class, 'store'])->name('menu.store');
    Route::post('menu/reorder', [MenuController::class, 'reorder'])->name('menu.reorder'); // before menu/{menuItem}
    Route::get('menu/{menuItem}', [MenuController::class, 'show'])->name('menu.show');
    Route::post('menu/{menuItem}', [MenuController::class, 'update'])->name('menu.update');
    Route::patch('menu/{menuItem}/toggle', [MenuController::class, 'toggleAvailability'])->name('menu.toggle');
    Route::delete('menu/{menuItem}', [MenuController::class, 'destroy'])->name('menu.destroy');

    Route::get('menu-category', [MenuCategoryController::class, 'index'])->name('menu_category.index');
    Route::post('menu-category', [MenuCategoryController::class, 'store'])->name('menu_category.store');
    Route::put('menu-category/{menuCategory}', [MenuCategoryController::class, 'update'])->name('menu_category.update');
    Route::delete('menu-category/{menuCategory}', [MenuCategoryController::class, 'destroy'])->name('menu_category.destroy');
});

Route::middleware('permission:reports.view')->group(function () {
    Route::get('report', [ReportController::class, 'index'])->name('report.index');
});

Route::middleware('permission:users.manage')->group(function () {
    Route::get('user', [UserController::class, 'index'])->name('user.index');
    Route::post('user', [UserController::class, 'store'])->name('user.store');
    Route::put('user/{user}', [UserController::class, 'update'])->name('user.update');
    Route::patch('user/{user}/toggle', [UserController::class, 'toggleActive'])->name('user.toggle');
    Route::delete('user/{user}', [UserController::class, 'destroy'])->name('user.destroy');
});

Route::middleware('permission:settings.manage')->prefix('settings')->name('settings.')->group(function () {
    Route::get('{section?}', [SettingController::class, 'index'])->name('index')
        ->where('section', 'general|theme|homepage|about|contact|social');
    Route::put('{section}', [SettingController::class, 'update'])->name('update')
        ->where('section', 'general|theme|homepage|about|contact');

    Route::get('navigation', [NavMenuItemController::class, 'index'])->name('navigation.index');
    Route::post('navigation', [NavMenuItemController::class, 'store'])->name('navigation.store');
    Route::put('navigation/{navMenuItem}', [NavMenuItemController::class, 'update'])->name('navigation.update');
    Route::delete('navigation/{navMenuItem}', [NavMenuItemController::class, 'destroy'])->name('navigation.destroy');
    Route::post('navigation/reorder', [NavMenuItemController::class, 'reorder'])->name('navigation.reorder');
});

Route::middleware('permission:suppliers.manage')->group(function () {
    Route::get('suppliers', [SupplierController::class, 'index'])->name('supplier.index');
    Route::post('suppliers', [SupplierController::class, 'store'])->name('supplier.store');
    Route::put('suppliers/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
    Route::delete('suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');
});

Route::middleware('permission:inventory.manage')->group(function () {
    Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');

    Route::post('inventory-categories', [InventoryController::class, 'storeCategory'])->name('inventory_category.store');
    Route::put('inventory-categories/{inventoryCategory}', [InventoryController::class, 'updateCategory'])->name('inventory_category.update');
    Route::delete('inventory-categories/{inventoryCategory}', [InventoryController::class, 'destroyCategory'])->name('inventory_category.destroy');

    Route::post('inventory-items', [InventoryController::class, 'storeItem'])->name('inventory_item.store');
    Route::put('inventory-items/{inventoryItem}', [InventoryController::class, 'updateItem'])->name('inventory_item.update');
    Route::delete('inventory-items/{inventoryItem}', [InventoryController::class, 'destroyItem'])->name('inventory_item.destroy');
});

Route::middleware('permission:purchase_orders.manage')->group(function () {
    Route::get('purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase_order.index');
    Route::post('purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase_order.store');
    Route::put('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'update'])->name('purchase_order.update');
    Route::delete('purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'destroy'])->name('purchase_order.destroy');
});

Route::middleware('permission:coupons.manage')->group(function () {
    Route::get('coupons', [CouponController::class, 'index'])->name('coupon.index');
    Route::post('coupons', [CouponController::class, 'store'])->name('coupon.store');
    Route::put('coupons/{coupon}', [CouponController::class, 'update'])->name('coupon.update');
    Route::delete('coupons/{coupon}', [CouponController::class, 'destroy'])->name('coupon.destroy');
});

Route::middleware('permission:customers.manage')->group(function () {
    Route::get('customers', [CustomerController::class, 'index'])->name('customer.index');
    Route::put('customers/{customer}', [CustomerController::class, 'update'])->name('customer.update');
    Route::delete('customers/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
});

Route::middleware('permission:payments.manage')->group(function () {
    Route::get('payments', [PaymentController::class, 'index'])->name('payment.index');
    Route::post('payments', [PaymentController::class, 'store'])->name('payment.store');
    Route::put('payments/{payment}', [PaymentController::class, 'update'])->name('payment.update');
    Route::get('payments-orders-without-payment', [PaymentController::class, 'ordersWithoutPayment'])->name('payment.orders_without_payment');
});
