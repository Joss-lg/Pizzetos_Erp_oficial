<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\CorteController;
use App\Http\Controllers\PizzaController;
use App\Http\Controllers\AlitasController;
use App\Http\Controllers\CostillasController;
use App\Http\Controllers\HamburguesasController;
use App\Http\Controllers\MagnoController;
use App\Http\Controllers\PapasController;
use App\Http\Controllers\MariscosController;
use App\Http\Controllers\RectangularController;
use App\Http\Controllers\RefrescosController;
use App\Http\Controllers\SpaguettyController;
use App\Http\Controllers\EspecialidadesController;
use App\Http\Controllers\BarraController;
use App\Http\Controllers\CategoriasController;
use App\Http\Controllers\SucursalesController;
use App\Http\Controllers\CargosController;
use App\Http\Controllers\ClientesController;
use App\Http\Controllers\VentasController;
use App\Http\Controllers\FlujoCajaController;
use App\Http\Controllers\PedidosController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\PuntoVentaController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PedidosEspecialesController; // <--- NUEVO CONTROLADOR IMPORTADO
use App\Http\Controllers\PermisosController;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------------------------------------------------
| Web Routes - Sistema Pizzetos 
|--------------------------------------------------------------------------
*/

// Redirección inicial
Route::get('/', function () {
    if (auth()->check()) {
        return auth()->user()->id_ca == 1 ? redirect('/dashboard') : redirect('/venta/flujo-caja');
    }
    return redirect('/login');
});

// --- AUTENTICACIÓN ---
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// =====================================================================
// SECCIÓN 1: ACCESO GENERAL (Administrador y Cajeros)
// =====================================================================
Route::middleware(['auth'])->group(function () {
    
    // --- PUNTO DE VENTA (POS) ---
    Route::get('/venta/pos', [PuntoVentaController::class, 'index'])->name('ventas.pos')
        ->middleware('permiso:pos,mostrar');
    Route::post('/venta/pos/guardar', [PuntoVentaController::class, 'store'])->name('ventas.pos.store')
        ->middleware('permiso:pos,crear');
    Route::get('/venta/pos/ticket/{id}', [PuntoVentaController::class, 'ticket'])->name('ventas.pos.ticket')
        ->middleware('permiso:pos,mostrar');
    Route::get('/venta/resume', [VentasController::class, 'resume'])->name('ventas.resume')
        ->middleware('permiso:historial,mostrar');
    Route::post('/venta/pagar', [PuntoVentaController::class, 'pagarOrden'])->name('ventas.pagar')
        ->middleware('permiso:pos,crear');

    // --- SEGURIDAD FINANCIERA (requieren autorización de admin dentro del controlador) ---
    Route::post('/venta/cancelar', [PuntoVentaController::class, 'cancelarPedido'])->name('ventas.cancelar');
    Route::post('/venta/editar-pago', [PuntoVentaController::class, 'editarPago'])->name('ventas.editar_pago');

    // --- MÓDULO PEDIDOS ESPECIALES ---
    Route::get('/pedidos-especiales', [PedidosEspecialesController::class, 'index'])->name('especiales.index')
        ->middleware('permiso:especiales,mostrar');
    Route::post('/venta/pos/especial', [PedidosEspecialesController::class, 'store'])->name('especiales.store')
        ->middleware('permiso:especiales,crear');
    Route::put('/pedidos-especiales/{id}/entregar', [PedidosEspecialesController::class, 'marcarEntregado'])->name('especiales.entregar')
        ->middleware('permiso:especiales,editar');
    Route::post('/pedidos-especiales/{id}/abono', [PedidosEspecialesController::class, 'agregarAbono'])->name('especiales.abono')
        ->middleware('permiso:especiales,editar'); // <--- NUEVA RUTA PARA ABONOS

    // --- MONITOR DE PEDIDOS / REPARTIDOR ---
    Route::get('/venta/pedidos', [PedidosController::class, 'index'])->name('ventas.pedidos')
        ->middleware('permiso:pedidos,mostrar');
    Route::put('/venta/pedidos/{id}/status', [PedidosController::class, 'cambiarStatus'])->name('ventas.pedidos.status')
        ->middleware('permiso:pedidos,editar');

    // --- FLUJO DE CAJA ---
    Route::get('/venta/flujo-caja', [FlujoCajaController::class, 'index'])->name('flujo.caja.index')
        ->middleware('permiso:flujo_caja,mostrar');
    Route::get('/venta/flujo-caja/historial', [FlujoCajaController::class, 'historial'])->name('flujo.caja.historial')
        ->middleware('permiso:flujo_caja,mostrar');
    Route::post('/venta/flujo-caja/abrir', [FlujoCajaController::class, 'abrirCaja'])->name('flujo.caja.abrir')
        ->middleware('permiso:flujo_caja,crear');
    Route::post('/venta/flujo-caja/cerrar/{id}', [FlujoCajaController::class, 'cerrarCaja'])->name('flujo.caja.cerrar')
        ->middleware('permiso:flujo_caja,editar');
    Route::get('/venta/flujo-caja/pdf/{id}', [FlujoCajaController::class, 'descargarPdf'])->name('flujo.caja.pdf')
        ->middleware('permiso:flujo_caja,mostrar');

    // --- CLIENTES (Consulta, Registro, Edición y Direcciones) ---
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index')
        ->middleware('permiso:clientes,mostrar');
    Route::get('/clientes/crear', [ClientesController::class, 'create'])->name('clientes.create')
        ->middleware('permiso:clientes,crear');
    Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store')
        ->middleware('permiso:clientes,crear');
    Route::get('/clientes/{id}/editar', [ClientesController::class, 'edit'])->name('clientes.edit')
        ->middleware('permiso:clientes,editar');
    Route::put('/clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update')
        ->middleware('permiso:clientes,editar');
    Route::post('/clientes/{id}/direcciones', [ClientesController::class, 'storeDireccion'])->name('clientes.storeDireccion')
        ->middleware('permiso:clientes,editar');
    Route::delete('/direcciones/{id}', [ClientesController::class, 'destroyDireccion'])->name('clientes.destroyDireccion')
        ->middleware('permiso:clientes,eliminar');

    // --- GASTOS ---
    Route::get('/venta/gastos', [GastosController::class, 'index'])->name('gastos.index')
        ->middleware('permiso:gastos,mostrar');
    Route::post('/venta/gastos', [GastosController::class, 'store'])->name('gastos.store')
        ->middleware('permiso:gastos,crear');
    Route::delete('/venta/gastos/{id}', [GastosController::class, 'destroy'])->name('gastos.destroy')
        ->middleware('permiso:gastos,eliminar');

});


