<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cotizacion_id')->constrained('cotizaciones')->unique();
            $table->string('numero', 5)->unique(); // igual a cotización.numero
            $table->date('fecha');
            $table->enum('estado', ['Emitida','Anulada'])->default('Emitida');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guias');
    }
};
