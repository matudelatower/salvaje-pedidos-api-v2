<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BannerController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\MercadoPagoController;
use App\Http\Controllers\SettingController;

Route::get('/', function () {
    return redirect()->route('login');
});

Auth::routes();

Route::middleware(['auth'])->group(function () {
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
    
    // Rutas para ABM de usuarios (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('users', UserController::class)->except(['show', 'edit', 'update']);
    });
    
    // Rutas para perfil de usuario (propio o admin)
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [UserController::class, 'update'])->name('users.update');
    
    // Rutas para ABM de categorías (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('categories', CategoryController::class);
    });
    
    // Rutas para ABM de productos (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('products', ProductController::class);
        Route::delete('/products/media/{media}', [ProductController::class, 'deleteMedia'])->name('products.media.delete');
    });
    
    // Rutas para ABM de banners (usuarios y admin)
    Route::middleware(['role:admin,usuario'])->group(function () {
        Route::resource('banners', BannerController::class);
        Route::post('/banners/reorder', [BannerController::class, 'reorder'])->name('banners.reorder');
    });
    
    // Rutas para ABM de unidades (solo admin)
    Route::middleware(['role:admin'])->group(function () {
        Route::resource('units', UnitController::class);
    });
    
    // Rutas para gestión de pedidos (usuarios y admin)
    Route::middleware(['role:admin,usuario'])->group(function () {
        Route::resource('orders', OrderController::class);
        Route::post('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.updateStatus');
        Route::get('/orders/{order}/whatsapp', [OrderController::class, 'sendWhatsApp'])->name('orders.whatsapp');
        Route::post('/orders/{order}/payment-pending', [OrderController::class, 'sendPaymentPending'])->name('orders.payment.pending');
    });
    
    // Rutas para MercadoPago (usuarios y admin)
    Route::middleware(['role:admin,usuario'])->group(function () {
        Route::post('/orders/{order}/mercadopago/preference', [MercadoPagoController::class, 'createPreference'])->name('mercadopago.preference');
        Route::get('/orders/{order}/mercadopago/retry', [MercadoPagoController::class, 'retryPayment'])->name('mercadopago.retry');
    });
    
    // Rutas para configuración global (todos los usuarios autenticados)
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::post('/settings/toggle-orders', [SettingController::class, 'toggleOrders'])->name('settings.toggle-orders');
    Route::get('/api/orders-status', [SettingController::class, 'getOrdersStatus'])->name('api.orders-status');
});

// Rutas públicas para MercadoPago (sin autenticación)
Route::get('/mercadopago/success', [MercadoPagoController::class, 'success'])->name('mercadopago.success');
Route::get('/mercadopago/failure', [MercadoPagoController::class, 'failure'])->name('mercadopago.failure');
Route::get('/mercadopago/pending', [MercadoPagoController::class, 'pending'])->name('mercadopago.pending');
Route::post('/mercadopago/webhook', [MercadoPagoController::class, 'webhook'])->name('mercadopago.webhook');
