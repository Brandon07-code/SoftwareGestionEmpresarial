<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Facturas y Comprobantes de Venta
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            $table->foreignId('sesion_caja_id')->nullable()->constrained('sesiones_caja')->onDelete('set null');
            
            $table->string('numero_factura', 50); // FAC-0001
            $table->foreignId('tercero_cliente_id')->constrained('terceros');
            $table->foreignId('tercero_vendedor_id')->nullable()->constrained('terceros');
            
            $table->decimal('subtotal', 12, 2)->default(0.00);
            $table->decimal('descuento_puntos', 12, 2)->default(0.00);
            $table->decimal('impuesto_iva', 12, 2)->default(0.00);
            $table->decimal('total', 12, 2)->default(0.00);
            
            $table->unsignedInteger('puntos_ganados')->default(0);
            $table->unsignedInteger('puntos_canjeados')->default(0);
            
            $table->enum('estado', ['PENDIENTE', 'PAGADA', 'ANULADA'])->default('PENDIENTE');
            $table->text('notas')->nullable();
            $table->timestamps();

            $table->unique(['empresa_id', 'numero_factura']);
        });

        // 2. Líneas de detalle de la venta (Productos o Servicios)
        Schema::create('venta_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->onDelete('cascade');
            $table->enum('tipo_item', ['PRODUCTO', 'SERVICIO']);
            $table->unsignedBigInteger('item_id');
            $table->string('nombre_item', 200);
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2);
            $table->decimal('descuento', 12, 2)->default(0.00);
            $table->decimal('subtotal', 12, 2);
            $table->decimal('costo_unitario', 12, 2)->default(0.00)->comment('Para calcular utilidad bruta');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('venta_detalles');
        Schema::dropIfExists('ventas');
    }
};
