<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Categorías unificadas para productos y servicios
        Schema::create('categorias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre', 100);
            $table->enum('tipo', ['PRODUCTO', 'SERVICIO'])->default('PRODUCTO');
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 2. Productos tangibles (Retail, Insumos de consumo interno)
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('categoria_id')->constrained('categorias')->onDelete('cascade');
            $table->string('codigo_barras', 100)->nullable();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_costo', 12, 2)->default(0.00);
            $table->decimal('precio_venta', 12, 2)->default(0.00);
            $table->integer('stock_actual')->default(0);
            $table->integer('stock_minimo')->default(5);
            $table->boolean('maneja_inventario')->default(true);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        // 3. Servicios intangibles (Procedimientos por turno o labor)
        Schema::create('servicios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('categoria_id')->nullable()->constrained('categorias')->onDelete('set null');
            $table->string('codigo', 50)->nullable();
            $table->string('nombre', 200);
            $table->text('descripcion')->nullable();
            $table->decimal('precio_venta', 12, 2);
            $table->unsignedInteger('duracion_minutos')->default(30);
            $table->decimal('comision_base_porcentaje', 5, 2)->default(40.00)->comment('% de comision pagado al especialista');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('servicios');
        Schema::dropIfExists('productos');
        Schema::dropIfExists('categorias');
    }
};
