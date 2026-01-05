<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmpresaConfig extends Model
{
    use HasFactory;

    protected $table = 'empresa_configs';

    protected $fillable = ['nombre_empresa','email','telefono','ruc','direccion','logo_path','igv_rate'];
}
