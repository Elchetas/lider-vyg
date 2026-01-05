@extends('layouts.app')

@section('content')
<h3>Nuevo Producto</h3>

<form method="POST"
      action="{{ route('productos.store') }}"
      enctype="multipart/form-data"
      class="mt-3">
    @csrf

    {{-- CAMPOS DEL PRODUCTO --}}
    @include('productos.form', ['producto' => null])

    <button class="btn btn-primary mt-3">Guardar</button>
    <a class="btn btn-outline-secondary mt-3" href="{{ route('productos.admin.index') }}">
        Volver
    </a>
</form>
@endsection
