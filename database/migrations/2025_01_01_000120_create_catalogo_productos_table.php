<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('catalogo_productos', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->index();
            $table->string('nombre_producto');
            $table->text('descripcion')->nullable();
            $table->string('imagen_path')->nullable(); // storage/app/public/productos/...
            $table->decimal('precio', 12, 2)->default(0); // Precio final (incluye IGV si aplica en la cotización)
            $table->decimal('precio_prov', 12, 2)->default(0);
            $table->string('proveedor')->nullable();
            $table->text('observaciones')->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogo_productos');
    }
};
