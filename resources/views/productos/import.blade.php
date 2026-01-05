@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Importar productos</h2>

    <form action="{{ route('productos.importar') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="mb-3">
            <label class="form-label">Archivo Excel</label>
            <input type="file" name="archivo" class="form-control" required>
        </div>

        <button class="btn btn-primary">Importar</button>
    </form>
</div>
@endsection
