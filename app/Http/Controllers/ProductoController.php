<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\CatalogoProducto;
use App\Models\Cotizacion;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    /**
     * =====================================================
     * CATÁLOGO (CREAR / EDITAR COTIZACIÓN)
     * =====================================================
     */
    public function index(Request $request)
    {
        $clientes = Cliente::where('activo', 1)
            ->orderBy('nombre_cliente', 'asc')
            ->get();

        $q         = $request->get('q');
        $proveedor = $request->get('proveedor');

        $productosQuery = CatalogoProducto::where('activo', 1);

        if ($q) {
            $productosQuery->where(function ($w) use ($q) {
                $w->where('codigo', 'like', "%{$q}%")
                  ->orWhere('nombre_producto', 'like', "%{$q}%")
                  ->orWhere('descripcion', 'like', "%{$q}%");
            });
        }

        if ($proveedor) {
            $productosQuery->where('nombre_proveedor', $proveedor);
        }

        $productos = $productosQuery
            ->orderBy('codigo', 'asc')
            ->get();

        $proveedores = CatalogoProducto::where('activo', 1)
            ->whereNotNull('nombre_proveedor')
            ->where('nombre_proveedor', '<>', '')
            ->groupBy('nombre_proveedor')
            ->orderBy('nombre_proveedor', 'asc')
            ->pluck('nombre_proveedor');

        $cotizacionJs = null;

        if ($request->filled('cotizacion_id')) {
            $cotizacion = Cotizacion::with(['cliente', 'detalles'])
                ->findOrFail($request->cotizacion_id);

            if ($cotizacion->estado !== 'Pendiente') {
                return redirect()
                    ->route('cotizaciones.show', $cotizacion)
                    ->with('err', 'La cotización ya no se puede editar');
            }

            $cotizacionJs = [
                'id'            => $cotizacion->id,
                'numero'        => $cotizacion->numero,
                'cliente_id'    => $cotizacion->cliente_id,
                'tipo_precio'   => $cotizacion->tipo_precio ?? 'obra',
                'moneda'        => $cotizacion->moneda,
                'afecto_igv'    => (bool) $cotizacion->afecto_igv,
                'observaciones' => $cotizacion->observaciones,
                'detalles'      => $cotizacion->detalles->map(function ($d) {
                    return [
                        'producto_id'     => (string) $d->producto_id,
                        'codigo'          => $d->codigo,
                        'descripcion'     => $d->descripcion,
                        'cantidad'        => (int) $d->cantidad,
                        'precio_unitario' => (float) $d->precio_unitario,
                        'observaciones'   => $d->observaciones,
                    ];
                })->values(),
            ];
        }

        return view('productos.catalogo', compact(
            'clientes',
            'productos',
            'proveedores',
            'q',
            'proveedor',
            'cotizacionJs'
        ));
    }

    /**
     * =====================================================
     * ADMINISTRACIÓN
     * =====================================================
     */
    public function adminIndex()
    {
        $productos = CatalogoProducto::orderBy('codigo', 'asc')->paginate(15);
        return view('productos.admin_index', compact('productos'));
    }

    public function create()
    {
        return view('productos.create');
    }

    /**
     * =====================================================
     * GUARDAR PRODUCTO
     * =====================================================
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'codigo'           => 'required|string|max:255',
            'nombre_producto'  => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'precio_obra'      => 'required|numeric|min:0',
            'precio_edificio'  => 'required|numeric|min:0',
            'precio_proveedor' => 'nullable|numeric|min:0',
            'cantidad'         => 'nullable|integer|min:0',
            'nombre_proveedor' => 'nullable|string|max:255',
            'observaciones'    => 'nullable|string',
            'activo'           => 'nullable|boolean',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        // legacy
        $data['precio']      = $data['precio_obra'];
        $data['precio_prov'] = $data['precio_proveedor'] ?? 0;
        $data['proveedor']   = $data['nombre_proveedor'] ?? null;

        // 🔽 GUARDAR IMAGEN
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombre = $data['codigo'] . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('productos'), $nombre);
            $data['imagen'] = $nombre;
        }

        CatalogoProducto::create($data);

        return redirect()
            ->route('productos.admin.index')
            ->with('ok', 'Producto creado correctamente');
    }

    public function edit(CatalogoProducto $producto)
    {
        return view('productos.edit', compact('producto'));
    }

    /**
     * =====================================================
     * ACTUALIZAR PRODUCTO
     * =====================================================
     */
    public function update(Request $request, CatalogoProducto $producto)
    {
        $data = $request->validate([
            'codigo'           => 'required|string|max:255',
            'nombre_producto'  => 'required|string|max:255',
            'descripcion'      => 'nullable|string',
            'precio_obra'      => 'required|numeric|min:0',
            'precio_edificio'  => 'required|numeric|min:0',
            'precio_proveedor' => 'nullable|numeric|min:0',
            'cantidad'         => 'nullable|integer|min:0',
            'nombre_proveedor' => 'nullable|string|max:255',
            'observaciones'    => 'nullable|string',
            'activo'           => 'nullable|boolean',
            'imagen'           => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        $data['activo'] = $request->boolean('activo', true);

        // legacy
        $data['precio']      = $data['precio_obra'];
        $data['precio_prov'] = $data['precio_proveedor'] ?? $producto->precio_prov ?? 0;
        $data['proveedor']   = $data['nombre_proveedor'] ?? $producto->proveedor;

        // 🔽 ACTUALIZAR IMAGEN
        if ($request->hasFile('imagen')) {
            $file = $request->file('imagen');
            $nombre = $data['codigo'] . '.' . $file->getClientOriginalExtension();

            if ($producto->imagen) {
                $old = public_path('productos/' . $producto->imagen);
                if (is_file($old)) {
                    unlink($old);
                }
            }

            $file->move(public_path('productos'), $nombre);
            $data['imagen'] = $nombre;
        }

        $producto->update($data);

        return redirect()
            ->route('productos.admin.index')
            ->with('ok', 'Producto actualizado correctamente');
    }

    public function destroy(CatalogoProducto $producto)
    {
        if ($producto->imagen) {
            $img = public_path('productos/' . $producto->imagen);
            if (is_file($img)) {
                unlink($img);
            }
        }

        $producto->delete();

        return redirect()
            ->route('productos.admin.index')
            ->with('ok', 'Producto eliminado');
    }
}
