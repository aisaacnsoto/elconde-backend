<?php

namespace App\Http\Controllers;

use App\Models\ProductoCodigo;
use App\Models\ProductoPresentacion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoPresentacionController extends Controller
{
    public function getByProducto($id) {
        return ProductoPresentacion::where('producto_id', $id)->get();
    }

    public function save(Request $request) {
        $model = new ProductoPresentacion();
        $model->producto_id = $request->input('producto_id');
        $model->unidad_medida_id = $request->input('unidad_medida_id');
        $model->costo = $request->input('costo');
        $model->precio_venta = $request->input('precio_venta');
        $model->margen_ganancia = $request->input('margen_ganancia');
        $model->comision_barbero = $request->input('comision_barbero');
        $model->stock = $request->input('stock');
        $model->puede_vender = $request->input('puede_vender');
        $model->puede_comprar = $request->input('puede_comprar');
        $model->puede_asignar = $request->input('puede_asignar');
        $model->puede_consumir = $request->input('puede_consumir');
        $model->save();

        $model->codigos = $request->input('bar_codes');

        foreach($model->codigos as $codigo) {
            $producto_codigo = new ProductoCodigo();
            $producto_codigo->producto_id = $model->id;
            $producto_codigo->codigo = $codigo['codigo'];
            $producto_codigo->save();
        }

        return $model;
    }
    
    public function update(Request $request, $id) {
        $model = ProductoPresentacion::find($id);
        $model->producto_id = $request->input('producto_id');
        $model->unidad_medida_id = $request->input('unidad_medida_id');
        $model->costo = $request->input('costo');
        $model->precio_venta = $request->input('precio_venta');
        $model->margen_ganancia = $request->input('margen_ganancia');
        $model->comision_barbero = $request->input('comision_barbero');
        $model->stock = $request->input('stock');
        $model->puede_vender = $request->input('puede_vender');
        $model->puede_comprar = $request->input('puede_comprar');
        $model->puede_asignar = $request->input('puede_asignar');
        $model->puede_consumir = $request->input('puede_consumir');
        $model->save();

        DB::table('productos_codigos')->where('producto_id', $model->id)->delete();

        $model->codigos = $request->input('bar_codes');

        foreach($model->codigos as $codigo) {
            $producto_codigo = new ProductoCodigo();
            $producto_codigo->producto_id = $model->id;
            $producto_codigo->codigo = $codigo['codigo'];
            $producto_codigo->save();
        }

        return $model;
    }

    public function delete($id) {
        $model = ProductoPresentacion::find($id);
        $model->delete();
        return $model;
    }

}
