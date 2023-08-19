<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KardexController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = DB::select('SELECT * FROM view_kardex WHERE producto_id = ? ORDER BY fecha, created_at', [$id]);

        $saldo = 0;

        for ($i = 0; $i < count($data); $i++) {
            $dataTmp = $data[$i];
            $tipo = $dataTmp->tipo;
            $entrada = (int) $dataTmp->entrada;
            $salida = (int) $dataTmp->salida;

            switch ($tipo) {

                case 'COMPRA': 
                    $saldo += $entrada;
                    break;

                case 'CONSUMO INTERNO': 
                    $saldo -= $salida;
                    break;

                case 'VENTA': 
                    $saldo -= $salida;
                    break;

                case 'INVENTARIO': 
                    $saldo = $dataTmp->saldo;
                    break;

                case 'ASIGNACION': 
                    $saldo -= $salida;
                    break;
                
            }

            // establecer hora
            $fechaArray = explode(' ', $dataTmp->created_at);
            $hora = (count($fechaArray) == 2 ? $fechaArray[1] : '');

            $data[$i]->hora = $hora;
            $data[$i]->saldo = $saldo;
        }

        return array_reverse($data);
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
