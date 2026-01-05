<?php

namespace App\Services;

class PricingService
{
    public function splitIgv(float $totalInclIgv, float $igvRate = 0.18): array
    {
        $base = $totalInclIgv / (1.0 + $igvRate);
        // redondeo a 2 decimales
        $base = round($base, 2);
        $igv = round($totalInclIgv - $base, 2);
        return [$base, $igv];
    }

    /**
     * Recibe las líneas (total_linea finales) y retorna [subtotal, igv, total].
     */
    public function totals(array $lineTotals, bool $afectoIgv, float $igvRate = 0.18): array
    {
        $total = round(array_sum($lineTotals), 2);
        if (!$afectoIgv) {
            return [$total, 0.0, $total];
        }

        $subtotal = 0.0;
        $igv = 0.0;
        foreach ($lineTotals as $t) {
            [$b, $i] = $this->splitIgv((float)$t, $igvRate);
            $subtotal += $b;
            $igv += $i;
        }
        $subtotal = round($subtotal, 2);
        $igv = round($igv, 2);
        // asegurar consistencia
        $total = round($subtotal + $igv, 2);
        return [$subtotal, $igv, $total];
    }
}
