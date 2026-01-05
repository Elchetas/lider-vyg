@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Cotizaciones</h3>

    {{-- Crear cotización ahora se hace desde el catálogo --}}
    <a class="btn btn-primary" href="{{ route('productos.catalogo') }}">
        Nueva cotización
    </a>
</div>

<table class="table table-sm table-striped align-middle">
    <thead>
        <tr>
            <th>Número</th>
            <th>Fecha</th>
            <th>Cliente</th>
            <th>Estado</th>
            <th class="text-end">Total</th>
            <th class="text-end">Acciones</th>
        </tr>
    </thead>
    <tbody>
        @forelse($cotizaciones as $c)
            <tr>
                <td>{{ $c->numero }}</td>
                <td>{{ $c->fecha_emision?->format('d/m/Y') }}</td>
                <td>{{ $c->cliente?->nombre_cliente }}</td>
                <td>
                    <span class="badge bg-secondary">
                        {{ $c->estado }}
                    </span>
                </td>
                <td class="text-end">
                    {{ number_format($c->total, 2) }}
                </td>
                <td class="text-end">

                    {{-- VER COTIZACIÓN --}}
                    <a class="btn btn-sm btn-outline-secondary"
                       href="{{ route('cotizaciones.show', $c) }}">
                        Ver
                    </a>

                    {{-- EDITAR SOLO SI ESTÁ PENDIENTE --}}
                    @if($c->estado === 'Pendiente')
                        <a class="btn btn-sm btn-outline-primary"
                           href="{{ route('productos.catalogo', ['cotizacion_id' => $c->id]) }}">
                            Editar
                        </a>
                    @endif

                    {{-- GENERAR GUÍA (FLUJO REAL DEL SISTEMA) --}}
                    @if(!$c->guia)
                        <form action="{{ route('cotizaciones.generar_guia', $c) }}"
                              method="POST"
                              class="d-inline">
                            @csrf
                            <button type="submit"
                                class="btn btn-sm btn-outline-success"
                                onclick="return confirm('¿Generar guía desde esta cotización?')">
                                Generar Guía
                            </button>
                        </form>
                    @else
                        <a class="btn btn-sm btn-outline-success"
                           href="{{ route('guias.show', $c->guia) }}">
                            Ver Guía
                        </a>
                    @endif

                </td>
            </tr>
        @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-3">
                    No hay cotizaciones registradas.
                </td>
            </tr>
        @endforelse
    </tbody>
</table>

{{ $cotizaciones->links() }}
@endsection
