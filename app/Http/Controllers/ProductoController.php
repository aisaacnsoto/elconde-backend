<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\ProductoCodigo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Producto::with('presentaciones.barCodes')->orderBy('nombre')->get();
    }

    public function indexActivos($activo)
    {
        return Producto::where('activo', $activo)->where('stock', '>', 0)->get();
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
        $model = new Producto();
        $model->categoria = $request->input('categoria');
        $model->nombre = $request->input('nombre');
        $model->precio = $request->input('precio');
        $model->stock = $request->input('stock');
        $model->activo = $request->input('activo');
        $model->unidad_medida = $request->input('unidad_medida');
        $model->comision_barbero = $request->input('comision_barbero');
        $model->save();

        return $model;
    }


    public function show($search)
    {
        $sql = "SELECT pp.id, p.nombre, pp.precio_venta AS 'precio', um.unidad AS 'unidad_medida', (alm.cantidad / um.factor) AS 'stock'
                    FROM productos_presentaciones pp
                    LEFT JOIN productos_codigos pc ON pp.id = pc.producto_id
                    INNER JOIN productos p ON pp.producto_id = p.id
                    INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                    INNER JOIN (
                        SELECT p.id, p.nombre, SUM(um.factor * pp.stock) AS 'cantidad'
                        FROM productos_presentaciones pp
                        INNER JOIN productos p ON pp.producto_id = p.id
                        INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                        GROUP BY p.id
                        ORDER BY p.nombre
                    ) alm ON p.id = alm.id
                    WHERE pc.codigo = ? AND p.activo = 1
                    ORDER BY p.nombre, um.unidad";
        $result = DB::select($sql, [$search]);
        return $result;
    }

    public function getById($id) {
        return Producto::find($id);
    }

    public function search($tipo, $search = '')
    {
        $result = [];
        if (!empty($search)) {
            $sql = "SELECT pp.id, p.nombre, pp.precio_venta AS 'precio', um.unidad AS 'unidad_medida', (alm.cantidad / um.factor) AS 'stock'
                    FROM productos_presentaciones pp
                    LEFT JOIN productos_codigos pc ON pp.id = pc.producto_id
                    INNER JOIN productos p ON pp.producto_id = p.id
                    INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                    INNER JOIN (
                        SELECT p.id, p.nombre, SUM(um.factor * pp.stock) AS 'cantidad'
                        FROM productos_presentaciones pp
                        INNER JOIN productos p ON pp.producto_id = p.id
                        INNER JOIN unidades_medida um ON pp.unidad_medida_id = um.id
                        GROUP BY p.id, p.nombre
                        ORDER BY p.nombre
                    ) alm ON p.id = alm.id
                    WHERE (p.nombre LIKE CONCAT('%',?,'%') OR pc.codigo = ?) AND p.activo = 1";
            
            switch ($tipo) {
                case 'venta':
                    $sql .= " AND pp.puede_vender = 1";
                    break;
                case 'compra':
                    $sql .= " AND pp.puede_comprar = 1";
                    break;
                case 'asignacion':
                    $sql .= " AND pp.puede_asignar = 1";
                    break;
                case 'consumo':
                    $sql .= " AND pp.puede_consumir = 1";
                    break;
                default:
                    break;
            }

            $sql .= " ORDER BY p.nombre, um.unidad";
            $result = DB::select($sql, [$search, $search]);
        }

        return $result;
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
        $model = Producto::find($id);
        $model->categoria = $request->input('categoria');
        $model->nombre = $request->input('nombre');
        $model->precio = $request->input('precio');
        $model->stock = $request->input('stock');
        $model->activo = $request->input('activo');
        $model->unidad_medida = $request->input('unidad_medida');
        $model->comision_barbero = $request->input('comision_barbero');
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
        $model = Producto::find($id);
        $model->delete();
        return $model;
    }

    public function productosServicios() {
        $sql = "SELECT nombre FROM productos UNION ALL SELECT nombre FROM servicios ORDER BY 1";
        return DB::select($sql);
    }
}
