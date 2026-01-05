<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('empresa_configs', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_empresa');
            $table->string('email')->nullable();
            $table->string('telefono')->nullable();
            $table->string('ruc')->nullable();
            $table->string('direccion')->nullable();
            $table->string('logo_path')->nullable(); // public/vendor/lidervyg/logo.png
            $table->decimal('igv_rate', 5, 4)->default(0.18);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('empresa_configs');
    }
};
