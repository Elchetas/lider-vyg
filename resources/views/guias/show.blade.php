@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center">
    <h3>Guía Nº {{ $guia->numero }}</h3>

    <div class="d-flex gap-2">
        {{-- PDF (descarga directa, sin vista previa) --}}
        <a class="btn btn-outline-primary"
           href="{{ route('guias.pdf', $guia) }}">
            Descargar PDF
        </a>

        {{-- EXCEL SUNAT --}}
        @php
            $excelPath = 'storage/sunat/FACT_' . $guia->numero . '.xlsx';
        @endphp

        @if(file_exists(public_path($excelPath)))
            <a class="btn btn-outline-success"
               href="{{ asset($excelPath) }}"
               target="_blank" rel="noopener">
                Excel SUNAT
            </a>
        @endif

        {{-- ELIMINAR GUÍA (similar a eliminar cotización) --}}
        @if(auth()->user()->isAdmin())
            <form method="POST"
                  action="{{ route('guias.destroy', $guia) }}"
                  onsubmit="return confirm('¿Está seguro que desea eliminar esta guía? La cotización volverá a Pendiente.');">
                @csrf
                @method('DELETE')
                <button class="btn btn-outline-danger">
                    Eliminar
                </button>
            </form>
        @endif
    </div>
</div>

<div class="card mt-3">
    <div class="card-body">
        <div class="row g-2">
            <div class="col-md-4">
                <strong>Fecha:</strong> {{ $guia->fecha?->format('d/m/Y') }}
            </div>

            <div class="col-md-8">
                <strong>Cliente:</strong> {{ $guia->cotizacion?->cliente?->nombre_cliente }}
            </div>

            <div class="col-md-4">
                <strong>RUC/DNI:</strong> {{ $guia->cotizacion?->cliente?->ruc_dni }}
            </div>

            <div class="col-md-8">
                <strong>Proyecto:</strong> {{ $guia->cotizacion?->cliente?->unidad_inmobiliaria }}
            </div>

            <div class="col-12">
                <strong>Generada desde Cotización Nº:</strong> {{ $guia->cotizacion?->numero }}
            </div>
        </div>
    </div>
</div>

<div class="table-responsive mt-3">
    <table class="table table-sm table-striped align-middle">
        <thead>
            <tr>
                <th style="width:120px">Código</th>
                <th>Descripción</th>
                <th class="text-end" style="width:120px">Cantidad</th>
                <th style="width:220px">Observaciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($guia->cotizacion->detalles as $d)
                <tr>
                    <td>{{ $d->codigo }}</td>
                    <td>{{ $d->descripcion }}</td>
                    <td class="text-end">{{ $d->cantidad }}</td>
                    <td class="text-danger fw-semibold">{{ $d->observaciones }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<div class="mt-3">
    <strong>Observaciones generales:</strong><br>
    <span class="text-muted">{{ $guia->cotizacion->observaciones }}</span>
</div>

<a class="btn btn-outline-secondary mt-3" href="{{ route('guias.index') }}">
    Volver
</a>
@endsection
