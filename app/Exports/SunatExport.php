<?php

namespace App\Exports;

use App\Models\Cotizacion;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class SunatExport implements FromCollection, WithHeadings
{
    public function __construct(private string $mes) {}

    public function headings(): array
    {
        return [
            'Nº','FECHA EMISIÓN','TIPO DE DOC.','SERIE','NÚMERO','GUIA','MONEDA','RUC','RAZON SOCIAL','ADM','PROYECTO/EDIFICIO','DETALLE DE ANULACION DE COMPROBANTE','DESCRIPCION (SERVICIO O VENTA DE PRODUCTO)','SUBTOTAL','IGV','TOTAL','SUBTOTAL','IGV','TOTAL','TIPO DE CAMBIO','30 DIAS','90 DIAS','RENTA','FACTORING','FECHA','MEDIO DE PAGO','N. OP','BANCO','AFECTO/NO AFECTO','%','ESTADO','ESTADO DEUDA','ANULACION DE COMPROBANTE','MONTO VENTA MES','N° COTIZACIÓN'
        ];
    }

    public function collection(): Collection
    {
        $start = Carbon::createFromFormat('Y-m', $this->mes)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $cotizaciones = Cotizacion::with('cliente','guia','pagos','detalles')
            ->whereBetween('fecha_emision', [$start->toDateString(), $end->toDateString()])
            ->orderBy('fecha_emision')
            ->orderBy('id')
            ->get();

        $rows = [];
        $n = 1;

        // Total de ventas del mes (columna MONTO VENTA MES)
        $montoVentaMes = round((float)$cotizaciones->sum('total'), 2);

        foreach ($cotizaciones as $cot) {
            $cliente = $cot->cliente;
            $pago = $cot->pagos->sortByDesc('id')->first();

            $descripcion = $cot->detalles()->count() ? $cot->detalles->pluck('descripcion')->implode(' | ') : '';

            $rows[] = [
                $n++,
                optional($cot->fecha_emision)->format('d/m/Y'),
                $cliente?->tipo_comprobante ?? '',
                '', // SERIE (si luego usas facturador, se puede integrar)
                $cot->numero,
                $cot->guia?->numero ?? '',
                $cot->moneda,
                $cliente?->ruc ?? '',
                $cliente?->nombre_factura ?? '',
                $cliente?->nombre_administrador ?? '',
                $cliente?->unidad_inmobiliaria ?? '',
                '', // detalle anulación (manual si aplica)
                $descripcion,
                $cot->subtotal,
                $cot->igv,
                $cot->total,
                $cot->subtotal,
                $cot->igv,
                $cot->total,
                '', // tipo de cambio
                '', // 30 dias
                '', // 90 dias
                '', // renta
                '', // factoring
                $pago?->fecha?->format('d/m/Y') ?? '',
                $pago?->medio_pago ?? '',
                $pago?->numero_op ?? '',
                $pago?->banco ?? '',
                $cot->afecto_igv ? 'AFECTO' : 'NO AFECTO',
                $cot->afecto_igv ? '18' : '0',
                $cot->estado,
                '', // estado deuda (si se implementa cuentas por cobrar)
                $cot->estado === 'Anulada' ? 'SI' : 'NO',
                $montoVentaMes,
                $cot->numero,
            ];
        }

        return collect($rows);
    }
}
