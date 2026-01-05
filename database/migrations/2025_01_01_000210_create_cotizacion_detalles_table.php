<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizacion_detalles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->cascadeOnDelete();
            $table->foreignId('producto_id')->nullable()->constrained('catalogo_productos');
            $table->string('codigo')->nullable();
            $table->text('descripcion');
            $table->integer('cantidad')->default(1);
            $table->decimal('precio_unitario', 12, 2)->default(0); // precio final ingresado (con IGV incluido si aplica)
            $table->decimal('total_linea', 12, 2)->default(0); // cantidad * precio_unitario
            $table->text('observaciones')->nullable(); // POR PRODUCTO (en rojo)
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizacion_detalles');
    }
};
