<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\ProductoPresentacion;
use App\Models\Usuario;
use App\Models\Venta;
use App\Models\VentaDetalle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Venta::all();
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
        $venta = new Venta();
        $venta->fecha = $request->input('fecha');
        $venta->hora = date('H:i:s');
        $venta->tipo_comprobante = $request->input('tipo_comprobante');
        $venta->nro_comprobante = $request->input('nro_comprobante');
        $venta->promocion = $request->input('promocion');
        $venta->cliente = $request->input('cliente');
        $venta->total = $request->input('total');
        $venta->tipo_venta = $request->input('tipo_venta');
        $venta->created_by = $request->input('created_by');
        $venta->save();
        $venta->get_usuario = Usuario::find($venta->created_by);

        $venta->detalles = $request->input('detalles');

        foreach ($venta->detalles as $detalle) {
            $producto_id = $detalle['get_producto']['producto']['id'];
            $cantidad = $detalle['cantidad'];

            // Registrar detalle de la venta
            $venta_detalle = new VentaDetalle();
            $venta_detalle->venta = $venta->id;
            $venta_detalle->producto = $producto_id;
            $venta_detalle->cantidad = $cantidad;
            $venta_detalle->precio = $detalle['precio'];
            $venta_detalle->importe = $detalle['importe'];
            $venta_detalle->save();

            // Disminuir stock
            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock -= (int) $cantidad;
            $producto->save();


        }

        // Obtener datos del cliente
        if ($venta->cliente != null) {
            $cliente = Cliente::find($venta->cliente);
            $venta->get_cliente = $cliente ? $cliente : null;
        }

        $model = Venta::find($venta->id);

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
        return Venta::find($id);
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
        $venta = Venta::find($id);
        $venta->tipo_comprobante = $request->input('tipo_comprobante');
        $venta->nro_comprobante = $request->input('nro_comprobante');
        $venta->promocion = $request->input('promocion');
        $venta->cliente = $request->input('cliente');
        $venta->total = $request->input('total');
        $venta->tipo_venta = $request->input('tipo_venta');
        $venta->created_by = $request->input('created_by');
        $venta->update();

        foreach ($venta->detalles as $detalle) {
            $producto_id = $detalle->producto;
            $cantidad = $detalle->cantidad;

            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock += $cantidad;
            $producto->save();
        }

        DB::table('ventas_detalles')->where('venta', $venta->id)->delete();

        $venta->detalles = $request->input('detalles');

        foreach ($venta->detalles as $detalle) {
            $producto_id = $detalle['get_producto']['producto']['id'];
            $cantidad = $detalle['cantidad'];

            // Registrar detalle de la venta
            $detalle_invent = new VentaDetalle();
            $detalle_invent->venta = $venta->id;
            $detalle_invent->producto = $producto_id;
            $detalle_invent->cantidad = $cantidad;
            $detalle_invent->importe = $detalle['importe'];
            $detalle_invent->precio = $detalle['precio'];
            $detalle_invent->save();

            // Resetear stock
            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock -= (int) $cantidad;
            $producto->save();
        }
        return $venta;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $venta = Venta::find($id);

        foreach ($venta->detalles as $detalle) {
            $producto_id = $detalle->producto;
            $cantidad = $detalle->cantidad;

            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock += $cantidad;
            $producto->save();
        }

        $venta->delete();
        return $venta;
    }

    public function archivo($from, $to, $cajero = 0) {
        $results = [];
        if ($cajero > 0) {
            $results = Venta::whereBetween('fecha', [$from, $to])
                                ->where('created_by', $cajero)
                                ->orderBy('fecha', 'desc')
                                ->orderBy('hora', 'desc')
                                ->get();
        } else {
            $results = Venta::whereBetween('fecha', [$from, $to])
                                ->orderBy('fecha', 'desc')
                                ->orderBy('hora', 'desc')
                                ->get();
        }
        return $results;
    }
}
