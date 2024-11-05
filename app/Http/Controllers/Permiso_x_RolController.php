<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Models\Permiso_x_Rol;
use App\Models\Permiso;
use App\Models\Rol;
use App\Models\Seccion;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\DB;


class Permiso_x_RolController extends Controller
{

	public function index( MyController $myController)
	{
		$permiso_asignar_permisos = $myController->tiene_permiso('manage_perm');
		if (!$permiso_asignar_permisos) {
			abort(403, '.');
			return false;
		}
		$secciones = Seccion::get();
		$permisos = Permiso::orderBy('seccion_id')->get();
		$roles = Rol::get();
		$permisosRoles = Permiso_x_Rol::get();

		return view('permiso_x_rol.index', compact('roles', 'permisos', 'permisosRoles', 'secciones'));
	}

	public function updatePermisos(Request $request, MyController $myController)
	{
		$permiso_asignar_permisos = $myController->tiene_permiso('manage_perm');
		if (!$permiso_asignar_permisos) {
			abort(403, '.');
			return false;
		}
		#dd($request);
		$arrayFormulario = $request->input('id', []);
		$idsPermitidos = array_keys($arrayFormulario); // Inicia con los IDs del formulario

		// Paso 1: Recorremos el array recibido
		foreach ($arrayFormulario as $key => $valor) {
			if (str_contains($key, 'new_')) {
				// Extraemos rol_id y permiso_id de la clave del array (cuando es un nuevo registro)
				[$prefix, $rolId, $permisoId] = explode('_', $key);
				$permiso_nombre = Permiso::where('id', $permisoId)->first()['descripcion'];
				$rol_nombre = Rol::where('rol_id', $rolId)->first()['nombre'];
				// Creamos el nuevo registro
				$nuevoRegistro = Permiso_x_Rol::create([
					'rol_id' => $rolId,
					'permiso_id' => $permisoId,
					'habilitado' => 1, // Asignamos habilitado a 1 para los nuevos permisos
				]);
				// Agregamos el ID recién creado a la lista de IDs permitidos
				$idsPermitidos[] = $nuevoRegistro->id;
				$clientIP = \Request::ip();
				$userAgent = \Request::userAgent();
				$username = Auth::user()->username;
				$message = $username . " actualizó el permiso " . $permiso_nombre . 
										" para el rol " . $rol_nombre;
				$myController->loguear($clientIP, $userAgent, $username, $message);
			} else {
				// Buscamos en la tabla por el ID existente
				$registro = Permiso_x_Rol::where('id', $key)->first();
				if ($registro && $registro->habilitado == 0) {
					$registro->habilitado = 1;
					$registro->save();
				}
				#dd($registro);
				$permiso_nombre = Permiso::where('id', $registro->permiso_id)->first()['descripcion'];
				$rol_nombre = Rol::where('rol_id', $registro->rol_id)->first()['nombre'];
				// Aseguramos que el ID también esté en los permitidos
				$idsPermitidos[] = $key;
/* 				$clientIP = \Request::ip();
				$userAgent = \Request::userAgent();
				$username = Auth::user()->username;
				$message = $username . " actualizó el permiso " . $permiso_nombre . 
										" para el rol " . $rol_nombre;
				$myController->loguear($clientIP, $userAgent, $username, $message);
 */			}
		}

		// Paso 2: Eliminamos los registros que no están en el array del formulario ni en los nuevos
		Permiso_x_Rol::whereNotIn('id', $idsPermitidos)->delete();

		return redirect()->back()->with('success', 'Tabla actualizada correctamente.');
	}
}