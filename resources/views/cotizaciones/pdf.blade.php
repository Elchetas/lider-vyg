<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <style>
        /* Forzar A4 y márgenes consistentes */
        @page {
            size: A4;
            margin: 18mm 15mm;
        }
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 11px;
            color: #111827;
            margin: 0;
        }

        /* ===== PALETA ===== */
        .primary { color: #368C41; }
        .bg-primary { background: #368C41; color: #ffffff; }
        .border-primary { border-color: #368C41; }

        /* ===== CONTACTO SUPERIOR ===== */
        .contact-bar {
            text-align: center;
            font-size: 10px;
            color: #374151;
            padding-bottom: 6px;
            margin-bottom: 10px;
            border-bottom: 1px solid #e5e7eb;
        }

        /* ===== HEADER ===== */
        .header-table {
            width: 100%;
            border-collapse: collapse;
        }
        .logo-cell {
            width: 55%;
            vertical-align: top;
        }
        .doc-cell {
            width: 45%;
            vertical-align: top;
            text-align: right;
        }
        .doc-box {
            display: inline-block;
            text-align: center;
            padding: 0;
            border: 2px solid #368C41;
            border-radius: 6px;
            overflow: hidden;
        }
        .doc-box .title {
            font-size: 11px;
            letter-spacing: 0.6px;
            font-weight: 800;
            background: #368C41;
            color: #ffffff;
            padding: 6px 10px;
        }
        .doc-box .number {
            font-size: 16px;
            font-weight: 900;
            padding: 6px 10px 2px 10px;
            color: #111827;
        }
        .doc-box .date {
            font-size: 10px;
            color: #374151;
            padding: 0 10px 6px 10px;
        }

        /* ===== CLIENTE ===== */
        .cliente {
            margin-top: 10px;
            border: 1px solid #e5e7eb;
            border-left: 4px solid #368C41;
            padding: 8px 10px;
            border-radius: 6px;
        }
        .cliente strong {
            color: #111827;
        }

        /* ===== TABLE ===== */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        thead th {
            background: #368C41;
            color: #ffffff;
            padding: 7px 6px;
            border: 1px solid #e5e7eb;
            font-weight: 700;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        tbody td {
            padding: 6px;
            border: 1px solid #e5e7eb;
            vertical-align: top;
        }
        .text-right {
            text-align: right;
        }

        /* ===== OBS ===== */
        .obs {
            color: #111827;
            white-space: pre-line;
        }

        /* ===== TOTALES (si aplica) ===== */
        .totales {
            width: 100%;
            margin-top: 12px;
        }
        .total-label {
            color: #374151;
            font-weight: 700;
            padding: 6px;
            border: 1px solid #e5e7eb;
            background: #f9fafb;
        }
        .total-value {
            text-align: right;
            font-weight: 800;
            padding: 6px;
            border: 1px solid #e5e7eb;
        }
        .small {
            font-size: 9.5px;
            color: #374151;
        }
    
</style>
</head>
<body>

{{-- ================= CONTACTO EMPRESA ================= --}}
@if($empresa && ($empresa->email || $empresa->telefono))
    <div class="contact-bar">
        @if($empresa->email)
            <span><strong>Email:</strong> {{ $empresa->email }}</span>
        @endif
        @if($empresa->email && $empresa->telefono)
            <span style="margin:0 8px;">|</span>
        @endif
        @if($empresa->telefono)
            <span><strong>Tel:</strong> {{ $empresa->telefono }}</span>
        @endif
    </div>
@endif



{{-- ================= HEADER ================= --}}
<table class="header-table">
    <tr>
        <td class="logo-cell">
            <img src="{{ public_path('vendor/lidervyg/logo.png') }}" width="125" style="margin-top:2px">
        </td>
        <td class="doc-cell">
            <div class="doc-box">
                <div class="title">COTIZACIÓN</div>
                <div class="number">{{ $cotizacion->numero }}</div>
                <div class="date">{{ \Carbon\Carbon::parse($cotizacion->fecha_emision)->format('d/m/Y') }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ================= CLIENTE ================= --}}
<div class="cliente">
    {{-- En PDF: "Cliente" debe ser el nombre de facturación y NO incluir tipo/proyecto --}}
    <strong>Cliente:</strong> {{ $cotizacion->cliente->nombre_factura ?? $cotizacion->cliente->nombre_cliente }}<br>
    <strong>RUC:</strong> {{ $cotizacion->cliente->ruc }}<br>
    <strong>Contacto:</strong> {{ $cotizacion->cliente->nombre_administrador }}<br>
    <strong>Dirección:</strong> {{ $cotizacion->cliente->direccion }}
</div>

<div class="small" style="margin-top:6px">
    <strong>Moneda:</strong> {{ $cotizacion->moneda }} &nbsp; | &nbsp;
    <strong>IGV:</strong>
    {{ $cotizacion->afecto_igv ? 'Operación Afecta a IGV' : 'Operación No Afecta a IGV' }}
</div>

{{-- ================= ITEMS ================= --}}
<table>
    <thead>
    <tr>
        <th style="width:70px">Código</th>
        <th>Nombre de Productos</th>
        <th style="width:60px">Cant.</th>
        <th style="width:90px">P. Unit.</th>
        <th style="width:90px">Sub Total</th>
        <th style="width:140px">Observaciones</th>
    </tr>
    </thead>
    <tbody>
    @foreach($cotizacion->detalles as $d)
        <tr>
            <td>{{ $d->codigo }}</td>
            <td>{{ $d->descripcion }}</td>
            <td class="text-right">{{ $d->cantidad }}</td>
            @php($sym = $cotizacion->moneda === 'USD' ? '$' : 'S/')
            <td class="text-right">{{ $sym }} {{ number_format($d->precio_unitario,2) }}</td>
            <td class="text-right">{{ $sym }} {{ number_format($d->total_linea,2) }}</td>
            <td class="obs">{{ $d->observaciones }}</td>
        </tr>
    @endforeach
    </tbody>
</table>

{{-- ================= OBS + TOTAL ================= --}}
<table class="totales">
    <tr>
        <td style="width:60%">
            <strong>Observaciones generales:</strong><br>
            {{ $cotizacion->observaciones }}
        </td>
        <td style="width:40%">
            <table>
                <tr>
                    <td class="total-label">Subtotal</td>
                    @php($sym = $cotizacion->moneda === 'USD' ? '$' : 'S/')
                    <td class="total-value">{{ $sym }} {{ number_format($cotizacion->subtotal,2) }}</td>
                </tr>
                <tr>
                    <td class="total-label">IGV</td>
                    <td class="total-value">{{ $sym }} {{ number_format($cotizacion->igv,2) }}</td>
                </tr>
                <tr>
                    <td class="total-label">TOTAL GENERAL</td>
                    <td class="total-value">{{ $sym }} {{ number_format($cotizacion->total,2) }}</td>
                </tr>
            </table>
        </td>
    </tr>
</table>

{{-- Nota eliminada por requerimiento: no mostrar texto sobre precios/IGV --}}

</body>
</html>
