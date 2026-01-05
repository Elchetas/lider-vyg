<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Guia extends Model
{
    use HasFactory;

    protected $table = 'guias';

    protected $casts = [
        'fecha' => 'date',
    ];

    protected $fillable = ['cotizacion_id','numero','fecha','estado'];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class, 'cotizacion_id');
    }
}
