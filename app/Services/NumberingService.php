<?php

namespace App\Services;

use App\Models\Cotizacion;
use Carbon\Carbon;

class NumberingService
{
    /**
     * Genera el siguiente número con formato:
     * MM + 3 dígitos (ej: 12001)
     */
    public function nextCotizacionNumero(?Carbon $date = null): string
    {
        $date = $date ?? Carbon::now();
        $mm = $date->format('m');
        $prefix = $mm; // '01'..'12'

        // buscar máximo del mes actual
        $max = Cotizacion::where('numero', 'like', $prefix.'%')->max('numero');
        $nextSeq = 1;
        if ($max) {
            $seq = intval(substr($max, 2, 3));
            $nextSeq = $seq + 1;
        }
        return $prefix . str_pad((string)$nextSeq, 3, '0', STR_PAD_LEFT);
    }
}
