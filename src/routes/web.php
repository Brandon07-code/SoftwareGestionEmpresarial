<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CitaController;

// Redirección principal al módulo de citas
Route::get('/', function () {
    return redirect()->route('citas.index');
});

// Rutas Resource para el módulo de citas (CRUD completo)
Route::resource('citas', CitaController::class);
