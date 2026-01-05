<?php

namespace App\Http\Controllers;

use App\Exports\SunatExport;
use App\Models\Cotizacion;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class SunatReportController extends Controller
{
    public function index(Request $request)
    {
        $mes = $request->get('mes') ?? now()->format('Y-m');
        $start = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
        $end = (clone $start)->endOfMonth();

        $cotizaciones = Cotizacion::with('cliente','guia','pagos','detalles')
            ->whereBetween('fecha_emision', [$start->toDateString(), $end->toDateString()])
            ->orderBy('fecha_emision')
            ->orderBy('id')
            ->get();

        return view('reportes.sunat', compact('mes','cotizaciones'));
    }

    public function export(Request $request)
    {
        $mes = $request->get('mes') ?? now()->format('Y-m');
        return Excel::download(new SunatExport($mes), "SUNAT_{$mes}.xlsx");
    }
}
