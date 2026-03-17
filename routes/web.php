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
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

/*
|--------------------------------
| Web Routes - Sistema Pizzetos 
|--------------------------------
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
    Route::get('/venta/pos', [PuntoVentaController::class, 'index'])->name('ventas.pos');
    Route::post('/venta/pos/guardar', [PuntoVentaController::class, 'store'])->name('ventas.pos.store');
    Route::get('/venta/pos/ticket/{id}', [PuntoVentaController::class, 'ticket'])->name('ventas.pos.ticket');
    Route::get('/venta/resume', [VentasController::class, 'resume'])->name('ventas.resume');
    Route::post('/venta/pagar', [PuntoVentaController::class, 'pagarOrden'])->name('ventas.pagar');

    // --- MONITOR DE PEDIDOS / REPARTIDOR ---
    Route::get('/venta/pedidos', [PedidosController::class, 'index'])->name('ventas.pedidos');
    Route::put('/venta/pedidos/{id}/status', [PedidosController::class, 'cambiarStatus'])->name('ventas.pedidos.status');

    // --- FLUJO DE CAJA ---
    Route::get('/venta/flujo-caja', [FlujoCajaController::class, 'index'])->name('flujo.caja.index');
    // 👇 RUTA MOVIDA: Ahora los cajeros tienen acceso al historial de cajas
    Route::get('/venta/flujo-caja/historial', [FlujoCajaController::class, 'historial'])->name('flujo.caja.historial');
    Route::post('/venta/flujo-caja/abrir', [FlujoCajaController::class, 'abrirCaja'])->name('flujo.caja.abrir');
    Route::post('/venta/flujo-caja/cerrar/{id}', [FlujoCajaController::class, 'cerrarCaja'])->name('flujo.caja.cerrar');
    Route::get('/venta/flujo-caja/pdf/{id}', [FlujoCajaController::class, 'descargarPdf'])->name('flujo.caja.pdf');

    // --- CLIENTES (Consulta y Registro) ---
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClientesController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');

    // --- GASTOS (Visible para Cajeros y Admin) ---
    Route::get('/venta/gastos', [GastosController::class, 'index'])->name('gastos.index');
    Route::post('/venta/gastos', [GastosController::class, 'store'])->name('gastos.store');
    Route::delete('/venta/gastos/{id}', [GastosController::class, 'destroy'])->name('gastos.destroy');

});


// =====================================================================
// SECCIÓN 2: ACCESO RESTRINGIDO (Solo Administrador)
// =====================================================================
Route::middleware(['auth', 'admin'])->group(function () {

    // DASHBOARD CONECTADO AL CONTROLADOR (Movido a la sección de Admin)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // --- SEGURIDAD FINANCIERA ---
    Route::post('/venta/cancelar', [PuntoVentaController::class, 'cancelarPedido'])->name('ventas.cancelar');
    Route::post('/venta/editar-pago', [PuntoVentaController::class, 'editarPago'])->name('ventas.editar_pago');

    // --- GESTIÓN AVANZADA DE CLIENTES ---
    Route::get('/clientes/{id}/editar', [ClientesController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update');
    Route::put('/clientes/{id}/desactivar', [ClientesController::class, 'destroy'])->name('clientes.destroy'); 
    Route::put('/clientes/{id}/activar', [ClientesController::class, 'activar'])->name('clientes.activar'); 
    Route::post('/clientes/{id}/direcciones', [ClientesController::class, 'storeDireccion'])->name('clientes.storeDireccion');
    Route::delete('/direcciones/{id}', [ClientesController::class, 'destroyDireccion'])->name('clientes.destroyDireccion');

    // --- GESTIÓN DE EMPLEADOS ---
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    Route::patch('/empleados/{id}/estado', [EmpleadoController::class, 'toggleStatus'])->name('empleados.status');

    // --- REPORTES Y CORTES CRÍTICOS ---
    Route::get('/corte-mensual', [CorteController::class, 'index'])->name('corte.index');
    Route::get('/corte-mensual/dia/{fecha}', [CorteController::class, 'getDetalleDia'])->name('corte.dia');
    // 👆 Se eliminó el historial de cajas de aquí para que no choque

    // --- CATÁLOGO DE PRODUCTOS ---
    Route::prefix('productos')->group(function () {
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
    Route::resource('recursos/categorias', CategoriasController::class);
    Route::resource('recursos/sucursales', SucursalesController::class);
    Route::resource('recursos/cargos', CargosController::class);

    // --- CONFIGURACIÓN ---
    Route::get('/Conf/configuracion', [ConfiguracionController::class, 'index'])->name('ventas.configuracion');

});