<?php

namespace App\Http\Controllers;

use App\Models\ConsumoInterno;
use App\Models\ProductoPresentacion;
use Illuminate\Http\Request;

class ConsumoInternoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return ConsumoInterno::orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $consumo = new ConsumoInterno();
        $consumo->empleado_id = $request->input('empleado_id');
        $consumo->cantidad = $request->input('cantidad');
        $consumo->producto_id = $request->input('producto_id');
        $consumo->fecha = $request->input('fecha');
        $consumo->save();

        // Disminuir stock
        $producto = ProductoPresentacion::find($consumo->producto_id);
        $producto->stock -= (int) $consumo->cantidad;
        $producto->save();

        return $consumo;
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
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $consumo = ConsumoInterno::find($id);

        // Aumentar stock anterior
        $producto = ProductoPresentacion::find($consumo->producto_id);
        $producto->stock += (int) $consumo->cantidad;
        $producto->save();

        // Actualizar consumo
        $consumo->empleado_id = $request->input('empleado_id');
        $consumo->cantidad = $request->input('cantidad');
        $consumo->producto_id = $request->input('producto_id');
        $consumo->fecha = $request->input('fecha');
        $consumo->save();

        // Disminuir stock actual
        $producto = ProductoPresentacion::find($consumo->producto_id);
        $producto->stock -= (int) $consumo->cantidad;
        $producto->save();
        
        return $consumo;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $consumo = ConsumoInterno::find($id);

        // Aumentar stock del anterior
        $producto = ProductoPresentacion::find($consumo->producto_id);
        $producto->stock += (int) $consumo->cantidad;
        $producto->save();
        
        $consumo->delete();
        
        return $consumo;
    }
}
