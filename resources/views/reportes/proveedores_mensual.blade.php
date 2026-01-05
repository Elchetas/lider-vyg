@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Reporte mensual por proveedor</h3>
    <form class="d-flex gap-2" method="GET" action="{{ route('reportes.proveedores_mensual') }}">
        <input type="month" class="form-control" name="mes" value="{{ $mes }}">
        <button class="btn btn-primary" type="submit">Ver</button>
    </form>
</div>

<div class="alert alert-info">
    Muestra productos vendidos (cotizaciones <strong>Aprobadas</strong>) agrupados por proveedor, con el total estimado a pagar usando <strong>precio_proveedor</strong>.
</div>

@if(count($proveedores) === 0)
    <div class="alert alert-warning">No hay ventas aprobadas para este mes.</div>
@else
    <div class="accordion" id="accProv">
        @foreach($proveedores as $idx => $p)
            <div class="accordion-item">
                <h2 class="accordion-header" id="ph{{ $idx }}">
                    <button class="accordion-button @if($idx>0) collapsed @endif" type="button" data-bs-toggle="collapse" data-bs-target="#pc{{ $idx }}">
                        {{ $p['proveedor'] }}
                        <span class="badge bg-secondary ms-2">Unidades: {{ $p['total_cantidad'] }}</span>
                        <span class="badge bg-success ms-2">Total a pagar: S/ {{ number_format($p['total_pagar'],2) }}</span>
                    </button>
                </h2>
                <div id="pc{{ $idx }}" class="accordion-collapse collapse @if($idx===0) show @endif" data-bs-parent="#accProv">
                    <div class="accordion-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Descripción</th>
                                        <th class="text-end">Cantidad</th>
                                        <th class="text-end">P. prov.</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($p['items'] as $it)
                                        <tr>
                                            <td>{{ $it['codigo'] }}</td>
                                            <td>{{ $it['descripcion'] }}</td>
                                            <td class="text-end">{{ $it['cantidad'] }}</td>
                                            <td class="text-end">{{ number_format($it['precio_proveedor'],2) }}</td>
                                            <td class="text-end">{{ number_format($it['total'],2) }}</td>
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
