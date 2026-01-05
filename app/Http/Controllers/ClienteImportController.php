<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class ClienteImportController extends Controller
{
    public function form()
    {
        return view('clientes.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls'
        ]);

        $data = Excel::toArray([], $request->file('archivo'));
        $filas = $data[0];

        // eliminar encabezados
        unset($filas[0]);

        foreach ($filas as $fila) {

            // saltar filas vacías
            if (empty(array_filter($fila))) {
                continue;
            }

            Cliente::create([
                'nombre_cliente'        => $fila[0] ?? null, // clientes
                'direccion'             => $fila[1] ?? null,
                'lugar'                 => $fila[2] ?? null,
                'nombre_administrador'  => $fila[3] ?? null, // contacto
                'observacion'           => $fila[4] ?? null,
                'unidad_inmobiliaria'   => $fila[5] ?? null,
                'tipo_comprobante'      => $fila[6] ?? 'Factura',
                'nombre_factura'        => $fila[7] ?? null,
                'ruc'                   => $fila[8] ?? null,
                'activo'                => true,
            ]);
        }

        return redirect()
            ->route('clientes.index')
            ->with('success', 'Clientes importados correctamente');
    }
}
