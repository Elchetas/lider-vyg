<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_cliente');
            $table->string('direccion')->nullable();
            $table->string('lugar')->nullable();
            $table->string('nombre_administrador')->nullable();
            $table->text('observacion')->nullable();
            $table->string('unidad_inmobiliaria')->nullable();
            $table->enum('tipo_comprobante', ['Factura','Boleta'])->default('Factura');
            $table->string('nombre_factura')->nullable();
            $table->string('ruc', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
