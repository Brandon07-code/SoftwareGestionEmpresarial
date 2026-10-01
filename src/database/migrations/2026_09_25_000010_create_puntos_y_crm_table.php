<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Libro Mayor / Kárdex de Puntos de Fidelización
        Schema::create('puntos_movimientos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('tercero_id')->constrained('terceros')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
            
            $table->enum('tipo', ['ACUMULACION', 'REDENCION', 'AJUSTE_MANUAL', 'VENCIMIENTO']);
            $table->integer('puntos')->comment('Valor positivo para ingreso, negativo para redencion');
            $table->unsignedInteger('saldo_anterior');
            $table->unsignedInteger('saldo_nuevo');
            $table->string('motivo', 255);
            $table->timestamp('created_at')->useCurrent();
        });

        // 2. CRM - Historial de Interacciones con Clientes
        Schema::create('crm_interacciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('tercero_id')->constrained('terceros')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade')->comment('Empleado que registro');
            
            $table->enum('canal', ['WHATSAPP', 'LLAMADA', 'PRESENCIAL', 'EMAIL'])->default('PRESENCIAL');
            $table->enum('tipo', ['PREFERENCIA', 'SEGUIMIENTO', 'RECLAMO', 'FELICITACION'])->default('SEGUIMIENTO');
            $table->text('nota');
            $table->dateTime('fecha_contacto');
            $table->date('proximo_seguimiento')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('crm_interacciones');
        Schema::dropIfExists('puntos_movimientos');
    }
};
