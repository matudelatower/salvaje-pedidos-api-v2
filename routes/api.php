<?php

use App\Http\Controllers\Api\OrderController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Rutas públicas para la app móvil
Route::prefix('v1')->group(function () {
    // Configuración global (pública)
    Route::get('/orders-status', [SettingController::class, 'getOrdersStatus']);
    
    // Productos y categorías (públicos)
    Route::get('/products', [OrderController::class, 'products']);
    Route::get('/categories', [OrderController::class, 'categories']);
    Route::get('/banners', [OrderController::class, 'banners']);
    
    // Pedidos (requieren autenticación o token temporal)
    Route::post('/orders', [OrderController::class, 'store']);
    Route::get('/orders/{order}', [OrderController::class, 'show']);
    Route::get('/orders/{order}/items', [OrderController::class, 'items']);
    Route::get('/orders/{order}/status', [OrderController::class, 'status']);
    Route::post('/orders/{order}/payment', [OrderController::class, 'createPaymentPreference']);
});

// Rutas de prueba para desarrollo
Route::get('/test', function () {
    return response()->json([
        'message' => 'Salvaje Bar API v1.0',
        'status' => 'online',
        'timestamp' => now()->toISOString()
    ]);
});
