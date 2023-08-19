<?php

namespace App\Http\Controllers;

use App\Models\Asignacion;
use App\Models\ProductoPresentacion;
use Illuminate\Http\Request;

class AsignacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Asignacion::orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
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
        $model = new Asignacion();
        $model->empleado = $request->input('empleado');
        $model->cantidad = $request->input('cantidad');
        $model->producto_id = $request->input('producto_id');
        $model->fecha = $request->input('fecha');
        $model->save();

        // Disminuir stock
        $producto = ProductoPresentacion::find($model->producto_id);
        $producto->stock -= (int) $model->cantidad;
        $producto->save();

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
        $model = Asignacion::find($id);

        // Aumentar stock anterior
        $producto = ProductoPresentacion::find($model->producto_id);
        $producto->stock += (int) $model->cantidad;
        $producto->save();

        $model->empleado = $request->input('empleado');
        $model->cantidad = $request->input('cantidad');
        $model->producto_id = $request->input('producto_id');
        $model->fecha = $request->input('fecha');
        $model->update();

        // Disminuir stock actual
        $producto = ProductoPresentacion::find($model->producto_id);
        $producto->stock -= (int) $model->cantidad;
        $producto->save();

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
        $model = Asignacion::find($id);
        $model->delete();

        // Aumentar stock del anterior
        $producto = ProductoPresentacion::find($model->producto_id);
        $producto->stock += (int) $model->cantidad;
        $producto->save();
        
        return $model;
    }
}
