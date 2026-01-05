<?php

namespace App\Http\Controllers;

use App\Models\CotizacionDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteClientesMensualController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes') ?? now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $rows = CotizacionDetalle::query()
            ->join('cotizaciones', 'cotizaciones.id', '=', 'cotizacion_detalles.cotizacion_id')
            ->join('clientes', 'clientes.id', '=', 'cotizaciones.cliente_id')
            ->where('cotizaciones.estado', 'Aprobada')
            ->whereBetween('cotizaciones.fecha_emision', [$start->toDateString(), $end->toDateString()])
            ->selectRaw('clientes.id as cliente_id, clientes.nombre_cliente, cotizacion_detalles.codigo, cotizacion_detalles.descripcion, SUM(cotizacion_detalles.cantidad) as cantidad_total')
            ->groupBy('clientes.id', 'clientes.nombre_cliente', 'cotizacion_detalles.codigo', 'cotizacion_detalles.descripcion')
            ->orderBy('clientes.nombre_cliente')
            ->orderBy('cotizacion_detalles.codigo')
            ->get();

        // Agrupar para la vista
        $clientes = [];
        foreach ($rows as $r) {
            $cid = (int)$r->cliente_id;
            if (!isset($clientes[$cid])) {
                $clientes[$cid] = [
                    'cliente_id' => $cid,
                    'nombre_cliente' => $r->nombre_cliente,
                    'items' => [],
                    'total_cantidad' => 0,
                ];
            }
            $clientes[$cid]['items'][] = [
                'codigo' => $r->codigo,
                'descripcion' => $r->descripcion,
                'cantidad' => (int)$r->cantidad_total,
            ];
            $clientes[$cid]['total_cantidad'] += (int)$r->cantidad_total;
        }

        $clientes = array_values($clientes);

        return view('reportes.clientes_mensual', compact('mes', 'clientes'));
    }
}
