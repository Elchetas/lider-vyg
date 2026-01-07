<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoProducto extends Model
{
    protected $table = 'catalogo_productos';

    /**
     * Campos oficiales + legacy
     */
    protected $fillable = [
        'codigo',
        'nombre_producto',
        'descripcion',
        'precio_obra',
        'precio_edificio',
        'cantidad',
        'precio_proveedor',
        'imagen',
        'nombre_proveedor',
        'observaciones',
        'activo',

        // legacy (NO BORRAR)
        'precio',
        'precio_prov',
        'proveedor',
        'imagen_path',
    ];

    protected $casts = [
        'activo'            => 'boolean',
        'precio_obra'       => 'decimal:2',
        'precio_edificio'   => 'decimal:2',
        'precio_proveedor'  => 'decimal:2',
        'precio'            => 'decimal:2',
        'precio_prov'       => 'decimal:2',
        'cantidad'          => 'integer',
    ];

    /**
     * =====================================================
     * URL FINAL DE IMAGEN
     * =====================================================
     * Prioridad:
     * 1) columna imagen
     * 2) columna legacy imagen_path
     * 3) búsqueda por código en /public/productos
     */
    public function getImagenUrlAttribute(): ?string
    {
        $img = $this->imagen ?: $this->imagen_path;

        // helper: buscar imagen en /public/productos
        $buscar = function (string $base): ?string {
            $base = trim($base);
            if ($base === '') return null;

            $exts = ['jpg', 'jpeg', 'png', 'webp'];

            foreach ($exts as $ext) {
                $rel = "productos/{$base}.{$ext}";
                if (is_file(public_path($rel))) {
                    return '/' . $rel;
                }
            }
            return null;
        };

        if ($img) {
            $img = trim((string) $img);

            // URL absoluta o ruta ya válida
            if (
                str_starts_with($img, 'http://') ||
                str_starts_with($img, 'https://') ||
                str_starts_with($img, '/')
            ) {
                return $img;
            }

            // Ej: productos/D0001.jpg
            if (str_contains($img, '/')) {
                $full = public_path(ltrim($img, '/'));
                if (is_file($full)) {
                    return '/' . ltrim($img, '/');
                }
            }

            // Ej: D0001.jpg
            if (str_contains($img, '.')) {
                $full = public_path('productos/' . $img);
                if (is_file($full)) {
                    return '/productos/' . $img;
                }
            }

            // Ej: D0001 (sin extensión)
            $found = $buscar($img);
            if ($found) return $found;
        }

        // Fallback final: buscar por código
        if ($this->codigo) {
            $found = $buscar($this->codigo);
            if ($found) return $found;
        }

        return null;
    }

    /**
     * =====================================================
     * PRECIO SEGÚN TIPO
     * =====================================================
     */
    public function precioSegunTipo(string $tipo): float
    {
        $tipo = strtolower(trim($tipo));

        if ($tipo === 'edificio') {
            return (float) ($this->precio_edificio ?? $this->precio ?? 0);
        }

        // default: obra
        return (float) ($this->precio_obra ?? $this->precio ?? 0);
    }
}
