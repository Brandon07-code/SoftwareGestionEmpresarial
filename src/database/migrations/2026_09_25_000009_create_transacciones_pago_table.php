<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_pago', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('venta_id')->nullable()->constrained('ventas')->onDelete('set null');
            $table->foreignId('cita_id')->nullable()->constrained('citas')->onDelete('set null');
            
            $table->enum('pasarela', ['WOMPI', 'NEQUI', 'EFECTIVO', 'TRANSFERENCIA'])->default('EFECTIVO');
            $table->string('referencia_interna', 100)->unique()->comment('Identificador unico generado por el ERP');
            $table->string('referencia_pasarela', 150)->nullable()->comment('ID devuelto por Wompi o Nequi');
            
            $table->decimal('monto', 12, 2);
            $table->string('moneda', 10)->default('COP');
            
            $table->enum('estado', ['PENDING', 'APPROVED', 'DECLINED', 'VOIDED', 'ERROR'])->default('PENDING');
            $table->string('metodo_pago_detalle', 100)->nullable()->comment('PSE, Bancolombia QR, Tarjeta Visa, Nequi Push');
            $table->json('respuesta_payload')->nullable()->comment('Respuesta cruda del Webhook de Wompi / Nequi');
            $table->string('url_checkout', 500)->nullable()->comment('Link de pago generado para el cliente');
            
            $table->dateTime('pagado_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_pago');
    }
};
