<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Puntos de venta / Cajas físicas
        Schema::create('cajas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->string('nombre', 100); // Caja 1 - Principal
            $table->enum('estado', ['CERRADA', 'ABIERTA'])->default('CERRADA');
            $table->timestamps();
        });

        // Sesiones de caja (Aperturas, movimientos y Arqueos de caja)
        Schema::create('sesiones_caja', function (Blueprint $table) {
            $table->id();
            $table->foreignId('caja_id')->constrained('cajas')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Cajero responsable');
            $table->decimal('monto_apertura', 12, 2)->default(0.00)->comment('Base de efectivo');
            $table->decimal('monto_cierre_efectivo', 12, 2)->nullable()->comment('Conteo de plata fisica al cierre');
            $table->decimal('total_ventas_efectivo', 12, 2)->default(0.00);
            $table->decimal('total_ventas_digitales', 12, 2)->default(0.00)->comment('Wompi, Nequi, Transferencia');
            $table->decimal('diferencia', 12, 2)->default(0.00)->comment('Sobrante (+) o Faltante (-)');
            $table->enum('estado', ['ABIERTA', 'CERRADA'])->default('ABIERTA');
            $table->text('observaciones')->nullable();
            $table->dateTime('abierto_at');
            $table->dateTime('cerrado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones_caja');
        Schema::dropIfExists('cajas');
    }
};
