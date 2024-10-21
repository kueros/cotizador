<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Permiso_x_Rol;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Seccion;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\DB;
class Permiso_x_RolController extends Controller
{

	public function index()
	{
		$secciones = Seccion::get();
		$permisos = Permiso::orderBy('seccion_id')->get();
		$roles = Rol::get();
		$permisosRoles = Permiso_x_Rol::get();
		/* 		$roles = 
					DB::table('roles')
					->leftJoin('permisos_x_rol', 'roles.rol_id', '=', 'permisos_x_rol.rol_id')
					->leftJoin('permisos', 'permisos.id', '=', 'permisos_x_rol.permiso_id')
					->leftJoin('secciones', 'permisos.seccion_id', '=', 'secciones.seccion_id')
					->select(
						'permisos_x_rol.habilitado as habilitado',
						'permisos.id as permiso_id', 
						'permisos.nombre as permiso_nombre', 
						'roles.rol_id as rol_id', 
						'roles.nombre as rol_nombre', 
						'secciones.seccion_id as seccion_id', 
						'secciones.nombre as seccion_nombre'
					)			->get();
		*/
		/* 		$permisosRoles =
					DB::table('permisos_x_rol')
					->leftJoin('permisos', 'permisos_x_rol.permiso_id', '=', 'permisos.id')
					->leftJoin('roles', 'permisos_x_rol.rol_id', '=', 'roles.rol_id')
					->leftJoin('secciones', 'permisos.seccion_id', '=', 'secciones.seccion_id')
					->orderBy('seccion_id')
					->select(
						'permisos_x_rol.habilitado as habilitado',
						'permisos.id as permiso_id', 
						'permisos.nombre as permiso_nombre', 
						'roles.rol_id as rol_id', 
						'roles.nombre as rol_nombre', 
						'secciones.seccion_id as seccion_id', 
						'secciones.nombre as seccion_nombre'
					)			->get();
		*/

			#dd($data);
		#dd($roles);
			#dd($permisosRoles);
        return view('permiso_x_rol.index', compact('roles', 'permisos', 'permisosRoles', 'secciones'));
	}

/* 	public function showPermisosPorRol()
	{
		$roles = Rol::with('permisos')->get();
		$permisos = Permiso::orderBy('seccion_id')->get();
		$secciones = Seccion::with('permisos')->get();

		return view('permisos_x_rol', compact('roles', 'permisos', 'secciones'));
	}
 */
	public function updatePermisos(Request $request)
	{
		#dd($request);
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
