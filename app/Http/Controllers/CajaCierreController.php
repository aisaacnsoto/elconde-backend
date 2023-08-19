<?php

namespace App\Http\Controllers;

use App\Models\CajaCierre;
use Illuminate\Http\Request;

class CajaCierreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return CajaCierre::all();
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
        $existe = CajaCierre::where('fecha', $fecha)->first();
        if ($existe) {
            $model = $existe;
        } else {
            $model = new CajaCierre();
        }
        $model->fecha = $fecha;
        $model->bil_200 = $request->input('bil_200');
        $model->bil_100 = $request->input('bil_100');
        $model->bil_50 = $request->input('bil_50');
        $model->bil_20 = $request->input('bil_20');
        $model->bil_10 = $request->input('bil_10');
        $model->mon_05 = $request->input('mon_05');
        $model->mon_02 = $request->input('mon_02');
        $model->mon_01 = $request->input('mon_01');
        $model->mon_50 = $request->input('mon_50');
        $model->mon_20 = $request->input('mon_20');
        $model->mon_10 = $request->input('mon_10');
        $model->total = $request->input('total');
        $model->created_by = $request->input('created_by');
        $model->save();
        return $model;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($fecha)
    {
        $model = CajaCierre::where('fecha', $fecha)->first();
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
        $model = CajaCierre::find($id);
        $model->fecha = $request->input('fecha');
        $model->bil_200 = $request->input('bil_200');
        $model->bil_100 = $request->input('bil_100');
        $model->bil_50 = $request->input('bil_50');
        $model->bil_20 = $request->input('bil_20');
        $model->bil_10 = $request->input('bil_10');
        $model->mon_05 = $request->input('mon_05');
        $model->mon_02 = $request->input('mon_02');
        $model->mon_01 = $request->input('mon_01');
        $model->mon_50 = $request->input('mon_50');
        $model->mon_20 = $request->input('mon_20');
        $model->mon_10 = $request->input('mon_10');
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
        $model = CajaCierre::find($id);
        $model->delete();
        return $model;
    }
}
