<?php

namespace App\Http\Controllers;

use App\Models\CajaApertura;
use Illuminate\Http\Request;

class CajaAperturaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CajaApertura::all();
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
        $fecha = $request->input('fecha');
        $model = null;
        $existe = CajaApertura::where('fecha', $fecha)->first();
        if ($existe) {
            $model = $existe;
        } else {
            $model = new CajaApertura();
        }
        $model->fecha = $fecha;
        $model->total = $request->input('total');
        $model->created_by = $request->input('created_by');
        $model->save();
        return $model;
    }

    /**
     * Display the specified resource.
     *
     * @param  string  $fecha
     * @return \Illuminate\Http\Response
     */
    public function show($fecha)
    {
        $model = CajaApertura::where('fecha', $fecha)->first();
        return $model;
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
        $model = CajaApertura::find($id);
        $model->fecha = $request->input('fecha');
        $model->total = $request->input('total');
        $model->created_by = $request->input('created_by');
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
        $model = CajaApertura::find($id);
        $model->delete();
        return $model;
    }
}
