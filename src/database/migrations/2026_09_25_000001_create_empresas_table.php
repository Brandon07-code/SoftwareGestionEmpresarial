<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('empresas', function (Blueprint $table) {
            $table->id();
            $table->string('nit', 30)->unique();
            $table->string('razon_social', 200);
            $table->string('nombre_comercial', 200);
            $table->enum('tipo_negocio', ['servicios', 'retail', 'mixto'])->default('mixto');
            $table->string('telefono', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->default('Cartago');
            $table->string('moneda', 10)->default('COP');
            $table->unsignedInteger('puntos_por_monto')->default(10000)->comment('Monto en COP para ganar 1 punto');
            $table->unsignedInteger('valor_por_punto')->default(500)->comment('Descuento en COP por cada punto redimido');
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });

        Schema::create('sucursales', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->string('nombre', 150);
            $table->string('codigo', 20)->nullable();
            $table->string('telefono', 50)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sucursales');
        Schema::dropIfExists('empresas');
    }
};
