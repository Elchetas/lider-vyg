@extends('layouts.app')

@section('content')
<h3>Editar Producto #{{ $producto->id }}</h3>

<form method="POST"
      action="{{ route('productos.update', $producto->id) }}"
      enctype="multipart/form-data"
      class="mt-3">

    @csrf
    @method('PUT')

    {{-- formulario del producto --}}
    @include('productos.form', ['producto' => $producto])

    {{-- mostrar imagen actual --}}
    @if($producto->imagen)
        <div class="mt-3">
            <p>Imagen actual:</p>
            <img src="{{ asset('productos/'.$producto->imagen) }}?v={{ $producto->updated_at->timestamp }}"
                 alt="Imagen producto"
                 width="150">
        </div>
    @endif

    <button class="btn btn-primary mt-3">Guardar</button>
    <a class="btn btn-outline-secondary mt-3"
       href="{{ route('productos.admin.index') }}">
        Volver
    </a>
</form>
@endsection
