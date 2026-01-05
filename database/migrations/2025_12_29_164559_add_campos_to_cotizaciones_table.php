<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            // No se agregan columnas aquí
            // Esta migración se mantiene solo por historial
        });
    }

    public function down(): void
    {
        // No rollback necesario
    }
};
