<?php

namespace App\Services;

use App\Models\Cotizacion;
use App\Models\VentaMensual;
use Carbon\Carbon;

class VentasMensualesService
{
    public function recomputeForMonth(string $mes): void
    {
        // mes: YYYY-MM
        $start = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $rows = Cotizacion::where('estado', 'Aprobada')
            ->whereBetween('fecha_emision', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('SUM(subtotal) as s_subtotal, SUM(igv) as s_igv, SUM(total) as s_total')
            ->first();

        $subtotal = round((float)($rows->s_subtotal ?? 0), 2);
        $igv = round((float)($rows->s_igv ?? 0), 2);
        $total = round((float)($rows->s_total ?? 0), 2);

        VentaMensual::updateOrCreate(
            ['mes' => $mes],
            ['subtotal' => $subtotal, 'igv' => $igv, 'total' => $total]
        );
    }
}
