@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="m-0">Configuración SUNAT (Perú)</h3>
        <form method="POST" action="{{ route('sunat.test') }}">
            @csrf
            <button type="submit" class="btn btn-outline-secondary">Probar conexión</button>
        </form>
    </div>

    @if(session('status'))
        <div class="alert alert-success">{{ session('status') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <div class="alert alert-info">
        <strong>Importante:</strong> Para emisión real SUNAT necesitas credenciales SOL y certificado digital (.pfx/.p12).
        Este módulo guarda la configuración y valida conectividad al WSDL.
    </div>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="{{ route('sunat.update') }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">RUC Emisor</label>
                        <input type="text" name="ruc" class="form-control" value="{{ old('ruc', $config?->ruc) }}" placeholder="11 dígitos">
                    </div>
                    <div class="col-md-8">
                        <label class="form-label">Razón social</label>
                        <input type="text" name="razon_social" class="form-control" value="{{ old('razon_social', $config?->razon_social) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Usuario SOL</label>
                        <input type="text" name="sol_user" class="form-control" value="{{ old('sol_user', $config?->sol_user) }}" placeholder="Ej: MODDATOS">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Clave SOL</label>
                        <input type="password" name="sol_password" class="form-control" value="" placeholder="(dejar vacío para no cambiar)">
                        <div class="form-text">Se guarda encriptada.</div>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">WSDL Facturación Electrónica (Producción)</label>
                        <input type="url" name="fe_wsdl" class="form-control" value="{{ old('fe_wsdl', $config?->fe_wsdl ?: $defaults['fe_wsdl']) }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">WSDL GRE (opcional)</label>
                        <input type="url" name="gre_wsdl" class="form-control" value="{{ old('gre_wsdl', $config?->gre_wsdl ?: $defaults['gre_wsdl']) }}">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label">Certificado digital (.pfx/.p12)</label>
                        <input type="file" name="cert_file" class="form-control" accept=".pfx,.p12">
                        @if($config?->cert_path)
                            <div class="form-text">Certificado cargado: <code>{{ $config->cert_path }}</code></div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Clave de certificado</label>
                        <input type="password" name="cert_password" class="form-control" value="" placeholder="(dejar vacío para no cambiar)">
                        <div class="form-text">Se guarda encriptada.</div>
                    </div>

                    <div class="col-12">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_enabled" id="is_enabled" value="1" {{ old('is_enabled', $config?->is_enabled) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_enabled">Activar módulo SUNAT</label>
                        </div>
                    </div>
                </div>

                @if($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="d-flex justify-content-end mt-3">
                    <button class="btn btn-primary" type="submit">Guardar</button>
                </div>
            </form>
        </div>
    </div>

    <div class="mt-4">
        <h5>Emisión real (pasos)</h5>
        <ol>
            <li>Completa RUC/Usuario SOL/Clave SOL.</li>
            <li>Carga tu certificado digital (.pfx/.p12) y su clave.</li>
            <li>Presiona <strong>Probar conexión</strong> (verifica acceso al WSDL).</li>
            <li>Activa el módulo.</li>
        </ol>
        <div class="small text-muted">Nota: la emisión de comprobantes (Factura/Boleta/GRE) se habilita en la siguiente iteración del módulo.</div>
    </div>
</div>
@endsection
