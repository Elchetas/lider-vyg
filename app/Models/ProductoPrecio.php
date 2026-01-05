<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductoPrecio extends Model
{
    protected $fillable = [
        'catalogo_producto_id',
        'unidad_inmobiliaria_id',
        'precio'
    ];
}
