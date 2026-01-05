<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('sunat_configs', function (Blueprint $table) {
            $table->id();
            $table->string('ruc')->nullable();
            $table->string('razon_social')->nullable();
            $table->string('sol_user')->nullable();
            // Encriptado con Crypt
            $table->text('sol_password')->nullable();
            $table->string('fe_wsdl')->nullable();
            $table->string('gre_wsdl')->nullable();
            $table->string('cert_path')->nullable();
            // Encriptado con Crypt
            $table->text('cert_password')->nullable();
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sunat_configs');
    }
};
