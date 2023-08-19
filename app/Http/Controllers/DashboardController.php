<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use App\Models\CajaCierre;
use App\Models\Cita;
use App\Models\Empleado;
use App\Models\Gasto;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index($fecha) {
        $today = date('Y-m-d', strtotime($fecha));
        $anio = date('Y', strtotime($fecha));
        $mes = date('m', strtotime($fecha));

        $ventas_del_dia = Venta::where('fecha', $today)->count();
        $ventas_del_dia += Cita::where('fecha', $today)->where('estado', 'ATENDIDO')->count();
        $apertura_caja = CajaApertura::where('fecha', $today)->first();
        $cierre_caja = CajaCierre::where('fecha', $today)->first();
        $gastos_del_dia = Gasto::where('fecha', $today)->get()->sum('total');

        $this_week_date_start = date('Y-m-d', strtotime('this week', strtotime($fecha)));
        // $this_week_date_start = '2020-07-20';
        $this_week = [
            'start' => $this_week_date_start,
            'end' => date('Y-m-d', strtotime('+6 days', strtotime($this_week_date_start)))
        ];
        $servicios_semana = $this->_getServiciosSemana( $this_week['start'], $this_week['end'] );
        // $this_week_sales = $this->getWeekSales( $this_week['start'], $this_week['end'] );

        $last_week_date_start = date('Y-m-d', strtotime('last week'));
        // $last_week_date_start = '2020-07-13';
        $last_week = [
            'start' => $last_week_date_start,
            'end' => date('Y-m-d', strtotime('+6 days', strtotime($last_week_date_start)))
        ];
        // $last_week_sales = $this->getWeekSales( $last_week['start'], $last_week['end'] );
        $month_stats = $this->_getMonthStats( (int) $mes, (int) $anio );

        $clientes_frecuentes = $this->getClientesFrecuentes();
        $productos_mas_vendidos = $this->getProductosMasVendidos();

        $servicios = $this->getServicios();
        // $productividad = $this->_getProductividad($anio);

        return response()->json([
            'ventas_del_dia' => $ventas_del_dia,
            'apertura_caja'  => $apertura_caja ? $apertura_caja->total : 0,
            'cierre_caja'    => $cierre_caja ? $cierre_caja->total : 0,
            'gastos_del_dia' => $gastos_del_dia,
            // 'servicios' => $servicios,
            'clientes_frecuentes'    => $clientes_frecuentes,
            'productos_mas_vendidos' => $productos_mas_vendidos,
            // 'productividad' => $productividad,
            'estadisticas_mes' => $month_stats,
            'servicios_semana' => $servicios_semana
        ]);
        // return $raw;
    }

    public function _getMonthStats($month, $anio) {
        $sql = "SELECT e.nombres AS 'item', COUNT(c.id) AS 'cantidad' FROM citas c INNER JOIN empleados e ON c.empleado = e.id WHERE MONTH(c.fecha) = ? AND YEAR(c.fecha) = ? GROUP BY e.nombres, c.empleado ";
        $sql .= "UNION ALL ";
        $sql .= "SELECT 'TIENDA' AS 'item', COUNT(v.id) FROM ventas v WHERE MONTH(v.fecha) = ? AND YEAR(v.fecha) = ?";

        $resultado = DB::select($sql, [$month, $anio, $month, $anio]);

        return $resultado;
    }

    public function getServicios() {
        $anio = date('Y');
        
        $citas = DB::table('citas')
                 ->where('estado', 'ATENDIDO')
                 ->whereRaw('YEAR(fecha) = ?', [ $anio ])
                 ->groupByRaw('MONTH(fecha)')
                 ->orderBy('fecha')
                 ->select(DB::raw('count(`id`) as `citas`, MONTH(`fecha`) as `mes`'))
                 ->get();

        $data = [];
        $current_month = 0;

        for ($i = 1; $i <= 12; $i++) {
            $mes = $this->getMonthName($i);
            $countCitas = 0;
            
            if ($current_month < $citas->count() && $citas[$current_month]->mes == $i) {
                $countCitas = $citas[$current_month]->citas;
                $current_month++;
            }

            $data[] = [
                'mes' => $mes,
                'citas' => $countCitas
            ];
        }
        
        return $data;
    }

    public function getWeekDayName($number) {
        $result = '';

        switch ($number) {
            case 0: $result = 'Lunes'; break;
            case 1: $result = 'Martes'; break;
            case 2: $result = 'Miércoles'; break;
            case 3: $result = 'Jueves'; break;
            case 4: $result = 'Viernes'; break;
            case 5: $result = 'Sábado'; break;
            case 6: $result = 'Domingo'; break;
        }

        return $result;
    }

    public function getWeekDayNameAbr($number) {
        $result = '';

        switch ($number) {
            case 0: $result = 'LUN'; break;
            case 1: $result = 'MAR'; break;
            case 2: $result = 'MIÉ'; break;
            case 3: $result = 'JUE'; break;
            case 4: $result = 'VIE'; break;
            case 5: $result = 'SÁB'; break;
            case 6: $result = 'DOM'; break;
        }

        return $result;
    }

    public function getMonthName($number) {
        $result = '';

        switch ($number) {
            case 1: $result  = 'ENE'; break;
            case 2: $result  = 'FEB'; break;
            case 3: $result  = 'MAR'; break;
            case 4: $result  = 'ABR'; break;
            case 5: $result  = 'MAY'; break;
            case 6: $result  = 'JUN'; break;
            case 7: $result  = 'JUL'; break;
            case 8: $result  = 'AGO'; break;
            case 9: $result  = 'SEP'; break;
            case 10: $result = 'OCT'; break;
            case 11: $result = 'NOV'; break;
            case 12: $result = 'DIC'; break;
        }

        return $result;
    }

    public function getClientesFrecuentes() {
        $resultado = DB::table('citas')
                       ->join('clientes', 'citas.cliente', '=', 'clientes.id')
                       ->where('citas.estado', 'ATENDIDO')
                       ->whereNotIn('citas.cliente', [1])
                       ->groupBy('citas.cliente', 'clientes.nombres')
                       ->selectRaw("count(citas.id) as 'citas', clientes.nombres")
                       ->orderByRaw('count(citas.id) desc')
                       ->orderByDesc('citas.updated_at')
                       ->limit(10)
                       ->get();
        
        return $resultado;
    }

    public function getProductosMasVendidos() {
        $resultado = DB::table('ventas_detalles')
                       ->join('productos', 'productos.id', '=', 'ventas_detalles.producto')
                       ->groupBy('ventas_detalles.producto', 'productos.nombre')
                       ->selectRaw("count(ventas_detalles.id) as 'ventas', productos.nombre")
                       ->orderByRaw('count(ventas_detalles.id) desc')
                       ->orderByDesc('ventas_detalles.created_at')
                       ->limit(10)
                       ->get();

        return $resultado;
    }

    public function _getProductividad($anio) {
        $sql = "SELECT
                    CONCAT(emp.nombres,' ',emp.ape_paterno) AS 'barbero',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 1), 0) AS 'ene',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 2), 0) AS 'feb',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 3), 0) AS 'mar',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 4), 0) AS 'abr',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 5), 0) AS 'may',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 6), 0) AS 'jun',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 7), 0) AS 'jul',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 8), 0) AS 'ago',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 9), 0) AS 'sep',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 10), 0) AS 'oct',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 11), 0) AS 'nov',
                    IFNULL((SELECT SUM(detalle.cantidad) FROM citas ci INNER JOIN citas_detalles detalle ON ci.id = detalle.cita WHERE ci.empleado = emp.id AND YEAR(ci.fecha) = $anio AND MONTH(ci.fecha) = 12), 0) AS 'dic'
                FROM empleados emp WHERE cargo = 1 ORDER BY emp.nombres LIMIT 5";
        $data = DB::select($sql);
        $result = [];
        
        for ($i = 0; $i < count($data); $i++) {
            $tmp = $data[$i];
            $barbero = $data[$i]->barbero;
            $cortes = [];
            
            $cortes[] = (int) $tmp->ene;
            $cortes[] = (int) $tmp->feb;
            $cortes[] = (int) $tmp->mar;
            $cortes[] = (int) $tmp->abr;
            $cortes[] = (int) $tmp->may;
            $cortes[] = (int) $tmp->jun;
            $cortes[] = (int) $tmp->jul;
            $cortes[] = (int) $tmp->ago;
            $cortes[] = (int) $tmp->sep;
            $cortes[] = (int) $tmp->oct;
            $cortes[] = (int) $tmp->nov;
            $cortes[] = (int) $tmp->dic;

            $result[] = [
                'barbero' => $barbero,
                'detalle' => $cortes
            ];
        }

        return $result;
    }

    public function _getServiciosSemana($week_start, $week_end) {
        $empleados = Empleado::where('cargo', 1)->get();
        $resultado = [];
        foreach ($empleados as $empleado) {
            $nombre = $empleado->nombres;
            $servicios = [];
            for ($i = 0; $i < 7; $i++) {
                $sql = "SELECT COUNT(c.id) AS 'cantidad', ? AS 'dia' FROM citas c INNER JOIN empleados e ON c.empleado = e.id WHERE (c.fecha BETWEEN ? AND ?) AND WEEKDAY(c.fecha) = ? AND c.empleado = ? AND c.estado='ATENDIDO'";
                $data = DB::selectOne($sql, [$i, $week_start, $week_end, $i, $empleado->id]);
                $servicios[] = $data->cantidad;
            }

            $resultado[] = [
                'barbero' => $nombre,
                'servicios' => $servicios
            ];
        }
        return $resultado;
    }
}
