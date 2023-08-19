<?php

namespace App\Http\Controllers;

use App\Models\ClienteVIP;
use Illuminate\Http\Request;

class ClienteVIPController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ClienteVIP::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new ClienteVIP();
        $model->codigo = $request->input('codigo');
        $model->cliente = $request->input('cliente')['id'];
        $model->fecha_desde = $request->input('fecha_desde');
        $model->fecha_hasta = $request->input('fecha_hasta');
        $model->activo = $request->input('activo');
        $model->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return ClienteVIP::find($id);
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
        $model = ClienteVIP::find($id);
        $model->codigo = $request->input('codigo');
        $model->cliente = $request->input('cliente')['id'];
        $model->fecha_desde = $request->input('fecha_desde');
        $model->fecha_hasta = $request->input('fecha_hasta');
        $model->activo = $request->input('activo');
        $model->save();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = ClienteVIP::find($id);
        $model->delete();
        return $model;
    }
}
