<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmpresaConfig;

class EmpresaConfigSeeder extends Seeder
{
    public function run(): void
    {
        EmpresaConfig::updateOrCreate(
            ['id' => 1],
            [
                'nombre_empresa' => 'Líder V y G',
                'email' => 'empresalidervygsac@gmail.com',
                'telefono' => '905 461 423',
                'logo_path' => 'public/vendor/lidervyg/logo.png',
                'igv_rate' => 0.18,
            ]
        );
    }
}
