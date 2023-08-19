<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use App\Models\UsuarioPermiso;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class UsuarioController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Usuario::where('id', '>', 1)->orderBy('nombre_display')->get();
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
        $usuario = new Usuario();
        $usuario->nombre_display = $request->input('nombre_display');
        $usuario->empleado = $request->input('empleado');
        $usuario->rol = $request->input('rol');
        $usuario->username = $request->input('username');
        $usuario->password = $request->input('password');
        $usuario->activo = $request->input('activo');
        $usuario->save();

        $permisos = $request->input('permisos');

        foreach ($permisos as $permiso_id) {
            $usuario_permiso = new UsuarioPermiso();
            $usuario_permiso->usuario_id = $usuario->id;
            $usuario_permiso->permiso_id = $permiso_id;
            $usuario_permiso->save();
        }

        return $usuario;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario = Usuario::find($id);
        // $permisos_concedidos = $usuario->permisos;

        // $i = 0;

        // $permisos = [];

        // foreach (Permiso::all() as $permiso) {
        //     $permisoTmp = $permiso;
        //     if ($i < count($permisos_concedidos) && $permisos_concedidos[$i]->id == $permisoTmp->id) {
        //         $permisoTmp->granted == true;
        //         $i++;
        //     }
        //     $permisos[] = $permisoTmp;
        // }
        // $usuario->permisos = $permisos;

        return $usuario;
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
        $usuario = Usuario::find($id);
        $usuario->nombre_display = $request->input('nombre_display');
        $usuario->empleado = $request->input('empleado');
        $usuario->rol = $request->input('rol');
        $usuario->username = $request->input('username');
        $usuario->password = $request->input('password');
        $usuario->activo = $request->input('activo');
        $usuario->update();

        $permisos = $request->input('permisos');

        DB::table('usuario_permisos')->where('usuario_id', $usuario->id)->delete();

        foreach ($permisos as $permiso_id) {
            $usuario_permiso = new UsuarioPermiso();
            $usuario_permiso->usuario_id = $usuario->id;
            $usuario_permiso->permiso_id = $permiso_id;
            $usuario_permiso->save();
        }

        return $usuario;
    }

    public function updateAdmin(Request $request)
    {
        $usuario = Usuario::find(1);
        $usuario->nombre_display = $request->input('nombre_display');
        $usuario->username = $request->input('username');
        $usuario->password = $request->input('password');
        $usuario->save();

        
        $user = Usuario::find(1);
        
        $permisos = [];
        foreach ($user->permisos as $permiso) {
            $permisos[] = $permiso->id;
        }

        return response()->json([
            'id' => $user->id,
            'nombre_display' => $user->nombre_display,
            'rol' => $user->rol,
            'permisos' => $permisos
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $model = Usuario::find($id);
        $model->delete();
        return $model;
    }

    public function login(Request $request) {
        $username = $request->input('username');
        $password = $request->input('password');
        // echo $username;
        // echo '<hr>';
        // echo $password;
        $user = Usuario::where('username', $username)->where('password', $password)->first();
        if ($user) {
            $model = Usuario::find($user->id);

            $permisos = [];
            foreach ($model->permisos as $permiso) {
                $permisos[] = $permiso->id;
            }

            return response()->json([
                'id' => $user->id,
                'nombre_display' => $user->nombre_display,
                'rol' => $user->rol,
                'permisos' => $permisos
            ]);
        } else {
            return response()->json([
                'message' => 'Datos incorrectos'
            ]);
        }
        // die();
        // return $username;
    }

    public function cajerosActivos() {
        return DB::table('usuarios')
                 ->where('rol', '>', 1)
                 ->where('activo', 1)
                 ->orderBy('rol')
                 ->orderBy('nombre_display')
                 ->get();
    }
}
