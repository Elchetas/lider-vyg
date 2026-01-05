<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Recuperar contraseña</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container d-flex justify-content-center align-items-center vh-100">
    <div class="card shadow" style="width: 420px;">
        <div class="card-body">

            <h5 class="text-center mb-3">Recuperar contraseña</h5>

            <p class="text-muted small">
                Ingresa tu correo electrónico y te enviaremos un enlace para restablecer tu contraseña.
            </p>

            {{-- Estado --}}
            @if (session('status'))
                <div class="alert alert-success">
                    {{ session('status') }}
                </div>
            @endif

            {{-- Errores --}}
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email"
                           name="email"
                           class="form-control"
                           value="{{ old('email') }}"
                           required autofocus>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    Enviar enlace de recuperación
                </button>
            </form>

            <div class="text-center mt-3">
                <a href="{{ route('login') }}">Volver al login</a>
            </div>

        </div>
    </div>
</div>

</body>
</html>
