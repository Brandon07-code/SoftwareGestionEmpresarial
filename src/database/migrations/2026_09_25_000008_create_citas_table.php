<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            
            $table->foreignId('tercero_cliente_id')->constrained('terceros')->comment('Tercero con es_cliente=true');
            $table->foreignId('tercero_especialista_id')->constrained('terceros')->comment('Tercero con es_empleado=true');
            $table->foreignId('servicio_id')->constrained('servicios')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null')->comment('Factura generada al liquidar');
            
            $table->dateTime('fecha_hora');
            $table->decimal('total', 12, 2);
            $table->enum('estado', ['PROGRAMADA', 'EN_ATENCION', 'COMPLETADA', 'CANCELADA'])->default('PROGRAMADA');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
