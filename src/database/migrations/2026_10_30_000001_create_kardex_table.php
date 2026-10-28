<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('kardex')) {
            Schema::create('kardex', function (Blueprint $table) {
                $table->id();
                $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('movement_type', 30); // ENTRADA_COMPRA, SALIDA_VENTA, ANULACION_VENTA, AJUSTE_INICIAL, AJUSTE_STOCK
                $table->integer('quantity'); // Cantidad positiva movida
                $table->integer('stock_before');
                $table->integer('stock_after');
                $table->decimal('unit_price', 12, 2)->default(0.00);
                $table->string('document_reference', 50)->nullable(); // ej: OC-00001, FV-00001
                $table->string('description', 255)->nullable();
                $table->timestamps();

                $table->index(['product_id', 'created_at']);
                $table->index('movement_type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('kardex');
    }
};
