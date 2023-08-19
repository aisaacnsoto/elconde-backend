<?php

namespace App\Http\Controllers;

use App\Models\GlobalSystem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalSystemController extends Controller
{
    public function getComision() {
        return DB::table('global')->value('pago_comision');
    }

    public function updateComision(Request $request) {
        $global = GlobalSystem::find(1);
        $global->pago_comision = $request->input('comision');
        $global->save();
        return $global;
    }
}
