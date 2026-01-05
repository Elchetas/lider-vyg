@extends('layouts.app')
@section('content')
<h3>Guías</h3>
<table class="table table-sm table-striped mt-3">
  <thead><tr>
    <th>Número</th><th>Fecha</th><th>Cotización</th><th>Cliente</th><th></th>
  </tr></thead>
  <tbody>
  @foreach($guias as $g)
    <tr>
      <td>{{ $g->numero }}</td>
      <td>{{ $g->fecha->format('d/m/Y') }}</td>
      <td>{{ $g->cotizacion?->numero }}</td>
      <td>{{ $g->cotizacion?->cliente?->nombre_cliente }}</td>
      <td class="text-end">
        <a class="btn btn-sm btn-outline-secondary" href="{{ route('guias.show',$g) }}">Ver</a>
        <a class="btn btn-sm btn-outline-primary" href="{{ route('guias.pdf',$g) }}">PDF</a>
        @if(auth()->user()->isAdmin())
          <form method="POST" action="{{ route('guias.destroy',$g) }}" class="d-inline"
                onsubmit="return confirm('¿Eliminar guía? La cotización volverá a Pendiente.');">
            @csrf
            @method('DELETE')
            <button class="btn btn-sm btn-outline-danger">Eliminar</button>
          </form>
        @endif
      </td>
    </tr>
  @endforeach
  </tbody>
</table>
{{ $guias->links() }}
@endsection
