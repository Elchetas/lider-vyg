<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Líder V y G - Sistema</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">

        <!-- Marca -->
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            Líder V y G
        </a>

        <!-- Botón responsive -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Menú -->
        <div class="collapse navbar-collapse" id="navbarMain">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">

                {{-- REGISTROS / PROCESOS --}}
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('clientes.*')
                        || request()->routeIs('productos.*')
                        ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Registros
                    </a>

                    <ul class="dropdown-menu">
                        <!-- CLIENTES -->
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('clientes.*') ? 'active' : '' }}"
                               href="{{ route('clientes.index') }}">
                                Clientes
                            </a>
                        </li>

                        <li><hr class="dropdown-divider"></li>

                        <!-- CATÁLOGO -->
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('productos.catalogo') ? 'active' : '' }}"
                               href="{{ route('productos.catalogo') }}">
                                Catálogo de productos
                            </a>
                        </li>

                        <!-- ADMIN PRODUCTOS -->
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('productos.admin.*') ? 'active' : '' }}"
                               href="{{ route('productos.admin.index') }}">
                                Administrar productos
                            </a>
                        </li>
                    </ul>
                </li>

                <!-- COTIZACIONES -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('cotizaciones.*') ? 'active' : '' }}"
                       href="{{ route('cotizaciones.index') }}">
                        Cotizaciones
                    </a>
                </li>

                <!-- GUÍAS -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('guias.*') ? 'active' : '' }}"
                       href="{{ route('guias.index') }}">
                        Guías
                    </a>
                </li>

                <!-- REPORTES -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle
                        {{ request()->routeIs('reportes.sunat*')
                            || request()->routeIs('sunat.config*')
                            || request()->routeIs('reportes.clientes_mensual*')
                            || request()->routeIs('reportes.proveedores_mensual*')
                            ? 'active' : '' }}"
                       href="#" role="button" data-bs-toggle="dropdown">
                        Reportes
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('reportes.sunat*') ? 'active' : '' }}"
                               href="{{ route('reportes.sunat') }}">
                                SUNAT
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('sunat.config*') ? 'active' : '' }}"
                               href="{{ route('sunat.config') }}">
                                Configuración SUNAT
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('reportes.clientes_mensual*') ? 'active' : '' }}"
                               href="{{ route('reportes.clientes_mensual') }}">
                                Clientes (mensual)
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item {{ request()->routeIs('reportes.proveedores_mensual*') ? 'active' : '' }}"
                               href="{{ route('reportes.proveedores_mensual') }}">
                                Proveedores (mensual)
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>

            <!-- Usuario -->
            @auth
                <span class="navbar-text text-white me-3">
                    {{ auth()->user()->name }}
                </span>

                <!-- Logout SOLO POST -->
                <form method="POST" action="{{ route('logout') }}" class="d-inline">
                    @csrf
                    <button type="submit" class="btn btn-outline-light btn-sm">
                        Salir
                    </button>
                </form>
            @endauth
        </div>
    </div>
</nav>

<!-- Contenido -->
<div class="container py-4">

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- CONTENIDO DE LAS VISTAS --}}
    @yield('content')

</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@stack('scripts')
</body>
</html>
