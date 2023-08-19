<?php

namespace App\Http\Controllers;

use App\Models\Empleado;
use Illuminate\Http\Request;

class EmpleadoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Empleado::orderBy('nombres')->get();
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
        $model = new Empleado();
        $model->codigo = $request->input('codigo');
        $model->ape_paterno = $request->input('ape_paterno');
        $model->ape_materno = $request->input('ape_materno');
        $model->nombres = $request->input('nombres');
        $model->num_doc = $request->input('num_doc');
        $model->tipo_doc = $request->input('tipo_doc');
        $model->telefono = $request->input('telefono');
        $model->sexo = $request->input('sexo');
        $model->fecha_nac = $request->input('fecha_nac');
        $model->direccion = $request->input('direccion');
        $model->correo = $request->input('correo');
        $model->fecha_ingreso_laboral = $request->input('fecha_ingreso_laboral');
        $model->cargo = $request->input('cargo');
        $model->foto = $request->input('foto');
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
        return Empleado::find($id);
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
        $model = Empleado::find($id);
        $model->codigo = $request->input('codigo');
        $model->ape_paterno = $request->input('ape_paterno');
        $model->ape_materno = $request->input('ape_materno');
        $model->nombres = $request->input('nombres');
        $model->num_doc = $request->input('num_doc');
        $model->tipo_doc = $request->input('tipo_doc');
        $model->telefono = $request->input('telefono');
        $model->sexo = $request->input('sexo');
        $model->fecha_nac = $request->input('fecha_nac');
        $model->direccion = $request->input('direccion');
        $model->correo = $request->input('correo');
        $model->fecha_ingreso_laboral = $request->input('fecha_ingreso_laboral');
        $model->cargo = $request->input('cargo');
        $model->foto = $request->input('foto');
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
        $model = Empleado::find($id);
        $model->delete();
        return $model;
    }

    public function getByCargo($cargo) {
        return Empleado::where('cargo', $cargo)->orderBy('nombres')->get();
    }
}
