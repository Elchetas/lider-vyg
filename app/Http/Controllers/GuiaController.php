<?php

namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Guia;
use App\Models\EmpresaConfig;
use App\Services\VentasMensualesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;

// Excel SUNAT
use App\Exports\CotizacionSunatExport;
use Maatwebsite\Excel\Facades\Excel;

class GuiaController extends Controller
{
    /* =========================
     * LISTADO DE GUÍAS
     * ========================= */
    public function index()
    {
        $guias = Guia::with('cotizacion.cliente')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('guias.index', compact('guias'));
    }

    /* =========================
     * VER GUÍA
     * ========================= */
    public function show(Guia $guia)
    {
        $guia->load(['cotizacion.cliente', 'cotizacion.detalles']);
        return view('guias.show', compact('guia'));
    }

    /* =========================
     * GENERAR GUÍA DESDE COTIZACIÓN
     * ========================= */
    public function generar(
        Request $request,
        Cotizacion $cotizacion,
        VentasMensualesService $mensual
    ) {
        // Solo cotizaciones pendientes
        if ($cotizacion->estado !== 'Pendiente') {
            return back()->with('err', 'Solo se puede generar guía desde una cotización pendiente.');
        }

        // Bloquear si ya tiene guía
        if ($cotizacion->guia) {
            return back()->with('err', 'Esta cotización ya tiene una guía generada.');
        }

        return DB::transaction(function () use ($cotizacion, $mensual) {

            // =========================
            // CREAR GUÍA
            // =========================
            $guia = Guia::create([
                'cotizacion_id' => $cotizacion->id,
                'numero'        => $cotizacion->numero,
                'fecha'         => $cotizacion->fecha_emision->copy()->addDay()->toDateString(),
                'estado'        => 'Emitida',
            ]);

            // =========================
            // APROBAR COTIZACIÓN
            // =========================
            $cotizacion->update([
                'estado' => 'Aprobada',
            ]);

            // =========================
            // RECOMPUTAR VENTAS MENSUALES
            // =========================
            $mes = $cotizacion->fecha_emision->format('Y-m');
            $mensual->recomputeForMonth($mes);

            // =========================
            // GENERAR EXCEL SUNAT
            // =========================
            $cotizacion->load(['cliente', 'detalles']);

            if (!Storage::disk('public')->exists('sunat')) {
                Storage::disk('public')->makeDirectory('sunat');
            }

            $nombreArchivo = 'FACT_' . $cotizacion->numero . '.xlsx';
            $ruta = 'sunat/' . $nombreArchivo;

            Excel::store(
                new CotizacionSunatExport($cotizacion),
                $ruta,
                'public'
            );

            return redirect()
                ->route('guias.show', $guia)
                ->with(
                    'ok',
                    'Guía generada correctamente. Cotización aprobada y Excel SUNAT creado.'
                );
        });
    }

    /* =========================
     * PDF DE GUÍA
     * ========================= */
    public function pdf(Request $request, Guia $guia)
    {
        $guia->load(['cotizacion.cliente', 'cotizacion.detalles']);
        $empresa = EmpresaConfig::first();

        $pdf = Pdf::loadView('guias.pdf', [
            'guia'    => $guia,
            'empresa' => $empresa,
        ])->setPaper('a4');

        $filename = 'GUIA_' . $guia->numero . '.pdf';
        // Vista previa deshabilitada: la Guía siempre se descarga como archivo.
        return $pdf->download($filename);
}

    /* =========================
     * ELIMINAR GUÍA
     * ========================= */
    public function destroy(Guia $guia, VentasMensualesService $mensual)
    {
        // Solo admin puede eliminar, mismo criterio que generar
        if (!auth()->user() || !auth()->user()->isAdmin()) {
            return redirect()->route('guias.show', $guia)
                ->with('err', 'No tiene permisos para eliminar guías.');
        }

        $guia->load(['cotizacion']);

        return DB::transaction(function () use ($guia, $mensual) {
            $cotizacion = $guia->cotizacion;

            // Eliminar Excel SUNAT si existe
            if ($cotizacion) {
                $archivo = 'sunat/FACT_' . $cotizacion->numero . '.xlsx';
                if (Storage::disk('public')->exists($archivo)) {
                    Storage::disk('public')->delete($archivo);
                }
            }

            // Eliminar guía y revertir estado de cotización
            $guia->delete();
            if ($cotizacion) {
                $cotizacion->update(['estado' => 'Pendiente']);
                // Recalcular ventas del mes donde estaba aprobada
                $mes = $cotizacion->fecha_emision->format('Y-m');
                $mensual->recomputeForMonth($mes);
            }

            return redirect()->route('cotizaciones.show', $cotizacion)
                ->with('ok', 'Guía eliminada y cotización devuelta a Pendiente.');
        });
    }
}
