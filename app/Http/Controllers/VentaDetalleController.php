<?php

namespace App\Http\Controllers;

use App\Models\VentaDetalle;
use Illuminate\Http\Request;

class VentaDetalleController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return VentaDetalle::all();
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
        $model = new VentaDetalle();
        $model->venta = $request->input('venta');
        $model->producto = $request->input('producto');
        $model->cantidad = $request->input('cantidad');
        $model->precio = $request->input('precio');
        $model->importe = $request->input('importe');
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
        //
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
        $model = VentaDetalle::find($id);
        $model->venta = $request->input('venta');
        $model->producto = $request->input('producto');
        $model->cantidad = $request->input('cantidad');
        $model->precio = $request->input('precio');
        $model->importe = $request->input('importe');
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
        $model = VentaDetalle::find($id);
        $model->delete();
        return $model;
    }
}
