@extends('layouts.app')
@section('content')
<h3>Editar Cliente #{{ $cliente->id }}</h3>
<form method="POST" action="{{ route('clientes.update',$cliente) }}" class="mt-3">
@csrf @method('PUT')
@include('clientes.form', ['cliente'=>$cliente])
<button class="btn btn-primary">Guardar</button>
<a class="btn btn-outline-secondary" href="{{ route('clientes.index') }}">Volver</a>
</form>
@endsection
