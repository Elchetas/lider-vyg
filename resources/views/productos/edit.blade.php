@extends('layouts.app')
@section('content')
<h3>Editar Producto #{{ $producto->id }}</h3>
<form method="POST" action="{{ route('productos.update',$producto) }}" enctype="multipart/form-data" class="mt-3">
@csrf @method('PUT')
@include('productos.form', ['producto'=>$producto])
<button class="btn btn-primary mt-3">Guardar</button>
<a class="btn btn-outline-secondary mt-3" href="{{ route('productos.admin.index') }}">Volver</a>
</form>
@endsection
