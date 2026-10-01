<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('terceros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            
            // Identificación
            $table->string('tipo_documento', 10)->default('CC'); // CC, NIT, CE, TI, Pasaporte
            $table->string('numero_documento', 50);
            $table->string('nombre_completo', 200);
            $table->string('primer_nombre', 100)->nullable();
            $table->string('primer_apellido', 100)->nullable();
            
            // Contacto
            $table->string('telefono', 50);
            $table->string('whatsapp', 50)->nullable();
            $table->string('email', 150)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('ciudad', 100)->default('Cartago');
            
            // Roles y Categorización del Tercero
            $table->boolean('es_cliente')->default(true);
            $table->boolean('es_proveedor')->default(false);
            $table->boolean('es_empleado')->default(false);
            
            // Atributos de Cliente y Crédito
            $table->decimal('limite_credito', 12, 2)->default(0);
            $table->unsignedInteger('dias_credito')->default(0);
            $table->unsignedInteger('puntos_fidelidad')->default(0);
            
            // Atributos de Empleado / Profesional
            $table->string('cargo', 100)->nullable(); // ej. Barbero, Estilista, Cajero, Tecnico
            $table->decimal('porcentaje_comision', 5, 2)->default(0.00)->comment('Porcentaje de comision por servicio realizado');
            
            $table->boolean('activo')->default(true);
            $table->timestamps();

            $table->unique(['empresa_id', 'numero_documento']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('terceros');
    }
};
