<?php

namespace App\Exports;

use App\Models\Cotizacion;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CotizacionSunatExport implements FromArray, WithHeadings
{
    protected Cotizacion $cotizacion;

    public function __construct(Cotizacion $cotizacion)
    {
        $this->cotizacion = $cotizacion;
    }

    public function headings(): array
    {
        return [
            'Fecha',
            'RUC Cliente',
            'Cliente',
            'Código',
            'Descripción',
            'Cantidad',
            'Precio Unitario SIN IGV',
            'Subtotal SIN IGV',
        ];
    }

    public function array(): array
    {
        $rows = [];
        $afectoIgv = $this->cotizacion->afecto_igv;
        $igvRate = 1.18;

        foreach ($this->cotizacion->detalles as $d) {

            $precioBase = $afectoIgv
                ? round($d->precio_unitario / $igvRate, 6)
                : round($d->precio_unitario, 6);

            $subtotalBase = round($precioBase * $d->cantidad, 2);

            $rows[] = [
                $this->cotizacion->fecha_emision->format('Y-m-d'),
                $this->cotizacion->cliente->ruc,
                $this->cotizacion->cliente->nombre_cliente,
                $d->codigo,
                $d->descripcion,
                $d->cantidad,
                $precioBase,
                $subtotalBase,
            ];
        }

        return $rows;
    }
}
