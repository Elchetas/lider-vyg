@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Catálogo de Productos</h3>
  <a class="btn btn-primary" href="{{ route('productos.create') }}">Nuevo</a>
</div>
<table class="table table-sm table-striped align-middle">
  <thead><tr>
    <th>Imagen</th><th>Código</th><th>Producto</th><th>Precio</th><th>Precio Prov.</th><th>Proveedor</th><th>Activo</th><th></th>
  </tr></thead>
  <tbody>
  @foreach($productos as $p)
    <tr>
      <td style="width:80px">@if($p->imagen_path)<img src="/{{ $p->imagen_path }}" style="height:50px">@endif</td>
      <td>{{ $p->codigo }}</td>
      <td>{{ $p->nombre_producto }}</td>
      <td>{{ number_format($p->precio,2) }}</td>
      <td>{{ number_format($p->precio_prov,2) }}</td>
      <td>{{ $p->proveedor }}</td>
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
