<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            if (!Schema::hasColumn('cotizaciones', 'tipo_precio')) {
                $table->enum('tipo_precio', ['obra', 'edificio'])
                    ->default('obra')
                    ->after('cliente_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('cotizaciones', function (Blueprint $table) {
            if (Schema::hasColumn('cotizaciones', 'tipo_precio')) {
                $table->dropColumn('tipo_precio');
            }
        });
    }
};
