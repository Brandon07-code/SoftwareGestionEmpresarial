<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Kárdex de Inventario (Entradas, Salidas, Mermas y Ajustes)
        Schema::create('inventario_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            $table->enum('tipo_movimiento', [
                'ENTRADA_COMPRA',
                'SALIDA_VENTA',
                'AJUSTE_POSITIVO',
                'AJUSTE_NEGATIVO',
                'MERMA'
            ]);
            $table->integer('cantidad');
            $table->decimal('costo_unitario', 12, 2);
            $table->integer('stock_anterior');
            $table->integer('stock_nuevo');
            $table->string('referencia_documento', 100)->nullable();
            $table->string('motivo', 255)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventario_movimientos');
    }
};
