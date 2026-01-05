<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    use HasFactory;

    protected $table = 'cotizaciones';

    protected $casts = [
        'fecha_emision' => 'date',
        'afecto_igv' => 'boolean',
    ];

    protected $fillable = [
        'numero','fecha_emision','cliente_id','tipo_precio','moneda','afecto_igv','estado','subtotal','igv','total','observaciones','creado_por'
    ];

    public function cliente()
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function detalles()
    {
        return $this->hasMany(CotizacionDetalle::class, 'cotizacion_id');
    }

    public function guia()
    {
        return $this->hasOne(Guia::class, 'cotizacion_id');
    }

    public function pagos()
    {
        return $this->hasMany(Pago::class, 'cotizacion_id');
    }
}
