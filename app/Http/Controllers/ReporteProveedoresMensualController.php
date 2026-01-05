<?php

namespace App\Http\Controllers;

use App\Models\CotizacionDetalle;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ReporteProveedoresMensualController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes') ?? now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $rows = CotizacionDetalle::query()
            ->join('cotizaciones', 'cotizaciones.id', '=', 'cotizacion_detalles.cotizacion_id')
            ->join('catalogo_productos', 'catalogo_productos.id', '=', 'cotizacion_detalles.producto_id')
            ->where('cotizaciones.estado', 'Aprobada')
            ->whereBetween('cotizaciones.fecha_emision', [$start->toDateString(), $end->toDateString()])
            ->selectRaw("COALESCE(NULLIF(TRIM(catalogo_productos.nombre_proveedor),''), 'SIN PROVEEDOR') as proveedor, cotizacion_detalles.codigo, cotizacion_detalles.descripcion, SUM(cotizacion_detalles.cantidad) as cantidad_total, AVG(COALESCE(catalogo_productos.precio_proveedor,0)) as precio_proveedor")
            ->groupBy('proveedor', 'cotizacion_detalles.codigo', 'cotizacion_detalles.descripcion')
            ->orderBy('proveedor')
            ->orderBy('cotizacion_detalles.codigo')
            ->get();

        $proveedores = [];
        foreach ($rows as $r) {
            $prov = (string)$r->proveedor;
            if (!isset($proveedores[$prov])) {
                $proveedores[$prov] = [
                    'proveedor' => $prov,
                    'items' => [],
                    'total_pagar' => 0,
                    'total_cantidad' => 0,
                ];
            }
            $precioProv = (float)$r->precio_proveedor;
            $qty = (int)$r->cantidad_total;
            $totalLinea = round($precioProv * $qty, 2);

            $proveedores[$prov]['items'][] = [
                'codigo' => $r->codigo,
                'descripcion' => $r->descripcion,
                'cantidad' => $qty,
                'precio_proveedor' => $precioProv,
                'total' => $totalLinea,
            ];
            $proveedores[$prov]['total_pagar'] += $totalLinea;
            $proveedores[$prov]['total_cantidad'] += $qty;
        }

        $proveedores = array_values($proveedores);

        return view('reportes.proveedores_mensual', compact('mes', 'proveedores'));
    }
}
