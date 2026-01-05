<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: DejaVu Sans; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ccc; padding: 5px; }
        th { background: #f2f2f2; }
        .text-end { text-align: right; }
    </style>
</head>
<body>

<h3>Reporte de Ventas</h3>

@if($desde || $hasta)
<p>
    Periodo:
    {{ $desde ?? 'Inicio' }} -
    {{ $hasta ?? 'Hoy' }}
</p>
@endif

<table>
    <thead>
        <tr>
            <th># Guía</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th class="text-end">Total</th>
        </tr>
    </thead>
    <tbody>
        @foreach($guias as $g)
            <tr>
                <td>{{ $g->numero }}</td>
                <td>{{ $g->fecha?->format('d/m/Y') }}</td>
                <td>{{ $g->cotizacion?->cliente?->nombre_cliente }}</td>
                <td class="text-end">
                    {{ number_format($g->cotizacion?->total ?? 0, 2) }}
                </td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3" class="text-end">TOTAL</th>
            <th class="text-end">
                {{ number_format($total, 2) }}
            </th>
        </tr>
    </tfoot>
</table>

</body>
</html>
