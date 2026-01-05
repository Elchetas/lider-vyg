<?php

namespace App\Imports;

use App\Models\Cliente;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ClientesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        return new Cliente([
            'nombre_cliente'        => $row['nombre_cliente'] ?? null,
            'direccion'             => $row['direccion'] ?? null,
            'lugar'                 => $row['lugar'] ?? null,
            'nombre_administrador'  => $row['nombre_administrador'] ?? null,
            'observacion'           => $row['observacion'] ?? null,
            'unidad_inmobiliaria'   => $row['unidad_inmobiliaria'] ?? null,
            'tipo_comprobante'      => $row['tipo_comprobante'] ?? null,
            'nombre_factura'        => $row['nombre_factura'] ?? null,
            'ruc'                   => $row['ruc'] ?? null,
            'activo'                => 1,
        ]);
    }
}
