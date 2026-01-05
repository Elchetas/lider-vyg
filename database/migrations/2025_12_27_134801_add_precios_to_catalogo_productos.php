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
        Schema::table('catalogo_productos', function (Blueprint $table) {

            // Identificación del producto
            if (!Schema::hasColumn('catalogo_productos', 'codigo')) {
                $table->string('codigo')->unique();
            }

            if (!Schema::hasColumn('catalogo_productos', 'nombre_producto')) {
                $table->string('nombre_producto');
            }

            if (!Schema::hasColumn('catalogo_productos', 'descripcion')) {
                $table->string('descripcion')->nullable();
            }

            // Precios según tipo de cliente
            if (!Schema::hasColumn('catalogo_productos', 'precio_obra')) {
                $table->decimal('precio_obra', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('catalogo_productos', 'precio_edificio')) {
                $table->decimal('precio_edificio', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('catalogo_productos', 'precio_proveedor')) {
                $table->decimal('precio_proveedor', 10, 2)->default(0);
            }

            // Otros campos del Excel
            if (!Schema::hasColumn('catalogo_productos', 'cantidad')) {
                $table->integer('cantidad')->default(0);
            }

            if (!Schema::hasColumn('catalogo_productos', 'imagen')) {
                $table->string('imagen')->nullable();
            }

            if (!Schema::hasColumn('catalogo_productos', 'nombre_proveedor')) {
                $table->string('nombre_proveedor')->nullable();
            }

            if (!Schema::hasColumn('catalogo_productos', 'observaciones')) {
                $table->text('observaciones')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('catalogo_productos', function (Blueprint $table) {

            $columns = [
                'codigo',
                'nombre_producto',
                'descripcion',
                'precio_obra',
                'precio_edificio',
                'precio_proveedor',
                'cantidad',
                'imagen',
                'nombre_proveedor',
                'observaciones',
            ];

            foreach ($columns as $column) {
                if (Schema::hasColumn('catalogo_productos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
