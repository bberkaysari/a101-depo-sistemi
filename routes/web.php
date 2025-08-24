<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\StockRequestController;

Route::get('/', function () {
    return view('welcome');
});

// Authentication routes
Route::get('/login', [App\Http\Controllers\Auth\LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [App\Http\Controllers\Auth\LoginController::class, 'login']);
Route::post('/logout', [App\Http\Controllers\Auth\LoginController::class, 'logout'])->name('logout');

// Protected routes
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard');
    
    // Resource routes
    Route::resource('locations', LocationController::class);
    Route::resource('categories', CategoryController::class);
    Route::resource('products', ProductController::class);
    Route::resource('stocks', StockController::class);
    Route::resource('stock-requests', StockRequestController::class);
    
    // Additional routes
    Route::get('categories/{category}/products', [CategoryController::class, 'products'])->name('categories.products');
    Route::get('products/{product}/stock-levels', [ProductController::class, 'stockLevels'])->name('products.stock-levels');
    Route::patch('stocks/{stock}/update-quantity', [StockController::class, 'updateQuantity'])->name('stocks.update-quantity');
    Route::get('stocks/low-stock', [StockController::class, 'lowStock'])->name('stocks.low-stock');
    Route::get('stocks/out-of-stock', [StockController::class, 'outOfStock'])->name('stocks.out-of-stock');
    Route::patch('stock-requests/{stockRequest}/approve', [StockRequestController::class, 'approve'])->name('stock-requests.approve');
    Route::patch('stock-requests/{stockRequest}/reject', [StockRequestController::class, 'reject'])->name('stock-requests.reject');
    Route::get('stock-requests/my-requests', [StockRequestController::class, 'myRequests'])->name('stock-requests.my-requests');
    Route::get('stock-requests/incoming-requests', [StockRequestController::class, 'incomingRequests'])->name('stock-requests.incoming-requests');
});
