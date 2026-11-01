<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\ClientApiController;
use App\Http\Controllers\Api\V1\KardexApiController;
use App\Http\Controllers\Api\V1\ProductApiController;
use App\Http\Controllers\Api\V1\SaleApiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (Turnix ERP v1 - Clase 13)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    // Autenticación pública con entrega de tokens Sanctum
    Route::post('auth/login', [AuthController::class, 'login']);

    // Endpoints protegidos mediante Bearer Token (Sanctum)
    Route::middleware('auth:sanctum')->group(function () {
        // Gestión de sesión y perfil
        Route::get('auth/me', [AuthController::class, 'me']);
        Route::post('auth/logout', [AuthController::class, 'logout']);

        // 1. Catálogo de Productos (consulta y registro)
        Route::get('products', [ProductApiController::class, 'index']);
        Route::get('products/{product}', [ProductApiController::class, 'show']);
        Route::post('products', [ProductApiController::class, 'store']);

        // 2. Kárdex de Inventario y Existencias físicas
        Route::get('kardex', [KardexApiController::class, 'index']);
        Route::get('products/{product}/kardex', [KardexApiController::class, 'productKardex']);

        // 3. Ventas y Facturación (Ventas del día y Registro transaccional)
        Route::get('sales', [SaleApiController::class, 'index']);
        Route::get('sales/today', [SaleApiController::class, 'today']);
        Route::get('sales/{sale}', [SaleApiController::class, 'show']);
        Route::post('sales', [SaleApiController::class, 'store']);

        // 4. Directorio de Clientes (consulta y registro)
        Route::get('clients', [ClientApiController::class, 'index']);
        Route::get('clients/{client}', [ClientApiController::class, 'show']);
        Route::post('clients', [ClientApiController::class, 'store']);
    });
});
