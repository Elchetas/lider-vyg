@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center">
  <h3>Reporte SUNAT - {{ $mes }}</h3>
  <a class="btn btn-success" href="{{ route('reportes.sunat.export', ['mes'=>$mes]) }}">Exportar Excel</a>
</div>
<form class="row g-2 mt-3" method="GET" action="{{ route('reportes.sunat') }}">
  <div class="col-md-3">
    <input class="form-control" type="month" name="mes" value="{{ $mes }}">
  </div>
  <div class="col-md-2">
    <button class="btn btn-outline-primary">Ver</button>
  </div>
</form>

<div class="table-responsive mt-3">
<table class="table table-sm table-striped">
  <thead>
    <tr>
      <th>Nº</th><th>Fecha</th><th>Tipo Doc</th><th>Número</th><th>Guía</th><th>RUC</th><th>Razón Social</th><th>Subtotal</th><th>IGV</th><th>Total</th><th>Afecto</th><th>Estado</th>
    </tr>
  </thead>
  <tbody>
    @foreach($cotizaciones as $i => $c)
      <tr>
        <td>{{ $i+1 }}</td>
        <td>{{ $c->fecha_emision->format('d/m/Y') }}</td>
        <td>{{ $c->cliente?->tipo_comprobante }}</td>
        <td>{{ $c->numero }}</td>
        <td>{{ $c->guia?->numero }}</td>
        <td>{{ $c->cliente?->ruc }}</td>
        <td>{{ $c->cliente?->nombre_factura }}</td>
        <td>{{ number_format($c->subtotal,2) }}</td>
        <td>{{ number_format($c->igv,2) }}</td>
        <td>{{ number_format($c->total,2) }}</td>
        <td>{{ $c->afecto_igv ? 'AFECTO' : 'NO AFECTO' }}</td>
        <td>{{ $c->estado }}</td>
      </tr>
    @endforeach
  </tbody>
</table>
</div>
@endsection
