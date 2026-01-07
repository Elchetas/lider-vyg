<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteImportController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductoImportController;
use App\Http\Controllers\CotizacionController;
use App\Http\Controllers\GuiaController;
use App\Http\Controllers\SunatReportController;
use App\Http\Controllers\SunatConfigController;
use App\Http\Controllers\ReporteClientesMensualController;
use App\Http\Controllers\ReporteProveedoresMensualController;
use App\Http\Controllers\ReporteVentaController;


/*
|--------------------------------------------------------------------------
| RUTA PRINCIPAL
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| LOGIN (GET – Breeze maneja POST)
|--------------------------------------------------------------------------
*/
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

/*
|--------------------------------------------------------------------------
| RUTAS PROTEGIDAS
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | DASHBOARD
    |--------------------------------------------------------------------------
    */
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    /*
    |--------------------------------------------------------------------------
    | PRODUCTOS (CATÁLOGO / ADMIN)
    |--------------------------------------------------------------------------
    */
    // Catálogo único (crear / editar cotización)
    Route::get('/catalogo', [ProductoController::class, 'index'])
        ->name('productos.catalogo');

    // Alias histórico
    Route::get('/productos', fn () => redirect()->route('productos.catalogo'))
        ->name('productos.index');

    // Administración
    Route::get('productos/admin', [ProductoController::class, 'adminIndex'])
        ->name('productos.admin.index');

    // Importación
    Route::get('productos/importar', [ProductoImportController::class, 'form'])
        ->name('productos.import.form');

    Route::post('productos/importar', [ProductoImportController::class, 'import'])
        ->name('productos.importar');

    // CRUD productos (sin index)
    Route::resource('productos', ProductoController::class)
        ->except(['index']);

    /*
    |--------------------------------------------------------------------------
    | CLIENTES
    |--------------------------------------------------------------------------
    */
    Route::get('clientes/importar', [ClienteImportController::class, 'form'])
        ->name('clientes.import.form');

    Route::post('clientes/importar', [ClienteImportController::class, 'import'])
        ->name('clientes.importar');

    Route::resource('clientes', ClienteController::class);

    /*
    |--------------------------------------------------------------------------
    | COTIZACIONES
    |--------------------------------------------------------------------------
    */
    // Crear desde catálogo
    Route::post(
        'cotizaciones/from-catalog',
        [CotizacionController::class, 'storeFromCatalog']
    )->name('cotizaciones.store_from_catalog');

    // Actualizar desde catálogo
    Route::put(
        'cotizaciones/{cotizacion}/from-catalog',
        [CotizacionController::class, 'updateFromCatalog']
    )->name('cotizaciones.update_from_catalog');

    // PDF cotización
    Route::get(
        'cotizaciones/{cotizacion}/pdf',
        [CotizacionController::class, 'pdf']
    )->name('cotizaciones.pdf');

    // Aprobar / generar guía
    Route::post(
        'cotizaciones/{cotizacion}/generar-guia',
        [GuiaController::class, 'generar']
    )->name('cotizaciones.generar_guia');

    // Resource (index, show, destroy, edit redirige al catálogo)
    Route::resource('cotizaciones', CotizacionController::class)
        ->parameters(['cotizaciones' => 'cotizacion']);

    /*
    |--------------------------------------------------------------------------
    | GUÍAS
    |--------------------------------------------------------------------------
    */
    Route::get('guias/{guia}/pdf', [GuiaController::class, 'pdf'])
        ->name('guias.pdf');

    Route::resource('guias', GuiaController::class)
        ->only(['index', 'show', 'destroy'])
        ->parameters(['guias' => 'guia']);

    /*
    |--------------------------------------------------------------------------
    | REPORTES SUNAT
    |--------------------------------------------------------------------------
    */
    Route::get('reportes/sunat', [SunatReportController::class, 'index'])
        ->name('reportes.sunat');

    Route::get('reportes/sunat/export', [SunatReportController::class, 'export'])
        ->name('reportes.sunat.export');

    /*
    |--------------------------------------------------------------------------
    | REPORTE DE VENTAS
    |--------------------------------------------------------------------------
    */
    Route::get('/reportes/ventas', [ReporteVentaController::class, 'index'])
        ->name('reportes.ventas');

    Route::get('/reportes/ventas/pdf', [ReporteVentaController::class, 'pdf'])
        ->name('reportes.ventas.pdf');

    /*
    |--------------------------------------------------------------------------
    | CONFIGURACIÓN SUNAT (Emisión electrónica)
    |--------------------------------------------------------------------------
    */
    Route::get('sunat/config', [SunatConfigController::class, 'edit'])->name('sunat.config');
    Route::put('sunat/config', [SunatConfigController::class, 'update'])->name('sunat.update');
    Route::post('sunat/test', [SunatConfigController::class, 'testConnection'])->name('sunat.test');

    /*
    |--------------------------------------------------------------------------
    | REPORTES (CLIENTES / PROVEEDORES)
    |--------------------------------------------------------------------------
    */
    Route::get(
        'reportes/clientes-mensual',
        [ReporteClientesMensualController::class, 'index']
    )->name('reportes.clientes_mensual');

    Route::get(
        'reportes/proveedores-mensual',
        [ReporteProveedoresMensualController::class, 'index']
    )->name('reportes.proveedores_mensual');
});

/*
|--------------------------------------------------------------------------
| AUTH (BREEZE)
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';



use App\Models\Product;

Route::get('/reset-products', function () {
    Product::query()->delete();
    return 'Productos eliminados correctamente';
});
