<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('catalogo_productos', function (Blueprint $table) {
            // Estructura oficial
            if (!Schema::hasColumn('catalogo_productos', 'precio_obra')) {
                $table->decimal('precio_obra', 12, 2)->default(0)->after('descripcion');
            }
            if (!Schema::hasColumn('catalogo_productos', 'precio_edificio')) {
                $table->decimal('precio_edificio', 12, 2)->default(0)->after('precio_obra');
            }
            if (!Schema::hasColumn('catalogo_productos', 'cantidad')) {
                $table->integer('cantidad')->default(0)->after('precio_edificio');
            }
            if (!Schema::hasColumn('catalogo_productos', 'precio_proveedor')) {
                $table->decimal('precio_proveedor', 12, 2)->default(0)->after('cantidad');
            }
            if (!Schema::hasColumn('catalogo_productos', 'imagen')) {
                $table->string('imagen')->nullable()->after('precio_proveedor');
            }
            if (!Schema::hasColumn('catalogo_productos', 'nombre_proveedor')) {
                $table->string('nombre_proveedor')->nullable()->after('imagen');
            }
        });
    }

    public function down(): void
    {
        Schema::table('catalogo_productos', function (Blueprint $table) {
            foreach (['precio_obra','precio_edificio','cantidad','precio_proveedor','imagen','nombre_proveedor'] as $col) {
                if (Schema::hasColumn('catalogo_productos', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
