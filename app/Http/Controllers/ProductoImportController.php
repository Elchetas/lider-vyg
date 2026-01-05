<?php

namespace App\Http\Controllers;

use App\Models\CatalogoProducto;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class ProductoImportController extends Controller
{
    public function form()
    {
        return view('productos.import');
    }

    public function import(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:xlsx,xls,csv,txt'
        ]);

        // 🔥 LIMPIAR TODOS LOS PRODUCTOS
        CatalogoProducto::truncate();

        $file = $request->file('archivo');
        $ext = strtolower($file->getClientOriginalExtension());

        $rows = [];
        if ($ext === 'csv' || $ext === 'txt') {
            $path = $file->storeAs('imports', 'catalogo_productos_import.csv');
            $full = storage_path('app/'.$path);
            $h = fopen($full, 'r');
            $headers = fgetcsv($h);
            while (($row = fgetcsv($h)) !== false) {
                if (empty(array_filter($row))) continue;
                $rows[] = array_combine($headers, $row);
            }
            fclose($h);
        } else {
            $data = Excel::toArray([], $file);
            $filas = $data[0] ?? [];
            unset($filas[0]); // headers
            foreach ($filas as $fila) {
                if (empty(array_filter($fila))) continue;
                // Soportamos el orden oficial del usuario:
                // codigo,nombre_producto,descripcion,precio_obra,precio_edificio,cantidad,precio_proveedor,imagen,nombre_proveedor,observaciones
                $rows[] = [
                    'codigo' => $fila[0] ?? null,
                    'nombre_producto' => $fila[1] ?? null,
                    'descripcion' => $fila[2] ?? null,
                    'precio_obra' => $fila[3] ?? 0,
                    'precio_edificio' => $fila[4] ?? 0,
                    'cantidad' => $fila[5] ?? 0,
                    'precio_proveedor' => $fila[6] ?? 0,
                    'imagen' => $fila[7] ?? null,
                    'nombre_proveedor' => $fila[8] ?? null,
                    'observaciones' => $fila[9] ?? null,
                ];
            }
        }

        foreach ($rows as $r) {
           CatalogoProducto::create([
                'codigo'            => $r['codigo'] ?? null,
                'nombre_producto'   => $r['nombre_producto'] ?? ($r['nombre'] ?? null),
                'descripcion'       => $r['descripcion'] ?? null,

                'precio_obra'       => (float)($r['precio_obra'] ?? 0),
                'precio_edificio'   => (float)($r['precio_edificio'] ?? 0),
                'precio_proveedor'  => (float)($r['precio_proveedor'] ?? 0),

                'cantidad'          => (int)($r['cantidad'] ?? 0),
                'imagen'            => $r['imagen'] ?? null,
                'nombre_proveedor'  => $r['nombre_proveedor'] ?? null,
                'observaciones'     => $r['observaciones'] ?? null,

                'proveedor'         => $r['nombre_proveedor'] ?? null,
                'activo'            => 1,
            ]);

        }

        return redirect()
            ->route('productos.catalogo')
            ->with('success', 'Productos importados correctamente');
    }
}
