<?php

namespace App\Http\Controllers;

use App\Models\Inventario;
use App\Models\InventarioDetalle;
use App\Models\ProductoPresentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Inventario::orderBy('fecha', 'desc')->orderBy('id', 'desc')->get();
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
        $invent = new Inventario();
        $invent->fecha = $request->input('fecha');
        $invent->hora = $request->input('hora');
        $invent->comentario = $request->input('comentario');
        $invent->save();
        
        $detalle = $request->input('detalle');
        
        foreach ($invent->detalle as $detalle) {
            $producto_id = $detalle['producto'];

            $presentacion = ProductoPresentacion::find($producto_id);
            $sql = "UPDATE productos_presentaciones pre INNER JOIN productos p ON pre.producto_id = p.id SET pre.stock = 0 WHERE pre.producto_id = ?";
            DB::select($sql, [$presentacion->producto_id]);
        }

        $productos = ProductoPresentacion::all();

        foreach ($detalle as $item) {
            $producto_pres_id = $item['producto'];
            $stock = $item['stock'];

            $producto_pres = $productos->first(function($value, $key) use($producto_pres_id) {
                return $value->id == $producto_pres_id;
            });
            $stock_prev = $producto_pres->stock;

            // Registrar detalle del inventario
            $item = new InventarioDetalle();
            $item->inventario = $invent->id;
            $item->producto = $producto_pres_id;
            $item->stock = $stock;
            $item->stock_prev = $stock_prev;
            $item->stock_difer = $stock_prev - $stock;
            $item->save();

            // Resetear stock
            $producto = ProductoPresentacion::find($producto_pres_id);
            $producto->stock = (int) $stock;
            $producto->save();
        }
        return $invent;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        return Inventario::find($id);
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
        $invent = Inventario::find($id);
        $invent->fecha = $request->input('fecha');
        $invent->hora = $request->input('hora');
        $invent->comentario = $request->input('comentario');
        $invent->save();

        $changes = $request->input('changes');
        $to_insert = $changes['to_insert'];
        $to_update = $changes['to_update'];
        $to_delete = $changes['to_delete'];

        foreach ($to_delete as $row) {
            if (isset($row['id'])) {
                // Eliminar detalle de la compra
                $detalle = InventarioDetalle::find($row['id']);
                $detalle->delete();
    
                // Restaurar stock anterior
                $producto = ProductoPresentacion::find($row['producto']);
                $producto->stock += $detalle->stock_difer;
                $producto->save();
            }
        }

        foreach ($to_update as $row) {
            if ($row['id']) {
                $stock = $row['stock'];
                // Eliminar detalle de la compra
                $detalle = InventarioDetalle::find($row['id']);
                $stockPrev = $detalle->stock;
                $detalle->stock = $stock;
                $detalle->stock_difer = $detalle->stock_prev - $stock;
                $detalle->save();
    
                // Actualizar stock anterior
                $producto = ProductoPresentacion::find($row['producto']);
                $producto->stock += $stock - $stockPrev;
                $producto->save();
            }
        }

        $productos = (count($to_insert) > 0) ? ProductoPresentacion::all() : null;

        foreach ($to_insert as $row) {
            $producto_pres_id = $row['producto'];
            $stock = $row['stock'];
            $producto_pres = $productos->first(function($value, $key) use($producto_pres_id) {
                return $value->id == $producto_pres_id;
            });
            $stock_prev = $producto_pres->stock;

            // Registrar detalle del inventario
            $detalle = new InventarioDetalle();
            $detalle->inventario = $invent->id;
            $detalle->producto = $producto_pres_id;
            $detalle->stock = $stock;
            $detalle->stock_prev = $stock_prev;
            $detalle->stock_difer = $stock_prev - $stock;
            $detalle->save();

            // Resetear stock anterior
            $producto = ProductoPresentacion::find($producto_pres_id);
            $producto->stock = $stock;
            $producto->save();
        }

        return $invent;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $invent = Inventario::find($id);
        $detalle_inventario = InventarioDetalle::where('inventario', $id)->get();

        foreach ($detalle_inventario as $row) {

            // Eliminar detalle de la compra
            $detalle = InventarioDetalle::find($row->id);
            $detalle->delete();
    
            // Restaurar stock anterior
            $producto = ProductoPresentacion::find($detalle->producto);
            $producto->stock += $detalle->stock_difer;
            $producto->save();
        }

        $invent->delete();
        return $invent;
    }
}
