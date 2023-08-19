<?php


use App\Http\Controllers\AsignacionController;
use App\Http\Controllers\CajaAperturaController;
use App\Http\Controllers\CajaCierreController;
use App\Http\Controllers\CitaController;
use App\Http\Controllers\CitaDetalleController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ClienteVIPController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\CompraDetalleController;
use App\Http\Controllers\ConsumoInternoController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EmpleadoController;
use App\Http\Controllers\GastoController;
use App\Http\Controllers\GastoTipoController;
use App\Http\Controllers\GlobalSystemController;
use App\Http\Controllers\HerramientaController;
use App\Http\Controllers\InventarioController;
use App\Http\Controllers\InventarioDetalleController;
use App\Http\Controllers\KardexController;
use App\Http\Controllers\OpinionController;
use App\Http\Controllers\PagoAdicionalController;
use App\Http\Controllers\PagoAdicionalTipoController;
use App\Http\Controllers\PagoLocalController;
use App\Http\Controllers\PagoPersonalController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\ProductoCategoriaController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\ProductoPresentacionController;
use App\Http\Controllers\PromocionController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\ServicioController;
use App\Http\Controllers\UnidadMedidaController;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\VentaDetalleController;
use App\Models\PagoAdicional;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// MANTENIMIENTO
Route::apiResources([
    'asignaciones' => AsignacionController::class,
    'caja-aperturas' => CajaAperturaController::class,
    'caja-cierres' => CajaCierreController::class,
    'citas' => CitaController::class,
    'citas-detalle' => CitaDetalleController::class,
    'clientes' => ClienteController::class,
    'clientes-vip' => ClienteVIPController::class,
    'compras' => CompraController::class,
    'compras-detalle' => CompraDetalleController::class,
    'consumos-internos' => ConsumoInternoController::class,
    'empleados' => EmpleadoController::class,
    'gastos' => GastoController::class,
    'gastos-tipo' => GastoTipoController::class,
    'herramientas' => HerramientaController::class,
    'inventarios' => InventarioController::class,
    'inventarios-detalle' => InventarioDetalleController::class,
    'kardex' => KardexController::class,
    'opiniones' => OpinionController::class,
    'pago-local' => PagoLocalController::class,
    'pago-adicional' => PagoAdicionalController::class,
    'pago-adicional-tipo' => PagoAdicionalTipoController::class,
    'permisos' => PermisoController::class,
    'productos-categoria' => ProductoCategoriaController::class,
    'productos' => ProductoController::class,
    'promociones' => PromocionController::class,
    'proveedores' => ProveedorController::class,
    'roles' => RolController::class,
    'servicios' => ServicioController::class,
    'unidades_medida' => UnidadMedidaController::class,
    'usuarios' => UsuarioController::class,
    'ventas' => VentaController::class,
    'ventas-detalle' => VentaDetalleController::class
]);

Route::post('login', [UsuarioController::class, 'login']);

// REPORTES
Route::get('reportes/cierre-caja/{fecha}', [ReporteController::class, 'cierreCaja']);
Route::get('reportes/pago-empleados/{fecha}', [ReporteController::class, 'pagoEmpleados']);
Route::get('reportes/gastos/mensual/{desde}/{hasta}', [ReporteController::class, 'gastos']);
Route::get('reportes/stock', [ReporteController::class, 'reporteStock']);
Route::get('reportes/consumos-internos/{fechaDesde}/{fechaHasta}', [ReporteController::class, 'consumosInternos']);
Route::get('reportes/ventas/{fechaDesde}/{fechaHasta}/{metodoPago?}/{productoId?}/{servicioId?}', [ReporteController::class, 'ventas']);
Route::get('reportes/citas/{clienteId}/{fechaDesde}/{fechaHasta}', [ReporteController::class, 'citasAtendidas']);
Route::get('reportes/rentabilidad/{fechaDesde}/{fechaHasta}', [ReporteController::class, 'rentabilidad']);
Route::get('reportes/resumen/{fecha}', [ReporteController::class, 'resumenServicios']);
Route::get('reportes/ganancia-neta/{fechaDesde}/{fechaHasta}', [ReporteController::class, 'gananciaNeta']);

// PRODUCTOS
Route::get('productos/activos/{activos}', [ProductoController::class, 'indexActivos']);
Route::get('productos/id/{id}', [ProductoController::class, 'getById']);
Route::get('productos/search/{tipo}/{query?}', [ProductoController::class, 'search']);
Route::get('productos-servicios', [ProductoController::class, 'productosServicios']);
Route::get('productos-presentaciones/{id}', [ProductoPresentacionController::class, 'getByProducto']);
Route::post('productos-presentaciones', [ProductoPresentacionController::class, 'save']);
Route::put('productos-presentaciones/{id}', [ProductoPresentacionController::class, 'update']);
Route::delete('productos-presentaciones/{id}', [ProductoPresentacionController::class, 'delete']);

// ESPECÍFICOS
Route::get('citas/update-detalle/{id}', [CitaController::class, 'updateDetalle']);
Route::get('servicios/activos/{activos}', [ServicioController::class, 'indexActivos']);
Route::get('clientes/search/query/{query?}', [ClienteController::class, 'search']);
Route::get('proveedores/search/query/{query?}', [ProveedorController::class, 'search']);
Route::get('servicios/search/query/{query?}', [ServicioController::class, 'search']);
Route::get('ventas/archivo/{from}/{to}/{cajero?}', [VentaController::class, 'archivo']);
Route::get('dashboard/{fecha}', [DashboardController::class, 'index']);

Route::get('citas/repro/{id}/{from}/{to}', [CitaController::class, 'reprogram']);

Route::put('usuarios/admin/update', [UsuarioController::class, 'updateAdmin']);
Route::get('usuarios/cajeros/activos', [UsuarioController::class, 'cajerosActivos']);
Route::put('citas/update-estado/{id}', [CitaController::class, 'updateEstado']);

Route::put('pago-personal/actualizar', [PagoPersonalController::class, 'actualizarPagos']);

Route::get('empleados/cargo/{cargo}', [EmpleadoController::class, 'getByCargo']);

Route::get('promociones/tipo/especificos', [PromocionController::class, 'especificos']);
Route::put('promociones/tipo/recomendaciones', [PromocionController::class, 'updatePromoRecom']);
Route::put('promociones/tipo/cliente-vip', [PromocionController::class, 'updatePromoVip']);
Route::put('promociones/tipo/cumpleanios', [PromocionController::class, 'updatePromoCumple']);
Route::put('promociones/tipo/trabajadores', [PromocionController::class, 'updatePromoTrabajador']);
