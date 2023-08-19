<?php

namespace App\Http\Controllers;

use App\Models\PagoAdicional;
use Illuminate\Http\Request;

class PagoAdicionalController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return PagoAdicional::orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new PagoAdicional();
        $model->tipo = $request->input('tipo');
        $model->fecha = $request->input('fecha');
        $model->monto = $request->input('monto');
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
        return PagoAdicional::find($id);
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
        $model = PagoAdicional::find($id);
        $model->tipo = $request->input('tipo');
        $model->fecha = $request->input('fecha');
        $model->monto = $request->input('monto');
        $model->save();

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
        $model = PagoAdicional::find($id);
        $model->delete();

        return $model;
    }
}
