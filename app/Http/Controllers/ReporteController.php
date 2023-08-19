<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\CajaCierre;
use App\Models\Cita;
use App\Models\Gasto;
use App\Models\PagoPersonal;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spipu\Html2Pdf\Html2Pdf;

class ReporteController extends Controller
{
    public function cierreCaja($fecha) {
        // APERTURA DE CAJA ================================================================================
        $aperturaCaja = CajaApertura::where('fecha', $fecha)->first();
        $aperturaCaja = $aperturaCaja ? (double)$aperturaCaja->total : 0;
        
        // CIERRE DE CAJA ==================================================================================
        $cierreCaja = CajaCierre::where('fecha', $fecha)->first();
        $cierreCajaTotal = $cierreCaja ? (double)$cierreCaja->total : 0;

        // SERVICIOS =======================================================================================
        $citas = Cita::where('fecha', $fecha)->get();
        $totalCitasEfectivo = 0;
        $totalCitasTarjeta = 0;
        // $pagoComisionServicios = 0;

        foreach ($citas as $tmpCita) {
            if ($tmpCita->estado == 'ATENDIDO') {
                if ($tmpCita->metodo_pago == 'TARJETA') {
                    $totalCitasTarjeta += (double) $tmpCita->total;
                } else {
                    $totalCitasEfectivo += (double) $tmpCita->total;
                }
            }
        }

        $totalServicios = $totalCitasEfectivo;

        // VENTAS ========================================================================================
        $ventas = Venta::where('fecha', $fecha)->get();
        $totalVentasEfectivo = 0;
        $totalVentasTarjeta = 0;
        // $pagoComisionVentas = 0;
        
        foreach ($ventas as $tmpVenta) {
            if ($tmpVenta->tipo_venta == 'EFECTIVO') {
                $totalVentasEfectivo += (double) $tmpVenta->total;
            } elseif ($tmpVenta->tipo_venta == 'TARJETA') {
                $totalVentasTarjeta += (double) $tmpVenta->total;
            }
        }
        
        $totalVentas = $totalVentasEfectivo;
        
        // TARJETA =======================================================================================
        $totalTarjeta = $totalCitasTarjeta + $totalVentasTarjeta;


        // GASTOS ========================================================================================
        $gastos = Gasto::where('fecha', $fecha)->get();
        $totalGastos = 0;
        foreach ($gastos as $tmpGasto) {
            $totalGastos += (double) $tmpGasto->total;
        }

        // PAGO PERSONAL =================================================================================

        $total_comision_servicios = 0;
        $total_comision_productos = 0;
        $total_comision = 0;
        $sql = "SELECT v.fecha, v.empleado_id, v.empleado_nombre, SUM(v.pago_barbero) AS 'pago_barbero', IFNULL((SELECT 1 FROM pago_personal pp WHERE pp.empleado = v.empleado_id AND pp.fecha = v.fecha LIMIT 1), 0) AS 'pagado'
                FROM view_servicios_ventas v
                WHERE v.empleado_cargo = 1 AND v.fecha = ?
                GROUP BY v.fecha, v.empleado_id, v.empleado_nombre
                ORDER BY v.fecha, v.empleado_nombre;";

        $registros = DB::select($sql, [$fecha]);

        foreach ($registros as $tmp) {
            $detalle = $this->_getPagoEmpleado($tmp->empleado_id, $fecha);
            $tmp->detalle = $detalle;
            $tmp->pago_barbero = (double) $detalle['pago']['comision_total'];

            if ($tmp->pagado == 1) {
                $total_comision_servicios += (double) $detalle['pago']['comision_servicios'];
                $total_comision_productos += (double) $detalle['pago']['comision_productos'];
                $total_comision += (double) $detalle['pago']['comision_total'];
            }
        }

        // TOTAL CAJA ====================================================================================
        $totalCaja = $aperturaCaja + $totalServicios + $totalVentas - $totalGastos - $total_comision + $totalTarjeta;

        $registroLiquid = $cierreCajaTotal + $totalTarjeta;

        $diferencia = $totalCaja - $registroLiquid;

        return response()->json([
            'fecha'     => $fecha,
            'apertura'  => $aperturaCaja,
            'cierre'    => [
                'detalle' => $cierreCaja,
                'total'   => $cierreCajaTotal
            ],
            'servicios' => [
                'total_efectivo'      => $totalCitasEfectivo,
                'total_servicios'     => $totalServicios
            ],
            'ventas'    => [
                'total_efectivo'      => $totalVentasEfectivo,
                'total_ventas'        => $totalVentas
            ],
            'pago_personal' => [
                'pago_servicios' => $total_comision_servicios,
                'pago_ventas' => $total_comision_productos,
                'total_pago' => $total_comision
            ],
            'tarjeta' => [
                'servicios'     => $totalCitasTarjeta,
                'ventas'        => $totalVentasTarjeta,
                'total_tarjeta' => $totalTarjeta
            ],
            'gastos'    => [
                'detalle' => $gastos,
                'total'   => $totalGastos
            ],
            'total_caja' => $totalCaja,
            'liquidacion' => $registroLiquid,
            'diferencia' => [
                'descripcion' => $diferencia >= 0 ? 'Faltante' : 'Sobrante',
                'monto'       => abs($diferencia)
            ]
        ]);
    }

