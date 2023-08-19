<?php

namespace App\Http\Controllers;

use App\Models\Servicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Servicio::orderBy('nombre')->get();
    }

    public function indexActivos($activo)
    {
        return Servicio::orderBy('nombre')->where('activo', $activo)->get();
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
        $model = new Servicio();
        $model->codigo = $request->input('codigo');
        $model->nombre = $request->input('nombre');
        $model->precio = $request->input('precio');
        $model->tiempo_promedio = $request->input('tiempo_promedio');
        $model->pago_comision = $request->input('pago_comision');
        $model->activo = $request->input('activo');
        $model->save();
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
        return Servicio::find($id);
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
        $model = Servicio::find($id);
        $model->codigo = $request->input('codigo');
        $model->nombre = $request->input('nombre');
        $model->precio = $request->input('precio');
        $model->tiempo_promedio = $request->input('tiempo_promedio');
        $model->pago_comision = $request->input('pago_comision');
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
        $model = Servicio::find($id);
        $model->delete();
        return $model;
    }

    public function search($search = '')
    {
        $result = [];
        if (!empty($search)) {
            $sql = "SELECT *
                    FROM servicios
                    WHERE
                        nombre LIKE CONCAT('%', ?, '%')
                    ORDER BY nombre";

            $result = DB::select($sql, [$search]);
        }

        return $result;
    }
}
