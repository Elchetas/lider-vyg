<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VentaMensual extends Model
{
    use HasFactory;

    protected $table = 'ventas_mensuales';

    protected $fillable = ['mes','subtotal','igv','total'];
}
