<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SaleController;
use App\Http\Controllers\Api\V1\AuthController as LegacyAuthController;
use App\Http\Controllers\Api\V1\ClientApiController;
use App\Http\Controllers\Api\V1\KardexApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use App\Http\Controllers\Api\V1\SaleApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Turnix ERP v1 - Clase 13)
|--------------------------------------------------------------------------
| Versionar la API (v1) permite cambiarla en el futuro sin romper las apps que ya la usan.
*/

Route::prefix('v1')->group(function () {
    // =========================================================================
    // ENDPOINTS CANÓNICOS DE CLASE 13 (GUÍA OFICIAL)
    // =========================================================================

    // Pública: máximo 5 intentos de login por minuto por IP
    Route::post('login', [AuthController::class, 'login'])->middleware('throttle:5,1');

    // Protegidas con token Bearer de Sanctum: 60 peticiones por minuto
    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('me', [AuthController::class, 'me']);
        Route::post('logout', [AuthController::class, 'logout']);

        Route::get('products', [ProductController::class, 'index']);
        Route::get('products/{product}', [ProductController::class, 'show']);

        Route::get('sales/today', [SaleApiController::class, 'today']);
        Route::get('sales/{sale}', [SaleController::class, 'show']);
        Route::post('sales', [SaleController::class, 'store']);
    });

    // =========================================================================
    // ENDPOINTS EXTENDIDOS DEL ECOSISTEMA TURNIX ERP (COMPATIBILIDAD CON SUITE)
    // =========================================================================
    Route::post('auth/login', [LegacyAuthController::class, 'login'])->middleware('throttle:5,1');

    Route::middleware(['auth:sanctum', 'throttle:60,1'])->group(function () {
        Route::get('auth/me', [LegacyAuthController::class, 'me']);
        Route::post('auth/logout', [LegacyAuthController::class, 'logout']);

        Route::post('products', [ProductApiController::class, 'store']);

        Route::get('kardex', [KardexApiController::class, 'index']);
        Route::get('products/{product}/kardex', [KardexApiController::class, 'productKardex']);

        Route::get('sales', [SaleApiController::class, 'index']);

        Route::get('clients', [ClientApiController::class, 'index']);
        Route::get('clients/{client}', [ClientApiController::class, 'show']);
        Route::post('clients', [ClientApiController::class, 'store']);
    });
});
