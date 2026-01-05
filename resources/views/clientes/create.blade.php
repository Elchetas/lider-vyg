@extends('layouts.app')
@section('content')
<h3>Nuevo Cliente</h3>
<form method="POST" action="{{ route('clientes.store') }}" class="mt-3">
@csrf
@include('clientes.form', ['cliente'=>null])
<button class="btn btn-primary">Guardar</button>
<a class="btn btn-outline-secondary" href="{{ route('clientes.index') }}">Volver</a>
</form>
@endsection
