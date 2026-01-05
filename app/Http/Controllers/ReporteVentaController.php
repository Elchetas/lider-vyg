<?php

namespace App\Http\Controllers;

use App\Models\Guia;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ReporteVentaController extends Controller
{
    public function index(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        // Ventas = guías emitidas (cada guía está asociada a una cotización aprobada)
        $guias = Guia::with(['cotizacion.cliente'])
            ->when($desde, fn ($q) =>
                $q->whereDate('fecha', '>=', $desde)
            )
            ->when($hasta, fn ($q) =>
                $q->whereDate('fecha', '<=', $hasta)
            )
            ->orderBy('fecha')
            ->get();

        $total = $guias->sum(fn ($g) => (float) optional($g->cotizacion)->total);

        return view('reportes.ventas', compact(
            'guias',
            'desde',
            'hasta',
            'total'
        ));
    }

    public function pdf(Request $request)
    {
        $desde = $request->desde;
        $hasta = $request->hasta;

        $guias = Guia::with('cotizacion.cliente')
            ->when($desde, fn ($q) =>
                $q->whereDate('fecha', '>=', $desde)
            )
            ->when($hasta, fn ($q) =>
                $q->whereDate('fecha', '<=', $hasta)
            )
            ->orderBy('fecha')
            ->get();

        $total = $guias->sum(fn ($g) => (float) optional($g->cotizacion)->total);

        $pdf = Pdf::loadView('reportes.ventas_pdf', compact(
            'guias',
            'desde',
            'hasta',
            'total'
        ));

        return $pdf->download('reporte-ventas.pdf');
    }
}
