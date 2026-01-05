<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('cotizaciones', function (Blueprint $table) {
            $table->id();
            $table->string('numero', 5)->unique(); // MM + 3 dígitos (ej: 12001)
            $table->date('fecha_emision');
            $table->foreignId('cliente_id')->constrained('clientes');
            $table->enum('moneda', ['PEN','USD'])->default('PEN');
            $table->boolean('afecto_igv')->default(true);
            $table->enum('estado', ['Pendiente','Aprobada','Anulada'])->default('Pendiente');
            $table->decimal('subtotal', 12, 2)->default(0);
            $table->decimal('igv', 12, 2)->default(0);
            $table->decimal('total', 12, 2)->default(0);
            $table->text('observaciones')->nullable(); // generales del doc
            $table->foreignId('creado_por')->nullable()->constrained('users');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
