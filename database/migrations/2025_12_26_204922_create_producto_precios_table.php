<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
       Schema::create('producto_precios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_producto_id')->constrained('catalogo_productos')->cascadeOnDelete();
            $table->foreignId('unidad_inmobiliaria_id')->constrained('unidades_inmobiliarias')->cascadeOnDelete();
            $table->decimal('precio', 12, 2);
            $table->unique(['catalogo_producto_id', 'unidad_inmobiliaria_id']);
            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('producto_precios');
    }
};