    public function pagoEmpleados($fecha) {

        $total_comision_servicios = 0;
        $total_comision_productos = 0;
        $total_comision = 0;
        $sql = "SELECT v.fecha, v.empleado_id, v.empleado_nombre, SUM(v.pago_barbero) AS 'pago_barbero', IFNULL((SELECT 1 FROM pago_personal pp WHERE pp.empleado = v.empleado_id AND pp.fecha = v.fecha LIMIT 1), 0) AS 'pagado'
                FROM view_servicios_ventas v
                WHERE v.empleado_cargo = 1 AND v.fecha = ?
                GROUP BY v.fecha, v.empleado_id, v.empleado_nombre
                ORDER BY v.fecha, v.empleado_nombre;";

        $registros = DB::select($sql, [$fecha]);

        foreach ($registros as $tmp) {
            $detalle = $this->_getPagoEmpleado($tmp->empleado_id, $fecha);
            $tmp->detalle = $detalle;
            $tmp->pago_barbero = (double) $detalle['pago']['comision_total'];

            $total_comision_servicios += (double) $detalle['pago']['comision_servicios'];
            $total_comision_productos += (double) $detalle['pago']['comision_productos'];
            $total_comision += (double) $detalle['pago']['comision_total'];
        }

        return response()->json([
            'fecha' => $fecha,
            'pago_empleados' => $registros,
            'total' => [
                'pago_servicios' => $total_comision_servicios,
                'pago_productos' => $total_comision_productos,
                'pago_personal' => $total_comision
            ]
        ]);
    }

    public function _pagado($empleado_id, $fecha) {
        $pagado = PagoPersonal::where('empleado', $empleado_id)->where('fecha', $fecha)->first();
        $pagado = $pagado ? true : false;
        return $pagado;
    }

    public function _getPagoEmpleado($empleado_id, $fecha) {

        $comision_por_servicios = 0;
        $comision_por_productos = 0;
        $comision_total = 0;

        $sql = "SELECT v.empleado_nombre, SUM(v.cantidad) AS 'cantidad', v.concepto_descripcion, v.precio, v.promo_id, v.promo_nombre, v.descuento, SUM(v.importe) AS 'subtotal', v.comision_barbero, SUM(v.pago_barbero) AS 'pago_barbero'
                FROM view_servicios_ventas v
                WHERE v.fecha = ? AND v.empleado_id = ? AND v.tipo = 'SERVICIOS'
                GROUP BY v.empleado_id, v.concepto_id, v.precio, v.promo_id, v.descuento, v.importe, v.empleado_nombre, v.concepto_descripcion, v.promo_nombre, v.comision_barbero
                ORDER BY v.concepto_descripcion";
        $detalleServicios = DB::select($sql, [$fecha, $empleado_id]);

        $sql = "SELECT v.empleado_nombre, SUM(v.cantidad) AS 'cantidad', v.concepto_descripcion, v.concepto_unidad, v.precio, v.promo_id, v.promo_nombre, v.descuento, SUM(v.importe) AS 'subtotal', v.comision_barbero, SUM(v.pago_barbero) AS 'pago_barbero'
                FROM view_servicios_ventas v
                WHERE v.fecha = ? AND v.empleado_id = ? AND v.tipo = 'VENTAS'
                GROUP BY v.empleado_id, v.concepto_id, v.precio, v.promo_id, v.descuento, v.importe, v.empleado_nombre, v.concepto_descripcion, v.promo_nombre, v.comision_barbero, v.concepto_unidad
                ORDER BY v.concepto_descripcion";
        $detalleVentas = DB::select($sql, [$fecha, $empleado_id]);

        // Procesar servicios realizados
        foreach ($detalleServicios as $tmp) {
            $comision_por_servicios += $tmp->pago_barbero;
        }

        foreach ($detalleVentas as $tmp) {
            $comision_por_productos += $tmp->pago_barbero;
        }
        $comision_por_servicios = $this->_redondear($comision_por_servicios);
        $comision_por_productos = $this->_redondear($comision_por_productos);
        $comision_total = $comision_por_servicios + $comision_por_productos;

        return [
            'servicios' => $detalleServicios,
            'ventas' => $detalleVentas,
            'pago' => [
                'comision_servicios' => $comision_por_servicios,
                'comision_productos' => $comision_por_productos,
                'comision_total' => $comision_total
            ]
        ];
    }

