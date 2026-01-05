@extends('layouts.app')
@section('content')
<h3>{{ $producto->nombre_producto }} ({{ $producto->codigo }})</h3>
<div class="card mt-3"><div class="card-body">
  <div class="row">
    <div class="col-md-3">
      @if($producto->imagen_path)
        <img src="/{{ $producto->imagen_path }}" style="max-width:100%">
      @endif
    </div>
    <div class="col-md-9">
      <div><strong>Precio:</strong> {{ number_format($producto->precio,2) }}</div>
      <div><strong>Precio proveedor:</strong> {{ number_format($producto->precio_prov,2) }}</div>
      <div><strong>Proveedor:</strong> {{ $producto->proveedor }}</div>
      <div><strong>Activo:</strong> {{ $producto->activo?'Si':'No' }}</div>
      <hr>
      <div><strong>Descripción:</strong><br>{{ $producto->descripcion }}</div>
      <div class="mt-2"><strong>Observaciones:</strong><br>{{ $producto->observaciones }}</div>
    </div>
  </div>
</div></div>
<a class="btn btn-outline-secondary mt-3" href="{{ route('productos.catalogo') }}">Volver</a>
@endsection
