<?php

use Illuminate\Support\Facades\Route;
use Modules\Shop\Http\Controllers\Admin\AttributeController;
use Modules\Shop\Http\Controllers\Admin\BannerController;
use Modules\Shop\Http\Controllers\Admin\AuthController;
use Modules\Shop\Http\Controllers\Admin\BrandController;
use Modules\Shop\Http\Controllers\Admin\CategoryController;
use Modules\Shop\Http\Controllers\Admin\CustomerController;
use Modules\Shop\Http\Controllers\Admin\DashboardController;
use Modules\Shop\Http\Controllers\Admin\InventoryController;
use Modules\Shop\Http\Controllers\Admin\OrderController;
use Modules\Shop\Http\Controllers\Admin\PosController;
use Modules\Shop\Http\Controllers\Admin\ProductController;
use Modules\Shop\Http\Controllers\Admin\ProfileController;
use Modules\Shop\Http\Controllers\Admin\ReportController;
use Modules\Shop\Http\Controllers\Admin\SectionController;
use Modules\Shop\Http\Controllers\Admin\SettingController;
use Modules\Shop\Http\Controllers\Admin\StaffController;
use Modules\Shop\Http\Controllers\Admin\SupplierController;

// Shop back office: prefix "admin", names "shop.admin.*", guard "shop" (RouteServiceProvider).

Route::post('logout', [AuthController::class, 'logout'])->name('logout');

Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard.index');

Route::get('profile', [ProfileController::class, 'index'])->name('profile.index');
Route::put('profile', [ProfileController::class, 'update'])->name('profile.update');
Route::put('profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
Route::put('profile/theme', [ProfileController::class, 'updateTheme'])->name('profile.theme');

// ── Catalog ──
Route::get('category', [CategoryController::class, 'index'])->name('category.index');
Route::post('category', [CategoryController::class, 'store'])->name('category.store');
Route::put('category/{category}', [CategoryController::class, 'update'])->name('category.update');
Route::delete('category/{category}', [CategoryController::class, 'destroy'])->name('category.destroy');
Route::get('category/{parentId}/children', [CategoryController::class, 'children'])->whereNumber('parentId')->name('category.children');

Route::get('brand', [BrandController::class, 'index'])->name('brand.index');
Route::post('brand', [BrandController::class, 'store'])->name('brand.store');
Route::put('brand/{brand}', [BrandController::class, 'update'])->name('brand.update');
Route::delete('brand/{brand}', [BrandController::class, 'destroy'])->name('brand.destroy');

Route::get('attribute', [AttributeController::class, 'index'])->name('attribute.index');
Route::post('attribute', [AttributeController::class, 'store'])->name('attribute.store');
Route::put('attribute/{attribute}', [AttributeController::class, 'update'])->name('attribute.update');
Route::delete('attribute/{attribute}', [AttributeController::class, 'destroy'])->name('attribute.destroy');

// POS before the product resource, so "product/sell" isn't taken for a product id.
Route::get('product/sell', [PosController::class, 'sell'])->name('product.sell');
Route::post('product/sell', [PosController::class, 'storeSell'])->name('product.sell.store');
Route::get('product/{order}/invoice', [PosController::class, 'invoice'])->name('product.invoice');
Route::get('product/category/{category}/attributes', [ProductController::class, 'attributes'])->name('product.attributes');
Route::resource('product', ProductController::class)->except(['show']);

// ── Sales ──
Route::get('order', [OrderController::class, 'index'])->name('order.index');
Route::get('order/{order}', [OrderController::class, 'show'])->name('order.show');
Route::post('order/{order}/status/{status}', [OrderController::class, 'updateStatus'])->whereIn('status', ['delivered', 'cancelled'])->name('order.status.update');
Route::post('order/{order}/verify-payment', [OrderController::class, 'verifyPayment'])->name('order.verify_payment');

Route::get('customer', [CustomerController::class, 'index'])->name('customer.index');
Route::post('customer', [CustomerController::class, 'store'])->name('customer.store');
Route::post('customer/quick-store', [CustomerController::class, 'quickStore'])->name('customer.quick_store');
Route::get('customer/credit', [CustomerController::class, 'credit'])->name('customer.credit');
Route::get('customer/{customer}', [CustomerController::class, 'show'])->name('customer.show');
Route::put('customer/{customer}', [CustomerController::class, 'update'])->name('customer.update');
Route::delete('customer/{customer}', [CustomerController::class, 'destroy'])->name('customer.destroy');
Route::post('customer/{customer}/payment', [CustomerController::class, 'receivePayment'])->name('customer.payment');

// ── Stock ──
Route::get('inventory', [InventoryController::class, 'index'])->name('inventory.index');

Route::get('supplier', [SupplierController::class, 'index'])->name('supplier.index');
Route::post('supplier', [SupplierController::class, 'store'])->name('supplier.store');
Route::get('supplier/{supplier}', [SupplierController::class, 'show'])->name('supplier.show');
Route::put('supplier/{supplier}', [SupplierController::class, 'update'])->name('supplier.update');
Route::delete('supplier/{supplier}', [SupplierController::class, 'destroy'])->name('supplier.destroy');

Route::get('purchase', [SupplierController::class, 'purchases'])->name('supplier.purchases');
Route::get('purchase/create', [SupplierController::class, 'purchaseCreate'])->name('supplier.purchase.create');
Route::post('purchase', [SupplierController::class, 'purchaseStore'])->name('supplier.purchase.store');
Route::get('purchase/{purchaseOrder}', [SupplierController::class, 'purchaseShow'])->name('supplier.purchase.show');

// ── Reports ──
Route::get('report/sales', [ReportController::class, 'sales'])->name('report.sales');
Route::get('report/stock', [ReportController::class, 'stock'])->name('report.stock');
Route::get('report/profit', [ReportController::class, 'profit'])->name('report.profit');
Route::get('report/expenses', [ReportController::class, 'expenseIndex'])->name('report.expenses');
Route::post('report/expenses', [ReportController::class, 'expenseStore'])->name('report.expenses.store');
Route::delete('report/expenses/{expense}', [ReportController::class, 'expenseDestroy'])->name('report.expenses.destroy');

// ── Staff ──
Route::get('staff', [StaffController::class, 'index'])->name('staff.index');
Route::post('staff', [StaffController::class, 'store'])->name('staff.store');
Route::put('staff/{user}', [StaffController::class, 'update'])->name('staff.update');
Route::delete('staff/{user}', [StaffController::class, 'destroy'])->name('staff.destroy');
Route::post('staff/{user}/send-invite', [StaffController::class, 'sendInvite'])->name('staff.send_invite');

// ── Storefront ──
Route::get('setting', [SettingController::class, 'index'])->name('setting.index');
Route::put('setting', [SettingController::class, 'update'])->name('setting.update');

Route::get('banner', [BannerController::class, 'index'])->name('banner.index');
Route::post('banner', [BannerController::class, 'store'])->name('banner.store');
Route::put('banner/{banner}', [BannerController::class, 'update'])->name('banner.update');
Route::delete('banner/{banner}', [BannerController::class, 'destroy'])->name('banner.destroy');

Route::get('section', [SectionController::class, 'index'])->name('section.index');
Route::post('section', [SectionController::class, 'store'])->name('section.store');
Route::get('section/{section}/edit', [SectionController::class, 'edit'])->name('section.edit');
Route::put('section/{section}', [SectionController::class, 'update'])->name('section.update');
Route::delete('section/{section}', [SectionController::class, 'destroy'])->name('section.destroy');
