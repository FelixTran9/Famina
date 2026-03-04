<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StaffController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\Auth\AdminLoginController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\WebPromotionController;
use App\Http\Controllers\Admin\WebCustomerController;
use App\Http\Controllers\Admin\WebSupplierController;

Route::get('/', function () {
    return view('shop_home'); // Gọi file shop_home.blade.php vừa tạo
})->name('home');

// Auth routes
Route::get('/admin/login', [AdminLoginController::class, 'showLoginForm'])->name('admin.login');
Route::post('/admin/login', [AdminLoginController::class, 'login']);
Route::post('/admin/logout', [AdminLoginController::class, 'logout'])->name('admin.logout');

Route::prefix('admin')->middleware('admin')->group(function () {
	// Dashboard
	Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
	Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard.home');
	// Staff
	Route::get('/staff', [StaffController::class, 'index'])->name('staff.index');
	Route::get('/staff/create', [StaffController::class, 'create'])->name('staff.create');
	Route::post('/staff', [StaffController::class, 'store'])->name('staff.store');
	Route::get('/staff/{id}/edit', [StaffController::class, 'edit'])->name('staff.edit');
	Route::put('/staff/{id}', [StaffController::class, 'update'])->name('staff.update');
	Route::delete('/staff/{id}', [StaffController::class, 'destroy'])->name('staff.destroy');

	// Products
	Route::get('/products', [ProductController::class, 'index'])->name('products.index');
	Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
	Route::post('/products', [ProductController::class, 'store'])->name('products.store');
	Route::get('/products/{id}/edit', [ProductController::class, 'edit'])->name('products.edit');
	Route::put('/products/{id}', [ProductController::class, 'update'])->name('products.update');
	Route::delete('/products/{id}', [ProductController::class, 'destroy'])->name('products.destroy');

	// Orders
	Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
	Route::get('/orders/create', [OrderController::class, 'create'])->name('orders.create');
	Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
	Route::get('/orders/{id}', [OrderController::class, 'show'])->name('orders.show');
	Route::get('/orders/{id}/edit', [OrderController::class, 'edit'])->name('orders.edit');
	Route::put('/orders/{id}', [OrderController::class, 'update'])->name('orders.update');
	Route::delete('/orders/{id}', [OrderController::class, 'destroy'])->name('orders.destroy');

	// Shipping
	Route::get('/shipping', [ShippingController::class, 'index'])->name('shipping.index');

	// Promotions
	Route::get('/promotions', [WebPromotionController::class, 'index'])->name('promotions.index');
	Route::get('/promotions/create', [WebPromotionController::class, 'create'])->name('promotions.create');
	Route::post('/promotions', [WebPromotionController::class, 'store'])->name('promotions.store');
	Route::get('/promotions/{id}/edit', [WebPromotionController::class, 'edit'])->name('promotions.edit');
	Route::put('/promotions/{id}', [WebPromotionController::class, 'update'])->name('promotions.update');
	Route::delete('/promotions/{id}', [WebPromotionController::class, 'destroy'])->name('promotions.destroy');

	// Customers
	Route::get('/customers', [WebCustomerController::class, 'index'])->name('customers.index');
	Route::get('/customers/create', [WebCustomerController::class, 'create'])->name('customers.create');
	Route::post('/customers', [WebCustomerController::class, 'store'])->name('customers.store');
	Route::get('/customers/{id}/edit', [WebCustomerController::class, 'edit'])->name('customers.edit');
	Route::put('/customers/{id}', [WebCustomerController::class, 'update'])->name('customers.update');
	Route::delete('/customers/{id}', [WebCustomerController::class, 'destroy'])->name('customers.destroy');

	// Suppliers
	Route::get('/suppliers', [WebSupplierController::class, 'index'])->name('suppliers.index');
	Route::get('/suppliers/create', [WebSupplierController::class, 'create'])->name('suppliers.create');
	Route::post('/suppliers', [WebSupplierController::class, 'store'])->name('suppliers.store');
	Route::get('/suppliers/{id}/edit', [WebSupplierController::class, 'edit'])->name('suppliers.edit');
	Route::put('/suppliers/{id}', [WebSupplierController::class, 'update'])->name('suppliers.update');
	Route::delete('/suppliers/{id}', [WebSupplierController::class, 'destroy'])->name('suppliers.destroy');
	Route::get('/shipping/create', [ShippingController::class, 'create'])->name('shipping.create');
	Route::post('/shipping', [ShippingController::class, 'store'])->name('shipping.store');
	Route::get('/shipping/{id}/edit', [ShippingController::class, 'edit'])->name('shipping.edit');
	Route::put('/shipping/{id}', [ShippingController::class, 'update'])->name('shipping.update');
	Route::delete('/shipping/{id}', [ShippingController::class, 'destroy'])->name('shipping.destroy');
});
