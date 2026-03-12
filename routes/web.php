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
use App\Http\Controllers\AnticiposController;
use App\Http\Controllers\GastosController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\PuntoVentaController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Redirección inicial
Route::get('/', function () {
    return auth()->check() ? redirect('/dashboard') : redirect('/login');
});

// --- RUTAS DE AUTENTICACIÓN ---
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'login']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');


// --- RUTAS PROTEGIDAS
Route::middleware(['auth'])->group(function () {
    
    // Dashboard Principal
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // --- MÓDULO DE EMPLEADOS ---
    Route::get('/empleados', [EmpleadoController::class, 'index'])->name('empleados.index');
    Route::get('/empleados/crear', [EmpleadoController::class, 'create'])->name('empleados.create');
    Route::post('/empleados', [EmpleadoController::class, 'store'])->name('empleados.store');
    Route::get('/empleados/{id}/editar', [EmpleadoController::class, 'edit'])->name('empleados.edit');
    Route::put('/empleados/{id}', [EmpleadoController::class, 'update'])->name('empleados.update');
    Route::delete('/empleados/{id}', [EmpleadoController::class, 'destroy'])->name('empleados.destroy');
    Route::patch('/empleados/{id}/estado', [EmpleadoController::class, 'toggleStatus'])->name('empleados.status');

    // --- MÓDULO DE CORTE MENSUAL ---
    Route::get('/corte-mensual', [CorteController::class, 'index'])->name('corte.index');
    Route::get('/corte-mensual/dia/{fecha}', [CorteController::class, 'getDetalleDia'])->name('corte.dia');

   // --- MÓDULO DE PRODUCTOS: PIZZAS ---
    Route::get('/productos/pizzas', [PizzaController::class, 'index'])->name('pizzas.index');
    Route::get('/productos/pizzas/crear', [PizzaController::class, 'create'])->name('pizzas.create');
    Route::post('/productos/pizzas', [PizzaController::class, 'store'])->name('pizzas.store');
    Route::get('/productos/pizzas/{id}/editar', [PizzaController::class, 'edit'])->name('pizzas.edit');
    Route::put('/productos/pizzas/{id}', [PizzaController::class, 'update'])->name('pizzas.update');
    Route::delete('/productos/pizzas/{id}', [PizzaController::class, 'destroy'])->name('pizzas.destroy');

    // --- MÓDULO DE PRODUCTOS: ALITAS ---
    Route::get('/productos/alitas', [AlitasController::class, 'index'])->name('alitas.index');
    Route::get('/productos/alitas/crear', [AlitasController::class, 'create'])->name('alitas.create');
    Route::post('/productos/alitas', [AlitasController::class, 'store'])->name('alitas.store');
    Route::get('/productos/alitas/{id}/editar', [AlitasController::class, 'edit'])->name('alitas.edit');
    Route::put('/productos/alitas/{id}', [AlitasController::class, 'update'])->name('alitas.update');
    Route::delete('/productos/alitas/{id}', [AlitasController::class, 'destroy'])->name('alitas.destroy');

    // --- MÓDULO DE PRODUCTOS: COSTILLAS ---
    Route::get('/productos/costillas', [CostillasController::class, 'index'])->name('costillas.index');
    Route::get('/productos/costillas/crear', [CostillasController::class, 'create'])->name('costillas.create');
    Route::post('/productos/costillas', [CostillasController::class, 'store'])->name('costillas.store');
    Route::get('/productos/costillas/{id}/editar', [CostillasController::class, 'edit'])->name('costillas.edit');
    Route::put('/productos/costillas/{id}', [CostillasController::class, 'update'])->name('costillas.update');
    Route::delete('/productos/costillas/{id}', [CostillasController::class, 'destroy'])->name('costillas.destroy');

    // --- MÓDULO DE PRODUCTOS: HAMBURGUESAS ---
    Route::get('/productos/hamburguesas', [HamburguesasController::class, 'index'])->name('hamburguesas.index');
    Route::get('/productos/hamburguesas/crear', [HamburguesasController::class, 'create'])->name('hamburguesas.create');
    Route::post('/productos/hamburguesas', [HamburguesasController::class, 'store'])->name('hamburguesas.store');
    Route::get('/productos/hamburguesas/{id}/editar', [HamburguesasController::class, 'edit'])->name('hamburguesas.edit');
    Route::put('/productos/hamburguesas/{id}', [HamburguesasController::class, 'update'])->name('hamburguesas.update');
    Route::delete('/productos/hamburguesas/{id}', [HamburguesasController::class, 'destroy'])->name('hamburguesas.destroy');

    // --- MÓDULO DE PRODUCTOS: MAGNO ---
    Route::get('/productos/magno', [MagnoController::class, 'index'])->name('magno.index');
    Route::get('/productos/magno/crear', [MagnoController::class, 'create'])->name('magno.create');
    Route::post('/productos/magno', [MagnoController::class, 'store'])->name('magno.store');
    Route::get('/productos/magno/{id}/editar', [MagnoController::class, 'edit'])->name('magno.edit');
    Route::put('/productos/magno/{id}', [MagnoController::class, 'update'])->name('magno.update');
    Route::delete('/productos/magno/{id}', [MagnoController::class, 'destroy'])->name('magno.destroy');

    // --- MÓDULO DE PRODUCTOS: PAPAS ---
    Route::get('/productos/papas', [PapasController::class, 'index'])->name('papas.index');
    Route::get('/productos/papas/crear', [PapasController::class, 'create'])->name('papas.create');
    Route::post('/productos/papas', [PapasController::class, 'store'])->name('papas.store');
    Route::get('/productos/papas/{id}/editar', [PapasController::class, 'edit'])->name('papas.edit');
    Route::put('/productos/papas/{id}', [PapasController::class, 'update'])->name('papas.update');
    Route::delete('/productos/papas/{id}', [PapasController::class, 'destroy'])->name('papas.destroy');

    // --- MÓDULO DE PRODUCTOS: MARISCOS ---
    Route::get('/productos/mariscos', [MariscosController::class, 'index'])->name('mariscos.index');
    Route::get('/productos/mariscos/crear', [MariscosController::class, 'create'])->name('mariscos.create');
    Route::post('/productos/mariscos', [MariscosController::class, 'store'])->name('mariscos.store');
    Route::get('/productos/mariscos/{id}/editar', [MariscosController::class, 'edit'])->name('mariscos.edit');
    Route::put('/productos/mariscos/{id}', [MariscosController::class, 'update'])->name('mariscos.update');
    Route::delete('/productos/mariscos/{id}', [MariscosController::class, 'destroy'])->name('mariscos.destroy');

    // --- MÓDULO DE PRODUCTOS: RECTANGULAR ---
    Route::get('/productos/rectangular', [RectangularController::class, 'index'])->name('rectangular.index');
    Route::get('/productos/rectangular/crear', [RectangularController::class, 'create'])->name('rectangular.create');
    Route::post('/productos/rectangular', [RectangularController::class, 'store'])->name('rectangular.store');
    Route::get('/productos/rectangular/{id}/editar', [RectangularController::class, 'edit'])->name('rectangular.edit');
    Route::put('/productos/rectangular/{id}', [RectangularController::class, 'update'])->name('rectangular.update');
    Route::delete('/productos/rectangular/{id}', [RectangularController::class, 'destroy'])->name('rectangular.destroy');

    // --- MÓDULO DE PRODUCTOS: REFRESCOS ---
    Route::get('/productos/refrescos', [RefrescosController::class, 'index'])->name('refrescos.index');
    Route::get('/productos/refrescos/crear', [RefrescosController::class, 'create'])->name('refrescos.create');
    Route::post('/productos/refrescos', [RefrescosController::class, 'store'])->name('refrescos.store');
    Route::get('/productos/refrescos/{id}/editar', [RefrescosController::class, 'edit'])->name('refrescos.edit');
    Route::put('/productos/refrescos/{id}', [RefrescosController::class, 'update'])->name('refrescos.update');
    Route::delete('/productos/refrescos/{id}', [RefrescosController::class, 'destroy'])->name('refrescos.destroy');

    // --- MÓDULO DE PRODUCTOS: SPAGUETTY ---
    Route::get('/productos/spaguetty', [SpaguettyController::class, 'index'])->name('spaguetty.index');
    Route::get('/productos/spaguetty/crear', [SpaguettyController::class, 'create'])->name('spaguetty.create');
    Route::post('/productos/spaguetty', [SpaguettyController::class, 'store'])->name('spaguetty.store');
    Route::get('/productos/spaguetty/{id}/editar', [SpaguettyController::class, 'edit'])->name('spaguetty.edit');
    Route::put('/productos/spaguetty/{id}', [SpaguettyController::class, 'update'])->name('spaguetty.update');
    Route::delete('/productos/spaguetty/{id}', [SpaguettyController::class, 'destroy'])->name('spaguetty.destroy');

    // --- MÓDULO DE PRODUCTOS: ESPECIALIDADES ---
    Route::get('/productos/especialidades', [EspecialidadesController::class, 'index'])->name('especialidades.index');
    Route::get('/productos/especialidades/crear', [EspecialidadesController::class, 'create'])->name('especialidades.create');
    Route::post('/productos/especialidades', [EspecialidadesController::class, 'store'])->name('especialidades.store');
    Route::get('/productos/especialidades/{id}/editar', [EspecialidadesController::class, 'edit'])->name('especialidades.edit');
    Route::put('/productos/especialidades/{id}', [EspecialidadesController::class, 'update'])->name('especialidades.update');
    Route::delete('/productos/especialidades/{id}', [EspecialidadesController::class, 'destroy'])->name('especialidades.destroy');

    // --- MÓDULO DE PRODUCTOS: BARRA ---
    Route::get('/productos/barra', [BarraController::class, 'index'])->name('barra.index');
    Route::get('/productos/barra/crear', [BarraController::class, 'create'])->name('barra.create');
    Route::post('/productos/barra', [BarraController::class, 'store'])->name('barra.store');
    Route::get('/productos/barra/{id}/editar', [BarraController::class, 'edit'])->name('barra.edit');
    Route::put('/productos/barra/{id}', [BarraController::class, 'update'])->name('barra.update');
    Route::delete('/productos/barra/{id}', [BarraController::class, 'destroy'])->name('barra.destroy');

    // --- MÓDULO DE RECURSOS:  CATEGORÍAS ---
    Route::get('/recursos/categorias', [CategoriasController::class, 'index'])->name('categorias.index');
    Route::get('/recursos/categorias/crear', [CategoriasController::class, 'create'])->name('categorias.create');
    Route::post('/recursos/categorias', [CategoriasController::class, 'store'])->name('categorias.store');
    Route::get('/recursos/categorias/{id}/editar', [CategoriasController::class, 'edit'])->name('categorias.edit');
    Route::put('/recursos/categorias/{id}', [CategoriasController::class, 'update'])->name('categorias.update');
    Route::delete('/recursos/categorias/{id}', [CategoriasController::class, 'destroy'])->name('categorias.destroy');

    // --- SUCURSALES ---
    Route::get('/recursos/sucursales', [SucursalesController::class, 'index'])->name('sucursales.index');
    Route::get('/recursos/sucursales/crear', [SucursalesController::class, 'create'])->name('sucursales.create');
    Route::post('/recursos/sucursales', [SucursalesController::class, 'store'])->name('sucursales.store');
    Route::get('/recursos/sucursales/{id}/editar', [SucursalesController::class, 'edit'])->name('sucursales.edit');
    Route::put('/recursos/sucursales/{id}', [SucursalesController::class, 'update'])->name('sucursales.update');
    Route::delete('/recursos/sucursales/{id}', [SucursalesController::class, 'destroy'])->name('sucursales.destroy');

    // --- CARGOS ---
    Route::get('/recursos/cargos', [CargosController::class, 'index'])->name('cargos.index');
    Route::get('/recursos/cargos/crear', [CargosController::class, 'create'])->name('cargos.create');
    Route::post('/recursos/cargos', [CargosController::class, 'store'])->name('cargos.store');
    Route::get('/recursos/cargos/{id}/editar', [CargosController::class, 'edit'])->name('cargos.edit');
    Route::put('/recursos/cargos/{id}', [CargosController::class, 'update'])->name('cargos.update');
    Route::delete('/recursos/cargos/{id}', [CargosController::class, 'destroy'])->name('cargos.destroy');

    // --- CLIENTES ---
    Route::get('/clientes', [ClientesController::class, 'index'])->name('clientes.index');
    Route::get('/clientes/crear', [ClientesController::class, 'create'])->name('clientes.create');
    Route::post('/clientes', [ClientesController::class, 'store'])->name('clientes.store');
    Route::get('/clientes/{id}/editar', [ClientesController::class, 'edit'])->name('clientes.edit');
    Route::put('/clientes/{id}', [ClientesController::class, 'update'])->name('clientes.update');
    Route::put('/clientes/{id}/desactivar', [ClientesController::class, 'destroy'])->name('clientes.destroy'); 
    Route::put('/clientes/{id}/activar', [ClientesController::class, 'activar'])->name('clientes.activar'); 
    Route::post('/clientes/{id}/direcciones', [ClientesController::class, 'storeDireccion'])->name('clientes.storeDireccion');
    Route::delete('/direcciones/{id}', [ClientesController::class, 'destroyDireccion'])->name('clientes.destroyDireccion');

    // =====================================================================
    // MÓDULO DE VENTAS
    // =====================================================================
    Route::get('/venta/resume', [VentasController::class, 'resume'])->name('ventas.resume');

    // --- FLUJO DE CAJA ---
    Route::get('/venta/flujo-caja', [FlujoCajaController::class, 'index'])->name('flujo.caja.index');
    Route::get('/venta/flujo-caja/historial', [FlujoCajaController::class, 'historial'])->name('flujo.caja.historial');
    Route::post('/venta/flujo-caja/abrir', [FlujoCajaController::class, 'abrirCaja'])->name('flujo.caja.abrir');
    Route::post('/venta/flujo-caja/cerrar/{id}', [FlujoCajaController::class, 'cerrarCaja'])->name('flujo.caja.cerrar');
    Route::get('/venta/flujo-caja/pdf/{id}', [FlujoCajaController::class, 'descargarPdf'])->name('flujo.caja.pdf');

    // --- MONITOR DE PEDIDOS (COCINA/ENTREGA) ---
    Route::get('/venta/pedidos', [PedidosController::class, 'index'])->name('ventas.pedidos');
    Route::put('/venta/pedidos/{id}/status', [PedidosController::class, 'cambiarStatus'])->name('ventas.pedidos.status');

    // --- PEDIDOS ESPECIALES / ANTICIPOS ---
    Route::get('/venta/anticipos', [AnticiposController::class, 'index'])->name('ventas.anticipos');

    // --- GASTOS ---
    Route::get('/venta/gastos', [GastosController::class, 'index'])->name('gastos.index');
    Route::post('/venta/gastos', [GastosController::class, 'store'])->name('gastos.store');
    Route::delete('/venta/gastos/{id}', [GastosController::class, 'destroy'])->name('gastos.destroy');

    // --- CONFIGURACIÓN POS ---
    Route::get('/Conf/configuracion', [ConfiguracionController::class, 'index'])->name('ventas.configuracion');
    
    // --- PUNTO DE VENTA (POS) ---
    Route::get('/venta/pos', [PuntoVentaController::class, 'index'])->name('ventas.pos');
    Route::post('/venta/pos/guardar', [PuntoVentaController::class, 'store'])->name('ventas.pos.store');
    Route::get('/venta/pos/ticket/{id}', [PuntoVentaController::class, 'ticket'])->name('ventas.pos.ticket');
    
    // --- NUEVAS RUTAS DE PAGOS Y CANCELACIONES ---
    Route::post('/venta/pagar', [PuntoVentaController::class, 'pagarOrden'])->name('ventas.pagar');
    Route::post('/venta/cancelar', [PuntoVentaController::class, 'cancelarPedido'])->name('ventas.cancelar');
    Route::post('/venta/editar-pago', [PuntoVentaController::class, 'editarPago'])->name('ventas.editar_pago');

});