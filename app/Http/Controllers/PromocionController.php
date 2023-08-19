<?php

namespace App\Http\Controllers;

use App\Models\Promocion;
use App\Models\PromocionCliente;
use App\Models\PromocionConfig;
use App\Models\PromocionServicio;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PromocionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Promocion::all();
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
        $model = new Promocion();
        $model->nombre = $request->input('nombre');
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
        //
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
        $model = Promocion::find($id);
        $model->nombre = $request->input('nombre');
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
        $model = Promocion::find($id);
        $model->delete();
        return $model;
    }

    public function especificos() {
        $recom = Promocion::find(1);
        $vip = Promocion::find(2);
        $cumpleanios = Promocion::find(3);
        $trabaj = Promocion::find(4);

        $recom_min = PromocionConfig::find('RECOM_MIN');
        $vip_min = PromocionConfig::find('VIP_MIN');
        $cumple_min = PromocionConfig::find('CUMPLE_DIAS_PRE');
        
        return response()->json([
            'recomendaciones' => [
                'min' => $recom_min->valor,
                'servicios' => $recom->servicios
            ],
            'clientes_vip' => [
                'min' => $vip_min->valor,
                'servicios' => $vip->servicios
            ],
            'cumpleanios' => [
                'min' => $cumple_min->valor,
                'servicios' => $cumpleanios->servicios
            ],
            'trabajadores' => [
                'clientes' => $trabaj->clientes,
                'servicios' => $trabaj->servicios
            ]
        ]);
    }

    public function updatePromoRecom(Request $request) {
        $promo_id = 1;
        $recom_min = PromocionConfig::find('RECOM_MIN');
        $recom_min->valor = $request->input('min');

        DB::table('promociones_servicios')->where('promocion', $promo_id)->delete();

        $servicios = $request->input('servicios');

        foreach ($servicios as $item) {
            $promoSer = new PromocionServicio();
            $promoSer->promocion = $promo_id;
            $promoSer->servicio = $item['servicio']['id'];
            $promoSer->descuento = $item['descuento'];
            $promoSer->save();
        }

        $recom_min->save();
        return $recom_min;
        // return $servicios;
    }

    public function updatePromoVip(Request $request) {
        $promo_id = 2;
        $vip_min = PromocionConfig::find('VIP_MIN');
        $vip_min->valor = $request->input('min');

        DB::table('promociones_servicios')->where('promocion', $promo_id)->delete();

        $servicios = $request->input('servicios');

        foreach ($servicios as $item) {
            $promoSer = new PromocionServicio();
            $promoSer->promocion = $promo_id;
            $promoSer->servicio = $item['servicio']['id'];
            $promoSer->descuento = $item['descuento'];
            $promoSer->save();
        }

        $vip_min->save();
        return $vip_min;
    }

    public function updatePromoCumple(Request $request) {
        $promo_id = 3;
        $cumple_dias_pre = PromocionConfig::find('CUMPLE_DIAS_PRE');
        $cumple_dias_pre->valor = $request->input('min');

        DB::table('promociones_servicios')->where('promocion', $promo_id)->delete();
        
        $servicios = $request->input('servicios');

        foreach ($servicios as $item) {
            $promoSer = new PromocionServicio();
            $promoSer->promocion = $promo_id;
            $promoSer->servicio = $item['servicio']['id'];
            $promoSer->descuento = $item['descuento'];
            $promoSer->save();
        }

        $cumple_dias_pre->save();
        return $cumple_dias_pre;
    }
    
    public function updatePromoTrabajador(Request $request) {
        $promo_id = 4;
        $promo_traba = Promocion::find($promo_id);

        // Actualizar servicios
        DB::table('promociones_servicios')->where('promocion', $promo_id)->delete();
        $servicios = $request->input('servicios');
        foreach ($servicios as $item) {
            $promoSer = new PromocionServicio();
            $promoSer->promocion = $promo_id;
            $promoSer->servicio = $item['servicio']['id'];
            $promoSer->descuento = $item['descuento'];
            $promoSer->save();
        }

        // Actualizar clientes
        DB::table('promociones_clientes')->where('promocion', $promo_id)->delete();
        $clientes = $request->input('clientes');
        foreach ($clientes as $item) {
            $promoSer = new PromocionCliente();
            $promoSer->promocion = $promo_id;
            $promoSer->cliente = $item['cliente']['id'];
            $promoSer->save();
        }

        return $promo_traba;
    }
}
