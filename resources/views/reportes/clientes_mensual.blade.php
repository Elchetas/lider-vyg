@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Reporte mensual por cliente</h3>
    <form class="d-flex gap-2" method="GET" action="{{ route('reportes.clientes_mensual') }}">
        <input type="month" class="form-control" name="mes" value="{{ $mes }}">
        <button class="btn btn-primary" type="submit">Ver</button>
    </form>
</div>

<div class="alert alert-info">
    Muestra los productos solicitados (cotizaciones <strong>Aprobadas</strong>) agrupados por cliente para el mes seleccionado.
</div>

@if(count($clientes) === 0)
    <div class="alert alert-warning">No hay cotizaciones aprobadas para este mes.</div>
@else
    <div class="accordion" id="accClientes">
        @foreach($clientes as $idx => $c)
            <div class="accordion-item">
                <h2 class="accordion-header" id="h{{ $idx }}">
                    <button class="accordion-button @if($idx>0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#c{{ $idx }}">
                        {{ $c['nombre_cliente'] }}
                        <span class="badge bg-secondary ms-2">Total unidades: {{ $c['total_cantidad'] }}</span>
                    </button>
                </h2>
                <div id="c{{ $idx }}" class="accordion-collapse collapse @if($idx===0) show @endif" data-bs-parent="#accClientes">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Cantidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($c['items'] as $it)
                                        <tr>
                                            <td>{{ $it['codigo'] }}</td>
                                            <td>{{ $it['descripcion'] }}</td>
                                            <td class="text-end">{{ $it['cantidad'] }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection
