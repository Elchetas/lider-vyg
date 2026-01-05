@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <div>
    <h3 class="mb-0">Productos (Administración)</h3>
    <div class="text-muted small">Esta vista es para mantenimiento. Para cotizar usa el <a href="{{ route('productos.catalogo') }}">Catálogo</a>.</div>
  </div>
  <div class="d-flex gap-2">
    <a class="btn btn-outline-secondary" href="{{ route('productos.catalogo') }}">Ir al Catálogo</a>
    <a class="btn btn-primary" href="{{ route('productos.create') }}">Nuevo</a>
  </div>
</div>

<table class="table table-sm table-striped align-middle">
  <thead>
    <tr>
      <th>Imagen</th>
      <th>Código</th>
      <th>Producto</th>
      <th>Precio Obra</th>
      <th>Precio Edificio</th>
      <th>Precio Proveedor</th>
      <th>Proveedor</th>
      <th>Activo</th>
      <th></th>
    </tr>
  </thead>
  <tbody>
  @foreach($productos as $p)
    @php
      $img = $p->imagen_url;
      $prov = $p->nombre_proveedor ?? $p->proveedor;
    @endphp
    <tr>
      <td style="width:80px">
        @if($img)
          <img src="{{ $img }}" style="height:50px; width:auto" alt="{{ $p->nombre_producto }}">
        @endif
      </td>
      <td>{{ $p->codigo }}</td>
      <td>
        <div class="fw-semibold">{{ $p->nombre_producto }}</div>
        <div class="small text-muted">{{ \Illuminate\Support\Str::limit($p->descripcion, 60) }}</div>
      </td>
      <td>{{ number_format((float)($p->precio_obra ?? $p->precio ?? 0),2) }}</td>
      <td>{{ number_format((float)($p->precio_edificio ?? $p->precio ?? 0),2) }}</td>
      <td>{{ number_format((float)($p->precio_proveedor ?? $p->precio_prov ?? 0),2) }}</td>
      <td>{{ $prov }}</td>
      <td>{{ $p->activo?'Si':'No' }}</td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('productos.show',$p) }}">Ver</a>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('productos.edit',$p) }}">Editar</a>
        <form class="d-inline" method="POST" action="{{ route('productos.destroy',$p) }}" onsubmit="return confirm('¿Eliminar producto?')">
          @csrf @method('DELETE')
          <button class="btn btn-sm btn-outline-danger">Eliminar</button>
        </form>
      </td>
    </tr>
  @endforeach
  </tbody>
</table>

{{ $productos->links() }}
@endsection