    public function consumosInternos($fechaDesde, $fechaHasta) {
        $data = $this->_getConsumosInternos($fechaDesde, $fechaHasta);
        return response()->json($data);
    }

    public function _getConsumosInternosEmpleado($fechaDesde, $fechaHasta, $empleadoId) {
        $sql = "SELECT ci.fecha, e.nombres AS 'empleado', ci.cantidad, p.nombre, um.unidad, pp.precio_venta, ci.cantidad * pp.precio_venta AS 'total'
                FROM consumos_internos ci
                INNER JOIN empleados e ON ci.empleado_id = e.id
                INNER JOIN productos_presentaciones pp ON ci.producto_id = pp.id
                INNER JOIN productos p ON pp.producto_id = p.id
                INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                WHERE e.id = ? AND (ci.fecha BETWEEN ? AND ?)
                ORDER BY 1, 2, 4, 5";

        $detalle = DB::select($sql, [$empleadoId, $fechaDesde, $fechaHasta]);

        return $detalle;
    }

    public function _getConsumosInternos($fechaDesde, $fechaHasta) {
        $total = 0;
        $sql = "SELECT e.id AS 'empleado_id', CONCAT(e.nombres,' ',e.ape_paterno,' ',e.ape_materno) AS 'empleado_nombre', SUM(ci.cantidad * pp.precio_venta) AS 'total'
                FROM consumos_internos ci
                INNER JOIN empleados e ON ci.empleado_id = e.id
                INNER JOIN productos_presentaciones pp ON ci.producto_id = pp.id
                WHERE ci.fecha BETWEEN ? AND ?
                GROUP BY ci.empleado_id, e.id, e.nombres, e.ape_paterno, e.ape_materno
                ORDER BY 2;";
        $registros = DB::select($sql, [$fechaDesde, $fechaHasta]);

        foreach ($registros as $tmp) {
            $tmp->consumos = $this->_getConsumosInternosEmpleado($fechaDesde, $fechaHasta, $tmp->empleado_id);
            $total += (float)$tmp->total;
        }

        return [
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'registros' => $registros,
            'total' => $total
        ];
    }

