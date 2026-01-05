<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CatalogoProducto;
use App\Models\Cotizacion;
use App\Models\CotizacionDetalle;
use App\Models\EmpresaConfig;
use App\Services\NumberingService;
use App\Services\PricingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class CotizacionController extends Controller
{
    /* =========================
     * LISTADO
     * ========================= */
    public function index()
    {
        $cotizaciones = Cotizacion::with('cliente')
            ->orderBy('id', 'desc')
            ->paginate(15);

        return view('cotizaciones.index', compact('cotizaciones'));
    }

    /* =========================
     * EDITAR (REDIRIGE AL CATÁLOGO)
     * ========================= */
    public function edit(Cotizacion $cotizacion)
    {
        // Solo editable si está Pendiente
        if ($cotizacion->estado !== 'Pendiente') {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'La cotización no puede modificarse porque ya no está Pendiente.');
        }

        // Si ya tiene guía, tampoco se edita
        if ($cotizacion->guia()->exists()) {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'La cotización no puede modificarse porque ya tiene una guía.');
        }

        // 👉 SIEMPRE volver al catálogo
        return redirect()
            ->route('productos.catalogo', ['cotizacion_id' => $cotizacion->id]);
    }

    /* =========================
     * VER
     * ========================= */
    public function show(Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'detalles', 'guia']);
        return view('cotizaciones.show', compact('cotizacion'));
    }

    /* =========================
     * CREAR DESDE CATÁLOGO
     * ========================= */
    public function storeFromCatalog(
        Request $request,
        NumberingService $numbering,
        PricingService $pricing
    ) {
        $data = $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'tipo_precio'       => 'required|in:obra,edificio',
            'moneda'            => 'required|in:PEN,USD',
            'afecto_igv'        => 'required|boolean',
            'observaciones'     => 'nullable|string',

            'items'                     => 'required|array|min:1',
            'items.*.producto_id'        => 'required|exists:catalogo_productos,id',
            'items.*.cantidad'           => 'required|integer|min:1',
            'items.*.observaciones'      => 'nullable|string',
        ]);

        return DB::transaction(function () use ($data, $numbering, $pricing, $request) {

            $cotizacion = Cotizacion::create([
                'numero'        => $numbering->nextCotizacionNumero(now()),
                'fecha_emision' => now()->toDateString(),
                'cliente_id'    => $data['cliente_id'],
                'tipo_precio'   => $data['tipo_precio'],
                'moneda'        => $data['moneda'],
                'afecto_igv'    => (bool) $data['afecto_igv'],
                'estado'        => 'Pendiente',
                'observaciones' => $data['observaciones'] ?? null,
                'creado_por'    => $request->user()?->id,
            ]);

            $this->syncDetallesAndTotals($cotizacion, $data, $pricing);

            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('ok', 'Cotización creada desde el catálogo');
        });
    }

    /* =========================
     * ACTUALIZAR DESDE CATÁLOGO
     * ========================= */
    public function updateFromCatalog(
        Request $request,
        Cotizacion $cotizacion,
        PricingService $pricing
    ) {
        if ($cotizacion->estado !== 'Pendiente') {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'No se puede editar una cotización que no esté Pendiente.');
        }

        if ($cotizacion->guia()->exists()) {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'No se puede editar porque la cotización ya tiene una guía.');
        }

        $data = $request->validate([
            'cliente_id'        => 'required|exists:clientes,id',
            'tipo_precio'       => 'required|in:obra,edificio',
            'moneda'            => 'required|in:PEN,USD',
            'afecto_igv'        => 'required|boolean',
            'observaciones'     => 'nullable|string',

            'items'                     => 'required|array|min:1',
            'items.*.producto_id'        => 'required|exists:catalogo_productos,id',
            'items.*.cantidad'           => 'required|integer|min:1',
            'items.*.observaciones'      => 'nullable|string',
        ]);

        return DB::transaction(function () use ($cotizacion, $data, $pricing) {

            $cotizacion->update([
                'cliente_id'    => $data['cliente_id'],
                'tipo_precio'   => $data['tipo_precio'],
                'moneda'        => $data['moneda'],
                'afecto_igv'    => (bool) $data['afecto_igv'],
                'observaciones' => $data['observaciones'] ?? null,
            ]);

            $this->syncDetallesAndTotals($cotizacion, $data, $pricing);

            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('ok', 'Cotización actualizada desde el catálogo');
        });
    }

    /* =========================
     * UPDATE REST (REDIRIGE)
     * ========================= */
    public function update(Request $request, Cotizacion $cotizacion, PricingService $pricing)
    {
        // Cualquier update REST se fuerza al flujo de catálogo
        return $this->updateFromCatalog($request, $cotizacion, $pricing);
    }

    /* =========================
     * ELIMINAR
     * ========================= */
    public function destroy(Cotizacion $cotizacion)
    {
        if ($cotizacion->estado !== 'Pendiente') {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'No se puede eliminar una cotización que no esté Pendiente.');
        }

        if ($cotizacion->guia()->exists()) {
            return redirect()
                ->route('cotizaciones.show', $cotizacion)
                ->with('err', 'No se puede eliminar: la cotización ya tiene una guía asociada.');
        }

        DB::transaction(function () use ($cotizacion) {
            $cotizacion->detalles()->delete();
            $cotizacion->delete();
        });

        return redirect()
            ->route('cotizaciones.index')
            ->with('ok', 'Cotización eliminada.');
    }

    /* =========================
     * PDF
     * ========================= */
    public function pdf(Request $request, Cotizacion $cotizacion)
    {
        $cotizacion->load(['cliente', 'detalles']);
        $empresa = EmpresaConfig::first();

        $pdf = Pdf::loadView('cotizaciones.pdf', [
            'cotizacion' => $cotizacion,
            'empresa'    => $empresa,
        ])->setPaper('a4');

        $filename = 'COT_' . $cotizacion->numero . '.pdf';

        // Si se pide inline=1 => vista previa en navegador (iframe/modal)
        if ($request->boolean('inline')) {
           return response()->download($path, $nombreArchivo, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$nombreArchivo.'"',
        ]);
        }

        return $pdf->download($filename);
}

    /* =========================
     * HELPERS
     * ========================= */
    private function syncDetallesAndTotals(
        Cotizacion $cotizacion,
        array $data,
        PricingService $pricing
    ): void {
        $cotizacion->detalles()->delete();

        $productoIds = collect($data['items'])->pluck('producto_id')->unique()->values();
        $productos = CatalogoProducto::whereIn('id', $productoIds)->get()->keyBy('id');

        $lineTotals = [];

        foreach ($data['items'] as $item) {
            $prod = $productos[$item['producto_id']] ?? null;
            if (!$prod) continue;

            $precioUnitario = $data['tipo_precio'] === 'edificio'
                ? (float) $prod->precio_edificio
                : (float) $prod->precio_obra;

            $cantidad = (int) $item['cantidad'];
            $totalLinea = round($cantidad * $precioUnitario, 2);
            $lineTotals[] = $totalLinea;

            CotizacionDetalle::create([
                'cotizacion_id'   => $cotizacion->id,
                'producto_id'     => $prod->id,
                'codigo'          => $prod->codigo,
                'descripcion'     => trim($prod->nombre_producto . ' ' . ($prod->descripcion ?? '')),
                'cantidad'        => $cantidad,
                'precio_unitario' => $precioUnitario,
                'total_linea'     => $totalLinea,
                'observaciones'   => $item['observaciones'] ?? null,
            ]);
        }

        $igvRate = (float) (optional(EmpresaConfig::first())->igv_rate ?? 0.18);

        [$subtotal, $igv, $total] = $pricing->totals(
            $lineTotals,
            (bool) $cotizacion->afecto_igv,
            $igvRate
        );

        $cotizacion->update(compact('subtotal', 'igv', 'total'));
    }
}
