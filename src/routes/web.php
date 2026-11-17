<?php

use App\Http\Controllers\AuditController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\CrmController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\ExcelController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PasarelaPagoController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\SaleController;
use App\Http\Controllers\TerceroController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect('/login'));
Route::get('/api-tester', fn () => view('api-tester'))->name('api.tester');

Route::get('/dashboard', DashboardController::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Notificaciones del Sistema (Clase 14)
    Route::get('notificaciones', [NotificationController::class, 'index'])->name('notifications.index');
    Route::patch('notificaciones/{id}', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('notificaciones/leer-todas', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
    Route::post('notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');

    // Configuración de Organizaciones / Empresas (Multi-tenant)
    Route::resource('empresas', EmpresaController::class)->except(['show', 'destroy']);

    // Bitácora de Auditoría y Seguridad (Clase 12)
    Route::get('auditoria', [AuditController::class, 'index'])->name('audit.index');

    // Kárdex de Inventario (Clase 12)
    Route::get('kardex', [KardexController::class, 'index'])->name('kardex.index');
    Route::get('products/{product}/kardex', [KardexController::class, 'productKardex'])->name('products.kardex');

    // Excel: exportar e importar (Clase 13). Se declaran ANTES de Route::resource
    Route::get('products/exportar', [ExcelController::class, 'exportProducts'])->name('products.export');
    Route::get('products/importar', [ExcelController::class, 'importForm'])->name('products.import');
    Route::post('products/importar', [ExcelController::class, 'import'])->name('products.import.store');
    Route::get('products/plantilla', [ExcelController::class, 'template'])->name('products.template');
    Route::get('sales/exportar', [ExcelController::class, 'exportSales'])->name('sales.export');

    // Productos
    Route::get('products/export/excel', [ProductController::class, 'exportExcel'])->name('products.export.excel');
    Route::get('products/import/template', [ProductController::class, 'exportTemplate'])->name('products.import.template');
    Route::post('products/import/excel', [ProductController::class, 'importExcel'])->name('products.import.excel');
    Route::patch('products/{product}/restore', [ProductController::class, 'restore'])
        ->withTrashed()->name('products.restore');
    Route::resource('products', ProductController::class)->except('show');

    // Categorías
    Route::patch('categories/{category}/restore', [CategoryController::class, 'restore'])
        ->withTrashed()->name('categories.restore');
    Route::resource('categories', CategoryController::class)->except('show');

    // Clientes
    Route::patch('clients/{client}/restore', [ClientController::class, 'restore'])
        ->withTrashed()->name('clients.restore');
    Route::resource('clients', ClientController::class)->except('show');

    // Ventas
    Route::get('sales/export/excel', [SaleController::class, 'exportExcel'])->name('sales.export.excel');
    Route::get('sales/{sale}/pdf', [SaleController::class, 'pdf'])->name('sales.pdf');
    Route::resource('sales', SaleController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Proveedores (Clase 10)
    Route::patch('providers/{provider}/restore', [ProviderController::class, 'restore'])
        ->withTrashed()->name('providers.restore');
    Route::resource('providers', ProviderController::class)->except('show');

    // Compras (Clase 10)
    Route::patch('purchases/{purchase}/receive', [PurchaseController::class, 'receive'])
        ->name('purchases.receive');
    Route::get('purchases/{purchase}/pdf', [PurchaseController::class, 'pdf'])
        ->name('purchases.pdf');
    Route::resource('purchases', PurchaseController::class)->only(['index', 'create', 'store', 'show', 'destroy']);

    // Rutas operativas de Turnix ERP (Barbería y Perfumería JyM)
    Route::resource('citas', CitaController::class);
    Route::resource('terceros', TerceroController::class);
    Route::get('pagos', [PasarelaPagoController::class, 'index'])->name('pagos.index');
    Route::get('pagos/checkout/{id}', [PasarelaPagoController::class, 'checkout'])->name('pagos.checkout');
    Route::post('pagos/simular/{id}', [PasarelaPagoController::class, 'simularWebhook'])->name('pagos.simular');
    Route::get('crm', [CrmController::class, 'index'])->name('crm.index');
    Route::post('crm/interacciones', [CrmController::class, 'storeInteraccion'])->name('crm.interacciones.store');
});

require __DIR__.'/auth.php';