    public function ventas($fechaDesde, $fechaHasta, $metodoPago = -1, $productoId = '-1', $servicioId = '-1') {
        $tempTable = "";
        $citasBldr  = DB::table('citas')
        ->join('citas_detalles', 'citas.id', '=', 'citas_detalles.cita')
        ->join('servicios', 'servicios.id', '=', 'citas_detalles.servicio')
        ->where('citas.estado', 'ATENDIDO')
        ->groupBy('citas.fecha', 'citas.metodo_pago')
        ->select('citas.fecha', 'citas.metodo_pago', DB::raw('SUM(citas_detalles.importe) as total'));

        $ventasBldr = DB::table('ventas')
        ->join('ventas_detalles', 'ventas.id', '=', 'ventas_detalles.venta')
        ->join('productos_presentaciones', 'ventas_detalles.producto', '=', 'productos_presentaciones.id')
        ->join('productos', 'productos_presentaciones.producto_id', '=', 'productos.id')
        ->groupBy('ventas.fecha', 'ventas.tipo_venta')
        ->select('ventas.fecha', 'ventas.tipo_venta', DB::raw('SUM(ventas_detalles.importe) as total'));

        if ($metodoPago != -1) {
            $citasBldr->where('citas.metodo_pago', $metodoPago)->groupBy('citas.metodo_pago');
            $ventasBldr->where('ventas.tipo_venta', $metodoPago)->groupBy('ventas.tipo_venta');
        }


        if ($productoId != '-1') {
            $ventasBldr->where('productos.id', $productoId);
        }

        if ($servicioId != '-1') {
            $citasBldr->where('servicios.id', $servicioId);
        }

        $citasBldr->unionAll($ventasBldr);

        $bindings = $citasBldr->getBindings();
        array_push($bindings, $fechaDesde, $fechaHasta);

        $total = 0;
        $registros = [];

        $sql = "SELECT t.fecha, SUM(t.total) AS 'total'
                FROM ({$citasBldr->toSql()}) t
                WHERE t.fecha BETWEEN ? AND ?
                GROUP BY t.fecha
                ORDER BY 1;";
        $registros = DB::select($sql, $bindings);

        foreach ($registros as $tmp) {
            $tmp->ventas = $this->_getVentas($tmp->fecha, $metodoPago, $productoId, $servicioId);
            $total += (float)$tmp->total;
        }

        $ranking = $this->_getVentasStats($fechaDesde, $fechaHasta, $metodoPago, $productoId, $servicioId);

        return response()->json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'registros' => $registros,
            'total' => $total,
            'ranking' => $ranking
        ]);
    }

    public function _getVentas($fecha, $metodoPago, $productoId, $servicioId) {
        $citasBldr  = DB::table('citas')
        ->join('citas_detalles', 'citas.id', '=', 'citas_detalles.cita')
        ->join('servicios', 'servicios.id', '=', 'citas_detalles.servicio')
        ->where('citas.estado', 'ATENDIDO')
        ->groupBy('citas.fecha', 'citas.metodo_pago', 'servicios.id', 'citas_detalles.importe', 'servicios.nombre')
        ->select('citas.fecha', DB::raw("'SERVICIOS' as 'tipo'"), 'citas.metodo_pago', DB::raw('SUM(citas_detalles.cantidad) as cantidad'), DB::raw("servicios.nombre as 'descripcion'"), DB::raw("null as 'unidad'"), DB::raw("SUM(citas_detalles.importe) / SUM(citas_detalles.cantidad) as 'importe'"), DB::raw("SUM(citas_detalles.importe) as 'total'"));

        $ventasBldr = DB::table('ventas')
        ->join('ventas_detalles', 'ventas.id', '=', 'ventas_detalles.venta')
        ->join('productos_presentaciones', 'ventas_detalles.producto', '=', 'productos_presentaciones.id')
        ->join('productos', 'productos_presentaciones.producto_id', '=', 'productos.id')
        ->join('unidades_medida', 'productos_presentaciones.unidad_medida_id', '=', 'unidades_medida.id')
        // ->groupBy('ventas.fecha', 'ventas.tipo_venta', 'productos_presentaciones.id', 'productos.id', 'unidades_medida.id', 'ventas_detalles.id')
        ->groupBy('ventas.fecha', 'ventas.tipo_venta', 'productos_presentaciones.id', 'productos.id', 'unidades_medida.id', 'ventas_detalles.importe', 'productos.nombre', 'unidades_medida.unidad')
        ->select('ventas.fecha', DB::raw("'VENTAS' as 'tipo'"), DB::raw('ventas.tipo_venta as metodo_pago'), DB::raw('SUM(ventas_detalles.cantidad) as cantidad'), DB::raw("productos.nombre as 'descripcion'"), 'unidades_medida.unidad', DB::raw("SUM(ventas_detalles.importe) / SUM(ventas_detalles.cantidad) as 'importe'"), DB::raw("SUM(ventas_detalles.importe) as 'total'"));
        
        if ($metodoPago != -1) {
            $citasBldr->where('citas.metodo_pago', $metodoPago);
            $ventasBldr->where('ventas.tipo_venta', $metodoPago);
        }

        if ($productoId != '-1') {
            $ventasBldr->where('productos.id', $productoId);
        }

        if ($servicioId != '-1') {
            $citasBldr->where('servicios.id', $servicioId);
        }

        $citasBldr->unionAll($ventasBldr);

        $bindings = $citasBldr->getBindings();
        array_push($bindings, $fecha);

        $sql = "SELECT * FROM ({$citasBldr->toSql()}) t
                WHERE t.fecha = ?
                ORDER BY 8 DESC, 1, 3, 5, 7";

        $detalle = DB::select($sql, $bindings);

        return $detalle;
    }

    public function _getVentasStats($fechaDesde, $fechaHasta, $metodoPago, $productoId, $servicioId) {
        $citasBldr  = DB::table('citas')
        ->join('citas_detalles', 'citas.id', '=', 'citas_detalles.cita')
        ->join('servicios', 'servicios.id', '=', 'citas_detalles.servicio')
        ->where('citas.estado', 'ATENDIDO')
        ->whereBetween('citas.fecha', [$fechaDesde, $fechaHasta])
        ->groupBy('servicios.nombre')
        ->select(DB::raw("servicios.nombre as 'descripcion'"), DB::raw('SUM(citas_detalles.cantidad) as cantidad'), DB::raw("null as 'unidad'"), DB::raw("SUM(citas_detalles.importe) as 'total'"));

        $ventasBldr = DB::table('ventas')
        ->join('ventas_detalles', 'ventas.id', '=', 'ventas_detalles.venta')
        ->join('productos_presentaciones', 'ventas_detalles.producto', '=', 'productos_presentaciones.id')
        ->join('productos', 'productos_presentaciones.producto_id', '=', 'productos.id')
        ->join('unidades_medida', 'productos_presentaciones.unidad_medida_id', '=', 'unidades_medida.id')
        ->whereBetween('ventas.fecha', [$fechaDesde, $fechaHasta])
        ->groupBy('productos.nombre', 'unidades_medida.unidad')
        ->select(DB::raw("productos.nombre as 'descripcion'"), DB::raw('SUM(ventas_detalles.cantidad) as cantidad'), 'unidades_medida.unidad', DB::raw("SUM(ventas_detalles.importe) as 'total'"));
        
        if ($metodoPago != -1) {
            $citasBldr->where('citas.metodo_pago', $metodoPago)->groupBy('citas.metodo_pago');
            $ventasBldr->where('ventas.tipo_venta', $metodoPago)->groupBy('ventas.tipo_venta');
            // $citasBldr->groupBy('citas.metodo_pago');
            // $ventasBldr->groupBy('ventas.tipo_venta');
        }

        $citasBldr->groupBy('servicios.id');
        $ventasBldr->groupBy('productos_presentaciones.id', 'productos.id', 'unidades_medida.id');

        if ($productoId != '-1') {
            $ventasBldr->where('productos.id', $productoId);
        }

        if ($servicioId != '-1') {
            $citasBldr->where('servicios.id', $servicioId);
        }

        $citasBldr->unionAll($ventasBldr);

        $bindings = $citasBldr->getBindings();

        $sql = "SELECT * FROM (
                    {$citasBldr->toSql()}
                ) t
                ORDER BY 4 DESC, 1, 2 DESC, 3";

        $detalle = DB::select($sql, $bindings);

        return $detalle;
    }

    public function citasAtendidas($clienteId, $fechaDesde, $fechaHasta) {
        $sql = "SELECT c.fecha,  SUM(c.total) AS 'total'
                FROM citas c
                WHERE c.cliente = ?
                GROUP BY c.fecha
                ORDER BY c.fecha DESC";
        $registros = DB::select($sql, [$clienteId]);

        foreach ($registros as $tmp) {
            $tmp->detalle = $this->_getCitasAtendidas($tmp->fecha, $clienteId);
        }

        return response()->json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'registros' => $registros
        ]);
    }

    public function _getCitasAtendidas($fecha, $clienteId) {
        $sql = "SELECT c.fecha, c.hora, CONCAT(e.nombres,' ',e.ape_paterno, ' ', e.ape_materno) AS 'empleado_nombre', cr.cantidad, s.nombre AS 'descripcion', cr.precio, p.id AS 'promo_id', p.nombre AS 'promo_nombre', cr.descuento, cr.importe
                FROM citas c
                INNER JOIN citas_detalles cr ON c.id = cr.cita
                INNER JOIN servicios s ON cr.servicio = s.id
                LEFT JOIN promociones p ON c.promocion = p.id
                INNER JOIN empleados e ON c.empleado = e.id
                WHERE c.fecha = ? AND c.estado = 'ATENDIDO' AND c.cliente = ?";

        $detalle = DB::select($sql, [$fecha, $clienteId]);

        return $detalle;
    }

    public function resumenServicios($fecha) {
        $monto = 0;
        $montoTarjeta = 0;
        $montoTotal = 0;
        
        $sql = "SELECT
                    c.id,
                    CONCAT(cli.nombres, ' ', cli.apellidos) AS 'cliente',
                    c.hora,
                    emp.nombres AS 'barbero',
                    cd.importe AS 'total',
                    s.nombre AS 'servicio',
                    prom.id AS 'promo_id',
                    prom.nombre AS 'promo_nombre',
                    cd.descuento,
                    c.metodo_pago
                FROM citas c
                INNER JOIN citas_detalles cd ON c.id = cd.cita
                INNER JOIN servicios s ON cd.servicio = s.id
                INNER JOIN clientes cli ON c.cliente = cli.id
                INNER JOIN empleados emp ON c.empleado = emp.id
                LEFT JOIN promociones prom ON c.promocion = prom.id
                WHERE c.estado = 'ATENDIDO' AND c.fecha = ?
                ORDER BY c.hora, cd.id";
        
        $dataServicios = DB::select($sql, [ $fecha ]);
        
        foreach ($dataServicios as $tmp) {
            $monto += (double) $tmp->total;
            if ($tmp->metodo_pago == 'TARJETA') {
                $montoTarjeta += (double) $tmp->total;
            }
        }

        $sql = "SELECT
                    ven.id,
                    ven.hora,
                    usu.nombre_display AS 'vendedor',
                    detalle.cantidad,
                    pro.nombre AS 'producto',
                    um.unidad AS 'unidad_medida',
                    detalle.importe AS 'total',
                    ven.tipo_venta AS 'metodo_pago'
                FROM ventas ven
                INNER JOIN ventas_detalles detalle ON detalle.venta = ven.id
                INNER JOIN productos_presentaciones pre ON pre.id = detalle.producto
                INNER JOIN productos pro ON pre.producto_id = pro.id
                INNER JOIN unidades_medida um ON pre.unidad_medida_id = um.id
                INNER JOIN usuarios usu ON ven.created_by = usu.id
                WHERE ven.fecha = ?
                ORDER BY ven.hora";
        
        $dataVentas = DB::select($sql, [ $fecha ]);
        
        foreach ($dataVentas as $tmp) {
            $monto += (double) $tmp->total;
            if ($tmp->metodo_pago == 'TARJETA') {
                $montoTarjeta += (double) $tmp->total;
            }
        }

        $montoTotal = $monto - $montoTarjeta;
        
        return response()->json([
            'fecha' => $fecha,
            'servicios' => $dataServicios,
            'ventas' => $dataVentas,
            'total' => [
                'subtotal' => $monto,
                'tarjeta' => $montoTarjeta,
                'monto_total' => $montoTotal,
            ]
        ]);
    }

    public function _redondear($valor) {
        $valor = round($valor, 2);
        $array = explode('.', $valor);
        $int = (int)$array[0];
        $dec = 0;
        $resultado = 0.0;

        if (count($array) == 2) {
            $dec = (int)$array[1];
            if ($dec < 10) {
                $dec *= 10;
            }
        }
        
        if ($dec > 0) {
            if ($dec <= 50) {
                $resultado = 0.5;
            } else {
                $int += 1; 
            }
        }

        $resultado += $int;
        return $resultado;
    }

    public function reporteStock() {
        // $sql = "SELECT p.nombre AS 'producto', um.unidad, pp.stock, pp.costo FROM productos_presentaciones pp INNER JOIN productos p ON pp.producto_id = p.id INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id WHERE pp.stock > 0 ORDER BY p.nombre, um.unidad";
        
        $sql = "SELECT pp.id, p.nombre producto, pp.precio_venta AS 'precio', pp.costo, um.unidad, TRUNCATE(alm.cantidad / um.factor, 0) AS 'stock'
                FROM productos_presentaciones pp
                LEFT JOIN productos_codigos pc ON pp.id = pc.producto_id
                INNER JOIN productos p ON pp.producto_id = p.id
                INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                INNER JOIN (
                    SELECT p.id, p.nombre, SUM(um.factor * pp.stock) AS 'cantidad'
                    FROM productos_presentaciones pp
                    INNER JOIN productos p ON pp.producto_id = p.id
                    INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                    GROUP BY p.id, p.nombre
                    ORDER BY p.nombre
                ) alm ON p.id = alm.id
                WHERE (alm.cantidad / um.factor) > 0 AND p.activo = 1
                ORDER BY p.nombre, um.unidad";
        
        $total = 0;
        $data = DB::select($sql);

        foreach ($data as $row) {
            $total += $row->costo;
        }

        return response()->json([
            'detalle' => $data,
            'total' => $total
        ]);
    }

    public function gastos($desde, $hasta) {
        $data = $this->_getGastos($desde, $hasta);

        return response()->json($data);
    }

    public function _getGastos($desde, $hasta) {
        $sql = "SELECT gt.id, gt.nombre, SUM(g.total)  AS 'total'
                FROM gastos g
                INNER JOIN gastos_tipos gt ON g.tipo_gasto = gt.id
                WHERE g.fecha BETWEEN ? AND ?
                GROUP BY gt.id, gt.nombre
                ORDER BY gt.nombre";
        $resultado = DB::select($sql, [$desde, $hasta]);
        
        $total = 0;

        foreach ($resultado as $row) {
            $row->gastos = $this->_getGastoDetalle($row->id, $desde, $hasta);
            $total += (float) $row->total;
        }

        return [
            'fechaDesde' => $desde,
            'fechaHasta' => $hasta,
            'detalle' => $resultado,
            'total' => $total
        ];
    }
    
    public function _getGastoDetalle($tipo, $desde, $hasta) {
        $sql = "SELECT g.fecha, g.nro_comprobante, g.tipo_comprobante, gt.nombre AS 'tipo_gasto', g.descripcion, g.total
                FROM gastos g
                INNER JOIN gastos_tipos gt ON g.tipo_gasto = gt.id
                WHERE (g.fecha BETWEEN ? AND ?) AND gt.id = ?
                ORDER BY g.fecha, gt.nombre, g.descripcion";
        $resultado = DB::select($sql, [$desde, $hasta, $tipo]);
        return $resultado;
    }

    public function rentabilidad($fechaDesde, $fechaHasta) {
        $data = $this->_getRentabilidad($fechaDesde, $fechaHasta);

        return response()->json($data);
    }

    public function _getRentabilidad($fechaDesde, $fechaHasta) {
        $sql = "SELECT *, IF (total_ganancia > 0, (total_barbero * 100) / total_ganancia, 0) AS prc_barbero, total_ganancia - total_barbero AS 'total_casa', IF (total_ganancia > 0, ((total_ganancia - total_barbero) * 100) / total_ganancia, 0) AS 'prc_casa'
                FROM (
                    SELECT tipo, descripcion, SUM(cantidad) AS 'cantidad', unidad, costo, precio, descuento,  SUM(total_venta) AS 'total_venta', (prc_ganancia * 100) AS 'prc_ganancia', ganancia, SUM(total_ganancia) AS 'total_ganancia', comision_barbero, SUM(total_barbero) AS 'total_barbero'
                    FROM (
                        SELECT c.fecha, 'SERVICIOS' AS tipo, s.nombre AS 'descripcion', cd.cantidad, NULL AS 'unidad', NULL AS 'costo', cd.precio, cd.descuento, cd.cantidad * (cd.precio - (cd.precio * (cd.descuento / 100))) AS 'total_venta', NULL AS 'prc_ganancia', NULL AS 'ganancia', cd.cantidad * (cd.precio - (cd.precio * (cd.descuento / 100))) AS 'total_ganancia', (s.pago_comision / 100) AS 'comision_barbero', (cd.cantidad * (cd.precio - (cd.precio * (cd.descuento / 100)))) * (s.pago_comision / 100) AS 'total_barbero'
                        FROM citas c
                        INNER JOIN citas_detalles cd ON cd.cita = c.id
                        INNER JOIN servicios s ON cd.servicio = s.id
                        WHERE c.estado = 'ATENDIDO'
                        UNION ALL
                        SELECT v.fecha, 'VENTAS' AS tipo, pro.nombre AS 'descripcion', vd.cantidad, um.unidad, pp.costo,  vd.precio, vd.descuento, (vd.cantidad * (vd.precio - (vd.precio * (vd.descuento / 100)))) AS 'total_venta', IF(pp.costo > 0, (vd.precio / pp.costo) - 1, 0) AS 'prc_ganancia', (vd.precio - pp.costo) AS 'ganancia', (vd.precio - pp.costo) * vd.cantidad AS 'total_ganancia', IF(e.cargo = 1, pp.comision_barbero / 100, 0) AS 'comision_barbero', (vd.cantidad * (vd.precio - (vd.precio * (vd.descuento / 100)))) * IF(e.cargo = 1, pp.comision_barbero / 100, 0) AS 'total_barbero'
                        FROM ventas v
                        INNER JOIN ventas_detalles vd ON vd.venta = v.id
                        INNER JOIN productos_presentaciones pp ON pp.id = vd.producto
                        INNER JOIN productos pro ON pp.producto_id = pro.id
                        INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                        INNER JOIN usuarios usu ON v.created_by = usu.id
                        LEFT JOIN empleados e ON usu.empleado = e.id
                    ) t
                    WHERE fecha BETWEEN ? AND ?
                    GROUP BY descripcion, unidad, costo, precio, descuento, comision_barbero, tipo, prc_ganancia, ganancia
                ) t
                ORDER BY 15 DESC";
        $result = DB::select($sql, [$fechaDesde, $fechaHasta]);

        $totalVenta = 0;
        $totalGanancia = 0;
        $totalBarbero = 0;
        $totalCasa = 0;

        foreach ($result as $row) {
            $totalVenta    += (float) $row->total_venta;
            $totalGanancia += (float) $row->total_ganancia;
            $totalBarbero  += (float) $row->total_barbero;
            $totalCasa     += (float) $row->total_casa;
        }

        return [
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'rows' => $result,
            'total' => [
                'venta'    => $totalVenta,
                'ganancia' => $totalGanancia,
                'barbero'  => $totalBarbero,
                'casa'     => $totalCasa
            ]
        ];
    }

    public function gananciaNeta($fechaDesde, $fechaHasta) {
        $rentabilidad = $this->_getRentabilidad($fechaDesde, $fechaHasta);
        $gastos = $this->_getGastos($fechaDesde, $fechaHasta);
        $adicionales = $this->_getPagosAdicionales($fechaDesde, $fechaHasta);
        $consumos = $this->_getConsumosInternos($fechaDesde, $fechaHasta);

        $totalRentabilidad = (float) $rentabilidad['total']['casa'];
        $totalAdicionales = (float) $adicionales['total'];
        $totalGastos = (float) $gastos['total'];
        $totalConsumos = (float) $consumos['total'];
        $totalNeto = $totalRentabilidad - $totalAdicionales - $totalGastos - $totalConsumos;

        return response()->json([
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'rentabilidad' => $totalRentabilidad,
            'gastos' => $totalGastos,
            'consumos' => $totalConsumos,
            'adicionales' => $adicionales['detalle'],
            'total' => $totalNeto
        ]);
    }

    public function _getPagosAdicionales($fechaDesde, $fechaHasta) {
        $sql = "SELECT pat.nombre, SUM(pa.monto) monto
                FROM pagos_adicionales pa
                INNER JOIN pagos_adicionales_tipos pat ON pa.tipo = pat.id
                WHERE pa.fecha BETWEEN ? AND ?
                GROUP BY pa.tipo, pat.nombre";
        $resultado = DB::select($sql, [$fechaDesde, $fechaHasta]);
        
        $total = 0;

        foreach ($resultado as $row) {
            $total += (float) $row->monto;
        }

        return [
            'fechaDesde' => $fechaDesde,
            'fechaHasta' => $fechaHasta,
            'detalle' => $resultado,
            'total' => $total
        ];
    }

}
