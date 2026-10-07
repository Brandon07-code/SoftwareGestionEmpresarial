<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\TerceroController;
use App\Http\Controllers\PasarelaPagoController;
use App\Http\Controllers\CrmController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Productos (los permisos se validan dentro del controlador)
    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->withTrashed()
        ->name('products.restore');
    Route::resource('products', ProductController::class)->except('show');

    // Categorías
    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->withTrashed()
        ->name('categories.restore');
    Route::resource('categories', CategoryController::class)->except('show');

    // Rutas operativas del ERP
    Route::resource('citas', CitaController::class);
    Route::resource('terceros', TerceroController::class);
    Route::get('pagos', [PasarelaPagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/checkout/{id}', [PasarelaPagoController::class, 'checkout'])->name('pagos.checkout');
    Route::post('pagos/simular/{id}', [PasarelaPagoController::class, 'simularWebhook'])->name('pagos.simular');
    Route::get('crm', [CrmController::class, 'index'])->name('crm.index');
    Route::post('crm/interacciones', [CrmController::class, 'storeInteraccion'])->name('crm.interacciones.store');
});

require __DIR__.'/auth.php';
