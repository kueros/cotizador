<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Permiso_x_Rol;
use App\Models\Permiso;
use App\Models\Rol;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;

class Permiso_x_RolControllerSuperadmin extends Controller
{

    public function index()
    {
        // Obtener todos los roles con sus permisos
        $roles = Rol::with('permisos')->get();

        // Obtener todos los permisos ordenados
        $permisos = Permiso::orderBy('orden', 'asc')->get();

        $permisosRoles = Permiso_x_Rol::all();//with(['permiso', 'permiso.seccion', 'rol'])
            #->where('rol_id', 1) // Ejemplo: Filtro por un rol específico
            ->get();
dd($permisosRoles);
        return view('permiso_x_rol.index', compact('roles', 'permisos', 'permisosRoles'));
    }



    public function updatePermisos(Request $request)
    {
        foreach ($request->input('permisos', []) as $rolId => $permisos) {
            foreach ($permisos as $permisoId => $habilitado) {
                // Verificar si ya existe un registro para este rol y permiso
                $permisoRol = Permiso_x_Rol::where('rol_id', $rolId)
                    ->where('permiso_id', $permisoId)
                    ->first();

                if ($habilitado) {
                    // Si el checkbox está marcado, y no existe en la tabla, lo creamos
                    if (!$permisoRol) {
                        Permiso_x_Rol::create([
                            'rol_id' => $rolId,
                            'permiso_id' => $permisoId,
                            'habilitado' => 1,
                        ]);
                    }
                } else {
                    // Si el checkbox no está marcado, eliminamos el permiso si existe
                    if ($permisoRol) {
                        $permisoRol->delete();
                    }
                }
            }
        }

        return redirect()->back()->with('success', 'Permisos actualizados correctamente.');
    }

}
