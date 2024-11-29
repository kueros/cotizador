<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\AlertaDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;


class AlertaTipoController extends Controller
{
	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function index(Request $request, MyController $myController): View
	{
/* 		$permiso_listar_funciones = $myController->tiene_permiso('list_funciones');
		if (!$permiso_listar_funciones) {
			abort(403, '.');
			return false;
		}
 */		
        $tipos_alertas = TipoAlerta::paginate();

        return view('alertas_tipos.index', compact('tipos_alertas'))
			->with('i', ($request->input('page', 1) - 1) * $tipos_alertas->perPage());
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
        $tipos_alertas = TipoAlerta::all();
		$data = array();
        foreach($tipos_alertas as $r) {
#dd($r);
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas_tipos('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alertas_tipos('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';

            $data[] = array(
                $r->nombre,
                $accion
            );
        }
        $output = array(
            "recordsTotal" => $tipos_alertas->count(),
            "recordsFiltered" => $tipos_alertas->count(),
            "data" => $data
        );
 
		return response()->json($output);
    }

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_store(Request $request, MyController $myController)
	{
		// Limpia el campo nombre
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
		// Reglas de validación
		$validatedData = Validator::make($request->all(), [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-Z\s]+$/', // Solo letras y espacios
				Rule::unique('tipos_alertas', 'nombre') // Verifica la unicidad en la tabla
			],
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
			'nombre.unique' => 'Este nombre ya está en uso.',
		]);
	
		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
	
		// Crear el registro
		$alerta = TipoAlerta::create([
			'nombre' => $request->input('nombre'),
		]);
	
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "$username creó un nuevo tipo de alerta: {$alerta->nombre}";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Tipo de Alerta creado correctamente.'
		]);
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){

        $data = TipoAlerta::find($id);
		return response()->json($data);
    }

    /*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function update(Request $request, TipoAlerta $alertas_tipos, MyController $myController)
	{
		#dd($request->id);
/* 		$permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
		if (!$permiso_editar_funciones) {
			abort(403, '.');
			return false;
		}
*/
		// Limpia el campo nombre
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
		// Reglas de validación
		$validatedData = Validator::make($request->all(), [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-Z\s]+$/', // Solo letras y espacios
				Rule::unique('tipos_alertas', 'nombre') // Verifica la unicidad en la tabla
			],
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
			'nombre.unique' => 'Este nombre ya está en uso.',
		]);
	
		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
		$alertas_tipos_nombre_viejo = TipoAlerta::where('id', $request->id)->first()['nombre'];
		// Obtener el modelo
		$alertas_tipos = TipoAlerta::where('id', $request->id)->first();
		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		// Actualizar el modelo con los datos validados
		$alertas_tipos->update($validated);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el tipo de alerta " . $alertas_tipos_nombre_viejo . ' a ' . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Tipo de Alerta actualizado correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController) {
		// Validar si existe el tipo de alerta
		$alerta_tipo = TipoAlerta::find($id);
		if (!$alerta_tipo) {
			return response()->json(["error" => "El tipo de alerta no existe."], 404);
		}
	
		// Verificar si el tipo de alerta está siendo usado en la tabla alertas
		$usado = Alerta::where('tipos_alertas_id', $id)->exists();
		if ($usado) {
			return response()->json([
				"error" => "No se puede eliminar el tipo de alerta porque está siendo usado en la tabla alertas."
			], 400);
		}
	
		// Datos para el log
		$nombre = $alerta_tipo->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " borró el tipo de alerta " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Eliminar el tipo de alerta
		$alerta_tipo->delete();
	
		return response()->json(["status" => true]);
	}
}
