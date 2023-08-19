<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\CompraDetalle;
use App\Models\ProductoPresentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        return Compra::orderBy('fecha_inter', 'desc')->orderBy('hora_inter', 'desc')->get();
    }

    public function store(Request $request)
    {
        $compra = new Compra();
        $compra->fecha_emi = $request->input('fecha_emi');
        $compra->fecha_inter = $request->input('fecha_inter');
        $compra->hora_inter = $request->input('hora_inter');
        $compra->proveedor = $request->input('proveedor');
        $compra->tipo_doc = $request->input('tipo_doc');
        $compra->nro_doc_pref = $request->input('nro_doc_pref');
        $compra->nro_doc_suf = $request->input('nro_doc_suf');
        $compra->total = $request->input('total');
        $compra->save();

        $compra->detalle = $request->input('detalle');

        foreach ($compra->detalle as $detalle) {
            $producto_id = $detalle['get_producto']['producto']['id'];
            $cantidad = $detalle['cantidad'];

            // Registrar detalle de la compra
            $detalle_compra = new CompraDetalle();
            $detalle_compra->compra = $compra->id;
            $detalle_compra->producto = $producto_id;
            $detalle_compra->cantidad = $cantidad;
            $detalle_compra->precio = $detalle['precio'];
            $detalle_compra->importe = $detalle['importe'];
            $detalle_compra->save();

            // Aumentar stock
            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock += (int) $cantidad;
            $producto->save();

        }
        
        return $compra;
    }

    public function show($id)
    {
        return Compra::find($id);
    }

    public function update(Request $request, $id)
    {
        $compra = Compra::find($id);
        $compra->fecha_emi = $request->input('fecha_emi');
        $compra->fecha_inter = $request->input('fecha_inter');
        $compra->hora_inter = $request->input('hora_inter');
        $compra->proveedor = $request->input('proveedor');
        $compra->tipo_doc = $request->input('tipo_doc');
        $compra->nro_doc_pref = $request->input('nro_doc_pref');
        $compra->nro_doc_suf = $request->input('nro_doc_suf');
        $compra->total = $request->input('total');
        $compra->save();

        $changes = $request->input('changes');
        $to_delete = $changes['to_delete'];
        $to_insert = $changes['to_insert'];

        foreach ($to_delete as $row) {
            // Eliminar detalle de la compra
            $orden = CompraDetalle::find($row['id']);
            $orden->delete();

            // Reducir el stock
            $producto = ProductoPresentacion::find($row['producto']);
            $producto->stock -= $row['cantidad'];
            $producto->save();
        }

        foreach ($to_insert as $row) {
            // Registrar detalle de la compra
            $orden = new CompraDetalle();
            $orden->compra = $compra->id;
            $orden->producto = $row['producto'];
            $orden->cantidad = $row['cantidad'];
            $orden->importe = $row['importe'];
            $orden->precio = $row['precio'];
            $orden->save();

            // Aumentar el stock
            $producto = ProductoPresentacion::find($row['producto']);
            $producto->stock += $orden->cantidad;
            $producto->save();
        }

        return $compra;
    }

    public function destroy($id)
    {
        $compra = Compra::find($id);
        
        foreach ($compra->detalle as $detalle) {
            $producto_id = $detalle->producto;
            $cantidad = $detalle->cantidad;

            $producto = ProductoPresentacion::find($producto_id);
            $producto->stock -= $cantidad;
            $producto->save();
        }

        $compra->delete();
        return $compra;
    }
}
