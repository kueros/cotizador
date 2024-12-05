<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\AlertaDetalle;
use App\Models\AlertaTipoTratamiento;
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

class AlertaTipoTratamientoController extends Controller
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
        $alertas_tipos_tratamientos = AlertaTipoTratamiento::paginate();

        return view('alertas_tipos_tratamientos.index', compact('alertas_tipos_tratamientos'))
			->with('i', ($request->input('page', 1) - 1) * $alertas_tipos_tratamientos->perPage());
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
        #dd($request);
        $alertas_tipos_tratamientos = AlertaTipoTratamiento::all();
		$data = array();
        foreach($alertas_tipos_tratamientos as $r) {
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas_tipos_tratamientos('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alertas_tipos_tratamientos('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';

            $data[] = array(
                $r->nombre,
                $accion
            );
        }
        $output = array(
            "recordsTotal" => $alertas_tipos_tratamientos->count(),
            "recordsFiltered" => $alertas_tipos_tratamientos->count(),
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
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', // Solo letras sin acentos y espacios
				Rule::unique('alertas_tipos_tratamientos', 'nombre') // Verifica la unicidad en la tabla
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
		$tipo_tratamiento = AlertaTipoTratamiento::create([
			'nombre' => $request->input('nombre'),
		]);
	
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó el tipo de tratamiento \"$tipo_tratamiento->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Tipo de Tratamiento creado correctamente.'
		]);
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){

        $data = AlertaTipoTratamiento::find($id);
		return response()->json($data);
    }

    /*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function update(Request $request, AlertaTipoTratamiento $alertas_tipos_tratamientos, MyController $myController)
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
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', // Solo letras sin acentos y espacios
				Rule::unique('alertas_tipos_tratamientos', 'nombre') // Verifica la unicidad en la tabla
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
		$alertas_tipos_tratamientos_viejo = AlertaTipoTratamiento::where('id', $request->id)->first()['nombre'];
		// Obtener el modelo
		$alertas_tipos_tratamientos = AlertaTipoTratamiento::where('id', $request->id)->first();
		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		// Actualizar el modelo con los datos validados
		$alertas_tipos_tratamientos->update($validated);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Actualizó el tipo de tratamiento \"$alertas_tipos_tratamientos_viejo\" a \"$request->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Tipo de tratamiento actualizado correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController) {
		// Validar si existe el tipo de alerta
		$alerta_tipo_tratamiento = AlertaTipoTratamiento::find($id);
		if (!$alerta_tipo_tratamiento) {
			return response()->json(["error" => "El tipo de tratamiento no existe."], 404);
		}
	
		// Verificar si el tipo de alerta está siendo usado en la tabla alertas
		$usado = Alerta::where('tipos_tratamientos_id', $id)->exists();
		if ($usado) {
			return response()->json([
				"error" => "No se puede eliminar el tipo de tratamiento porque está siendo usado en la tabla alertas."
			], 400);
		}
	
		// Datos para el log
		$nombre = $alerta_tipo_tratamiento->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó el tipo de tratamiento \"$nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Eliminar el tipo de tratamiento
		$alerta_tipo_tratamiento->delete();
	
		return response()->json(["status" => true]);
	}
}
