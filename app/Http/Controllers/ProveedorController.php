<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Proveedor::all();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $model = new Proveedor();
        $model->nombre = $request->input('nombre');
        $model->ruc = $request->input('ruc');
        $model->email = $request->input('email');
        $model->direccion = $request->input('direccion');
        $model->telefono = $request->input('telefono');
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
        return Proveedor::find($id);
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
        $model = Proveedor::find($id);
        $model->nombre = $request->input('nombre');
        $model->ruc = $request->input('ruc');
        $model->email = $request->input('email');
        $model->direccion = $request->input('direccion');
        $model->telefono = $request->input('telefono');
        $model->activo = $request->input('activo');
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
        $model = Proveedor::find($id);
        $model->delete();
        
        return $model;
    }
    
    public function search($search = '')
    {
        $result = [];
        if (!empty($search)) {
            $sql = "SELECT *
                    FROM proveedores
                    WHERE
                        nombre LIKE CONCAT('%', ?, '%') OR
                        ruc LIKE CONCAT('%', ?, '%')
                    ORDER BY nombre";

            $result = DB::select($sql, [$search, $search]);
        }

        return $result;
    }
}
