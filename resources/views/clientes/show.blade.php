@extends('layouts.app')
@section('content')
<h3>Cliente: {{ $cliente->nombre_cliente }}</h3>
<div class="card mt-3"><div class="card-body">
  <div class="row">
    <div class="col-md-6"><strong>RUC:</strong> {{ $cliente->ruc }}</div>
    <div class="col-md-6"><strong>Razón Social:</strong> {{ $cliente->nombre_factura }}</div>
    <div class="col-md-6"><strong>Dirección:</strong> {{ $cliente->direccion }}</div>
    <div class="col-md-6"><strong>Lugar:</strong> {{ $cliente->lugar }}</div>
    <div class="col-md-6"><strong>Administrador:</strong> {{ $cliente->nombre_administrador }}</div>
    <div class="col-md-6"><strong>Proyecto/Edificio:</strong> {{ $cliente->unidad_inmobiliaria }}</div>
    <div class="col-md-6"><strong>Tipo comprobante:</strong> {{ $cliente->tipo_comprobante }}</div>
    <div class="col-md-6"><strong>Activo:</strong> {{ $cliente->activo?'Si':'No' }}</div>
    <div class="col-12 mt-2"><strong>Observación:</strong><br>{{ $cliente->observacion }}</div>
  </div>
</div></div>
<a class="btn btn-outline-secondary mt-3" href="{{ route('clientes.index') }}">Volver</a>
@endsection
