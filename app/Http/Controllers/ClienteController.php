<?php

namespace App\Http\Controllers;

use App\Models\Cita;
use App\Models\Cliente;
use App\Models\ClienteRecomendado;
use App\Models\Promocion;
use App\Models\PromocionConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Cliente::orderBy('nombres')->orderBy('apellidos')->get();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cliente_exists = Cliente::where('nombres', $request->input('nombres'))->where('apellidos', $request->input('apellidos'))->where('num_doc', $request->input('num_doc'))->first();
        if ($cliente_exists) return response()->json([ 'mensaje' => '¡El cliente ya está registrado!' ]);


        $model = new Cliente();
        $model->nombres = $request->input('nombres');
        $model->apellidos = $request->input('apellidos');
        $model->num_doc = $request->input('num_doc');
        $model->tipo_doc = $request->input('tipo_doc');
        $model->telefono = $request->input('telefono');
        $model->fecha_nac = $request->input('fecha_nac');
        $model->direccion = $request->input('direccion');
        $model->correo = $request->input('correo');
        $model->descripcion = $request->input('descripcion');
        $model->activo = $request->input('activo');
        $model->save();

        $recomendador_id = $request->input('recomendador');
        if ($recomendador_id != null) {
            $cli_reco = new ClienteRecomendado();
            $cli_reco->recomendado = $model->id;
            $cli_reco->recomendador = $recomendador_id;
            $cli_reco->save();
        }

        $promo_cumple = Promocion::find(3);
        $cumple_min = PromocionConfig::find('CUMPLE_DIAS_PRE');
        $cumple_min->valor;

        $promocion = null;

        $dias_difer = $this->_daysDiff(date('Y-m-d'), $model->fecha_nac);

        if ($dias_difer >= 0 && $dias_difer <= $cumple_min->valor) {
            $promocion = $promo_cumple;
        }
        $model->promocion = $promocion;

        return $model;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Cliente::where('num_doc', $id)->orWhere('id', $id)->first();
        // return Cliente::find($id);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $model = Cliente::find($id);
        $model->nombres = $request->input('nombres');
        $model->apellidos = $request->input('apellidos');
        $model->num_doc = $request->input('num_doc');
        $model->tipo_doc = $request->input('tipo_doc');
        $model->telefono = $request->input('telefono');
        $model->fecha_nac = $request->input('fecha_nac');
        $model->direccion = $request->input('direccion');
        $model->correo = $request->input('correo');
        $model->descripcion = $request->input('descripcion');
        $model->activo = $request->input('activo');
        $model->update();
        return $model;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Cliente::find($id);
        $model->delete();
        return $model;
    }

    public function search($search = '')
    {
        $search = trim($search);
        $search_array = explode(' ', $search);
        $search_array = array_filter($search_array);

        $result = [];
        
        if (!empty($search)) {
            $builder = DB::table('clientes')->leftJoin('clientes_vip', 'clientes.id', '=', 'clientes_vip.cliente')->groupBy('clientes.id');

            $builder->orWhere(function($query) use($search) {
                $query->whereRaw("nombres = ? OR apellidos = ? OR num_doc LIKE CONCAT('%', ?, '%') OR clientes_vip.codigo = ?", [$search, $search, $search, $search]);
            });

            $builder->orWhere(function($builder) use($search_array) {

                if (count($search_array) == 1) {
    
                    $builder->where(function($query) use($search_array) {
                        foreach ($search_array as $tmp) {
                            $query->orWhereRaw("nombres LIKE CONCAT('%', ?, '%') OR apellidos LIKE CONCAT('%', ?, '%') OR num_doc LIKE CONCAT('%', ?, '%')", [$tmp, $tmp, $tmp]);
                        }
                    });
    
                } else {
    
                    $builder->where(function($query) use($search_array) {
                        foreach ($search_array as $tmp) {
                            $query->orWhereRaw("nombres LIKE CONCAT('%', ?, '%')", [$tmp]);
                        }
                    });
        
                    $builder->where(function($query) use($search_array) {
                        foreach ($search_array as $tmp) {
                            $query->orWhereRaw("apellidos LIKE CONCAT('%', ?, '%')", [$tmp]);
                        }
                    });

                }
            });


            $builder->orderBy('clientes.nombres');
            $builder->groupBy('clientes.nombres');
            $builder->groupBy('clientes.apellidos');
            $builder->groupBy('clientes.num_doc');
            $builder->groupBy('clientes.tipo_doc');
            $builder->groupBy('clientes.telefono');
            $builder->groupBy('clientes.fecha_nac');
            $builder->groupBy('clientes.direccion');
            $builder->groupBy('clientes.correo');
            $builder->groupBy('clientes.descripcion');
            $builder->groupBy('clientes.activo');
            $builder->groupBy('clientes.created_at');
            $builder->groupBy('clientes.updated_at');
            $builder->groupBy('clientes_vip.fecha_desde');
            $builder->groupBy('clientes_vip.fecha_hasta');
            $builder->groupBy('clientes_vip.activo');
            $builder->selectRaw("clientes.*,
                    (SELECT COUNT(cr.id) FROM clientes_recomendados cr WHERE cr.recomendador = clientes.id AND cr.estado = 'VIGENTE') AS 'recomendados',
                    (SELECT COUNT(c.id) FROM citas c WHERE c.cliente = clientes.id AND c.estado = 'ATENDIDO' AND MONTH(c.fecha) = MONTH(NOW()) AND YEAR(c.fecha) = YEAR(NOW())) AS 'visitas',
                    IF((CURRENT_DATE() BETWEEN clientes_vip.fecha_desde AND clientes_vip.fecha_hasta) AND clientes_vip.activo = 1, 'si', 'no') AS 'codigo_vip_vigente'");

            $result = $builder->get();

        }

        $promo_recom = Promocion::find(1);
        $recom_min = PromocionConfig::find('RECOM_MIN');
        $recom_min->valor;
        
        $promo_vip = Promocion::find(2);
        $vip_min = PromocionConfig::find('VIP_MIN');
        $vip_min->valor;
        
        $promo_cumple = Promocion::find(3);
        $cumple_min = PromocionConfig::find('CUMPLE_DIAS_PRE');
        $cumple_min->valor;

        $promo_trab = Promocion::find(4);

        

        // Condicionar promociones
        foreach ( $result as $tmp ) {

            $cliente_id = $tmp->id;

            $promocion = null;
            $esVIP = false;
            $esCumple = false;
            $esTrabajador = false;
            $cumpleMessage = '';

            if ($cliente_id > 1) {

                if ($tmp->visitas >= $vip_min->valor) {
                    $promocion = $promo_vip;
                    $esVIP = true;
                }

                if ($tmp->codigo_vip_vigente == 'si') {
                    $promocion = $promo_vip;
                    $esVIP = true;
                }
    
                if ($tmp->recomendados >= $recom_min->valor) {
                    $promocion = $promo_recom;
                }

                // Comprobar si el cliente está en la lista de trabajadores
                $trabajadores = $promo_trab->clientes;
                $contador = 0;

                foreach ($trabajadores as $cli) {
                    if ($cli->cliente == $cliente_id) {
                        $contador++;
                    }
                }

                // Comprobar si se encontró un cliente para asignarle el descuento
                if ($contador > 0) {
                    $promocion = $promo_trab;
                    $esTrabajador = true;
                }
    
                // Calcular los días restantes para el cumpleaños
                $dias_difer = $this->_daysDiff(date('Y-m-d'), $tmp->fecha_nac);
                
                if ($dias_difer >= 0 && $dias_difer <= $cumple_min->valor) {
    
                    $descuento_aplicado = Cita::where('estado', 'ATENDIDO')->where('cliente', $cliente_id)->where('promocion', 3)->first();
    
                    if (!$descuento_aplicado) {
                        $promocion = $promo_cumple;
                    }
                    $esCumple = true;
    
                    if ($dias_difer == 0) {
                        $cumpleMessage = 'Cumpleaños Hoy';
                    } elseif ($dias_difer == 1) {
                        $cumpleMessage = 'Cumpleaños Mañana';
                    } else {
                        $cumpleMessage = 'Cumpleaños en '.$dias_difer.' Días';
                    }
                }
            }


            $tmp->vip = $esVIP;
            $tmp->cumpleanios = $esCumple;
            $tmp->trabajador = $esTrabajador;
            $tmp->cumple_message = $cumpleMessage;
            $tmp->promocion = $promocion;

            $clientes = DB::select("
                SELECT
                    CONCAT(c.nombres, ' ', c.apellidos) AS 'recomendado'
                FROM clientes_recomendados cr
                INNER JOIN clientes c ON cr.recomendado = c.id WHERE cr.recomendador = ? AND cr.estado = 'VIGENTE'", [$cliente_id]);
            $tmp->clientes_recomendados = $clientes;
        }

        return $result;
        // return $builder->toSql();
    }

    public function _daysDiff($fechaHoy, $fechaNac) {

        $anioActual = (int) date('Y', strtotime($fechaHoy));
        $anioCumple = (int) date('Y', strtotime($fechaNac));
        $anioDifer = $anioActual - $anioCumple;

        $fechaHoyDateTime = date_create($fechaHoy);
        $fechaNacDateTime = date_create(date('Y-m-d', strtotime('+'.$anioDifer.' year', strtotime($fechaNac))));
        $interval = date_diff($fechaHoyDateTime, $fechaNacDateTime);

        $diasDifer = $interval->days;

        if ($interval->invert == 1) $diasDifer *= -1;

        return $diasDifer;
    }

}
