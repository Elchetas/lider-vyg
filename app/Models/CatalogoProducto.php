<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CatalogoProducto extends Model
{
    protected $table = 'catalogo_productos';

    /**
     * Catálogo (estructura oficial solicitada por el usuario)
     *
     * codigo, nombre_producto, descripcion, precio_obra, precio_edificio,
     * cantidad, precio_proveedor, imagen, nombre_proveedor, observaciones
     *
     * Nota: se mantienen campos legacy (precio, precio_prov, proveedor, imagen_path)
     * para compatibilidad si existen datos antiguos.
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

        // legacy
        'precio',
        'precio_prov',
        'proveedor',
        'imagen_path',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'precio_obra' => 'decimal:2',
        'precio_edificio' => 'decimal:2',
        'precio_proveedor' => 'decimal:2',
        'precio' => 'decimal:2',
        'precio_prov' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    /**
     * URL de imagen (prioriza columna oficial "imagen", luego legacy "imagen_path")
     */
    public function getImagenUrlAttribute(): ?string
    {
        // 1) Columna oficial "imagen" (puede venir como nombre o ruta)
        $img = $this->imagen ?: $this->imagen_path;

        // Helper para buscar en /public/productos por nombre sin extensión
        $findInPublicProductos = function (string $base): ?string {
            $base = trim($base);
            if ($base === '') return null;

            $candidates = [
                "productos/{$base}.jpg",
                "productos/{$base}.jpeg",
                "productos/{$base}.png",
                "productos/{$base}.webp",
            ];

            foreach ($candidates as $rel) {
                $full = public_path($rel);
                if (is_file($full)) {
                    return '/' . $rel;
                }
            }
            return null;
        };

        // Si viene URL absoluta o ruta ya armada, respetar
        if ($img) {
            $img = trim((string)$img);

            if (str_starts_with($img, 'http://') || str_starts_with($img, 'https://') || str_starts_with($img, '/')) {
                return $img;
            }

            // Si ya incluye carpeta (ej: productos/D0001.jpg), servir desde /public
            if (str_contains($img, '/')) {
                return '/' . ltrim($img, '/');
            }

            // Si viene con extensión (ej: D0001.jpg) lo intentamos dentro de /productos
            if (str_contains($img, '.')) {
                $full = public_path('productos/' . $img);
                if (is_file($full)) {
                    return '/productos/' . $img;
                }
            }

            // Si viene sin extensión, buscarlo en /public/productos
            $found = $findInPublicProductos($img);
            if ($found) return $found;
        }

        // 2) Fallback fuerte: buscar por CÓDIGO (public/productos/D0001.jpg/png/...)
        if ($this->codigo) {
            $found = $findInPublicProductos($this->codigo);
            if ($found) return $found;
        }

        return null;
    }

    /**
     * Precio unitario según tipo (obra/edificio)
     */
    public function precioSegunTipo(string $tipo): float
    {
        $tipo = strtolower(trim($tipo));
        if ($tipo === 'edificio') {
            return (float)($this->precio_edificio ?? $this->precio ?? 0);
        }
        // default obra
        return (float)($this->precio_obra ?? $this->precio ?? 0);
    }
}
