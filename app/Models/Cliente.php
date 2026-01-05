<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cliente extends Model
{
    use HasFactory;

    protected $table = 'clientes';

   protected $fillable = [
    'nombre_cliente',
    'direccion',
    'lugar',
    'nombre_administrador',
    'observacion',
    'unidad_inmobiliaria',
    'tipo_comprobante',
    'nombre_factura',
    'ruc',
    'activo',
];



    public function cotizaciones()
    {
        return $this->hasMany(Cotizacion::class, 'cliente_id');
    }
}
