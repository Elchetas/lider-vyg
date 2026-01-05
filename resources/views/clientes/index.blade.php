@extends('layouts.app')
@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Clientes</h3>
  <a class="btn btn-primary" href="{{ route('clientes.create') }}">Nuevo</a>
</div>
<table class="table table-sm table-striped">
  <thead><tr>
    <th>ID</th><th>Nombre</th><th>RUC</th><th>Administrador</th><th>Proyecto/Edificio</th><th>Activo</th><th></th>
  </tr></thead>
  <tbody>
    @foreach($clientes as $c)
      <tr>
        <td>{{ $c->id }}</td>
        <td>{{ $c->nombre_cliente }}</td>
        <td>{{ $c->ruc }}</td>
        <td>{{ $c->nombre_administrador }}</td>
        <td>{{ $c->unidad_inmobiliaria }}</td>
        <td>{{ $c->activo ? 'Si' : 'No' }}</td>
        <td class="text-end">
          <a class="btn btn-sm btn-outline-secondary" href="{{ route('clientes.show',$c) }}">Ver</a>
          <a class="btn btn-sm btn-outline-primary" href="{{ route('clientes.edit',$c) }}">Editar</a>
          <form class="d-inline" method="POST" action="{{ route('clientes.destroy',$c) }}" onsubmit="return confirm('¿Eliminar cliente?')">
            @csrf @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
          </form>
        </td>
      </tr>
    @endforeach
  </tbody>
</table>
{{ $clientes->links() }}
@endsection
