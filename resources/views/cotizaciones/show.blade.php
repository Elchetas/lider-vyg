@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center">
    <h3>Cotización Nº {{ $cotizacion->numero }}</h3>

    <div class="d-flex gap-2">
        {{-- PDF --}}
        <a class="btn btn-outline-secondary"
           href="{{ route('cotizaciones.pdf', ['cotizacion' => $cotizacion->id]) }}">
            PDF
        </a>

        {{-- EDITAR → REGRESA AL CATÁLOGO --}}
        @if($cotizacion->estado === 'Pendiente' && !$cotizacion->guia)
            <a class="btn btn-outline-primary"
               href="{{ route('productos.catalogo', ['cotizacion_id' => $cotizacion->id]) }}">
                Editar
            </a>
        @endif

        {{-- ELIMINAR --}}
        @if($cotizacion->estado === 'Pendiente' && !$cotizacion->guia)
            <form method="POST"
                  action="{{ route('cotizaciones.destroy', $cotizacion) }}"
                  onsubmit="return confirm('¿Está seguro que desea eliminar esta cotización?');">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    Eliminar
                </button>
            </form>
        @endif

        {{-- APROBAR / GENERAR GUÍA --}}
        @if($cotizacion->estado === 'Pendiente' && !$cotizacion->guia && auth()->user()->isAdmin())
            <form method="POST"
                  action="{{ route('cotizaciones.generar_guia', ['cotizacion' => $cotizacion->id]) }}"
                  onsubmit="return confirm('¿Aprobar y generar guía? Luego no se podrá editar.');">
                @csrf
                <button class="btn btn-success">
                    Aprobar / Generar Guía
                </button>
            </form>
        @endif
    </div>
</div>

{{-- ================= DATOS GENERALES ================= --}}
<div class="card mt-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-3">
                <strong>Fecha:</strong>
                {{ $cotizacion->fecha_emision?->format('d/m/Y') }}
            </div>
            <div class="col-md-5">
                <strong>Cliente:</strong>
                {{ $cotizacion->cliente?->nombre_cliente }}
            </div>
            <div class="col-md-2">
                <strong>Moneda:</strong>
                {{ $cotizacion->moneda }}
            </div>
            <div class="col-md-2">
                <strong>Estado:</strong>
                <span class="badge {{ $cotizacion->estado === 'Pendiente' ? 'bg-warning text-dark' : 'bg-success' }}">
                    {{ $cotizacion->estado }}
                </span>
            </div>
            <div class="col-md-3 mt-2">
                <strong>IGV:</strong>
                {{ $cotizacion->afecto_igv ? 'Afecto' : 'No afecto' }}
            </div>
            <div class="col-md-9 mt-2">
                <strong>Obs. generales:</strong>
                {{ $cotizacion->observaciones ?: '-' }}
            </div>
        </div>
    </div>
</div>

{{-- ================= DETALLE ================= --}}
<div class="table-responsive mt-3">
    <table class="table table-sm table-striped align-middle">
        <thead>
            <tr>
                <th>Código</th>
                <th>Descripción</th>
                <th class="text-end">Cant.</th>
                <th class="text-end">Precio Unit.</th>
                <th class="text-end">Total</th>
                <th>Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($cotizacion->detalles as $d)
                <tr>
                    <td>{{ $d->codigo }}</td>
                    <td>{{ $d->descripcion }}</td>
                    <td class="text-end">{{ $d->cantidad }}</td>
                    <td class="text-end">{{ number_format($d->precio_unitario, 2) }}</td>
                    <td class="text-end">{{ number_format($d->total_linea, 2) }}</td>
                    <td class="text-danger fw-semibold">
                        {{ $d->observaciones ?: '-' }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

{{-- ================= TOTALES ================= --}}
<div class="row justify-content-end">
    <div class="col-md-4">
        <div class="card mt-2">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <span>Subtotal</span>
                    <strong>{{ number_format($cotizacion->subtotal, 2) }}</strong>
                </div>
                <div class="d-flex justify-content-between">
                    <span>IGV</span>
                    <strong>{{ number_format($cotizacion->igv, 2) }}</strong>
                </div>
                <hr>
                <div class="d-flex justify-content-between">
                    <span>TOTAL</span>
                    <strong>{{ number_format($cotizacion->total, 2) }}</strong>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ================= GUÍA ================= --}}
@if($cotizacion->guia)
    <div class="alert alert-info mt-3">
        Esta cotización ya fue aprobada.
        Guía Nº <strong>{{ $cotizacion->guia->numero }}</strong>.
        <a href="{{ route('guias.show', ['guia' => $cotizacion->guia->id]) }}">
            Ver guía
        </a>
    </div>
@endif

<a class="btn btn-outline-secondary mt-3"
   href="{{ route('cotizaciones.index') }}">
    Volver
</a>

{{-- ================= VISTA PREVIA PDF ================= --}}
<div class="modal fade" id="pdfPreviewCotizacion" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Vista previa – Cotización Nº {{ $cotizacion->numero }}</h5>
        <div class="d-flex gap-2">
          <a class="btn btn-primary btn-sm" href="{{ route('cotizaciones.pdf', $cotizacion) }}">Descargar</a>
          <button type="button" class="btn btn-outline-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
        </div>
      </div>
      <div class="modal-body p-0">
        <iframe
          title="Vista previa PDF"
          src="{{ route('cotizaciones.pdf', $cotizacion) }}?inline=1"
          style="width:100%; height:80vh; border:0;">
        </iframe>
      </div>
    </div>
  </div>
</div>

@endsection