// =====================================================================
// SECCIÓN 2: ACCESO RESTRINGIDO POR PERMISOS GRANULARES
// Un Admin (id_ca = 1) siempre pasa. Cualquier otro cargo depende de
// lo configurado en Gestión de Personal > Permisos para cada módulo.
// =====================================================================
Route::middleware(['auth'])->group(function () {

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')
        ->middleware('permiso:dashboard,mostrar');

    // --- GESTIÓN AVANZADA DE CLIENTES ---
    Route::put('/clientes/{id}/desactivar', [ClientesController::class, 'destroy'])->name('clientes.destroy')
        ->middleware('permiso:clientes,eliminar');
    Route::put('/clientes/{id}/activar', [ClientesController::class, 'activar'])->name('clientes.activar')
        ->middleware('permiso:clientes,editar');

    // --- GESTIÓN DE EMPLEADOS Y SUS PERMISOS ---
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index')
        ->middleware('permiso:empleados,mostrar');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create')
        ->middleware('permiso:empleados,crear');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store')
        ->middleware('permiso:empleados,crear');
    Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit')
        ->middleware('permiso:empleados,editar');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update')
        ->middleware('permiso:empleados,editar');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy')
        ->middleware('permiso:empleados,eliminar');
    Route::patch('/empleados/{id}/estado', [EmpleadoController::class, 'toggleStatus'])->name('empleados.status')
        ->middleware('permiso:empleados,editar');
    Route::get('/empleados/{id}/permisos', [PermisosController::class, 'edit'])->name('empleados.permisos.edit')
        ->middleware('permiso:empleados,gestionar');
    Route::post('/empleados/{id}/permisos', [PermisosController::class, 'update'])->name('empleados.permisos.update')
        ->middleware('permiso:empleados,gestionar');

    // --- REPORTES Y CORTES CRÍTICOS ---
    Route::get('/corte-mensual', [CorteController::class, 'index'])->name('corte.index')
        ->middleware('permiso:caja,mostrar');
    Route::get('/corte-mensual/dia/{fecha}', [CorteController::class, 'getDetalleDia'])->name('corte.dia')
        ->middleware('permiso:caja,mostrar');

    // --- CATÁLOGO DE PRODUCTOS ---
    Route::prefix('productos')->middleware('permiso:productos,gestionar')->group(function () {
        Route::resource('pizzas', PizzaController::class);
        Route::resource('alitas', AlitasController::class);
        Route::resource('costillas', CostillasController::class);
        Route::resource('hamburguesas', HamburguesasController::class);
        Route::resource('magno', MagnoController::class);
        Route::resource('papas', PapasController::class);
        Route::resource('mariscos', MariscosController::class);
        Route::resource('rectangular', RectangularController::class);
        Route::resource('refrescos', RefrescosController::class);
        Route::resource('spaguetty', SpaguettyController::class);
        Route::resource('especialidades', EspecialidadesController::class);
        Route::resource('barra', BarraController::class);
    });

    // --- RECURSOS DEL SISTEMA ---
    Route::middleware('permiso:recursos,gestionar')->group(function () {
        Route::resource('recursos/categorias', CategoriasController::class);
        Route::resource('recursos/sucursales', SucursalesController::class);
        Route::resource('recursos/cargos', CargosController::class);
    });

    // --- CONFIGURACIÓN ---
    Route::get('/Conf/configuracion', [ConfiguracionController::class, 'index'])->name('ventas.configuracion')
        ->middleware('permiso:configuracion,gestionar');

});