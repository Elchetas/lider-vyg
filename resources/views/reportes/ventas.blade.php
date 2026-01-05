@extends('layouts.app')

@section('content')
<h3>Reporte de Ventas</h3>

<form class="row g-2 mb-3">
    <div class="col-auto">
        <input type="date" name="desde" class="form-control"
               value="{{ $desde }}">
    </div>
    <div class="col-auto">
        <input type="date" name="hasta" class="form-control"
               value="{{ $hasta }}">
    </div>
    <div class="col-auto">
        <button class="btn btn-primary">Filtrar</button>
        <a class="btn btn-outline-danger"
           href="{{ route('reportes.ventas.pdf', request()->all()) }}">
            PDF
        </a>
    </div>
</form>

<table class="table table-sm table-striped">
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
@endsection
