<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\AlertaDetalle;
use App\Models\Funcion;
use App\Models\AlertaTipoTratamiento;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use App\Http\Controllers\AlertaTipoTratamientoController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AlertaController extends Controller
{
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function alertasIndex(Request $request, MyController $myController): View
	{
		/* 		$permiso_listar_funciones = $myController->tiene_permiso('list_funciones');
		if (!$permiso_listar_funciones) {
			abort(403, '.');
			return false;
		}
 */
		$funciones = Funcion::all();
		$tipos_alertas = TipoAlerta::all();
		$alertas_tipos_tratamientos = AlertaTipoTratamiento::all();

		$alertas =
			Alerta::leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')->leftJoin('alertas_tipos_tratamientos', 'alertas.tipos_tratamientos_id', '=', 'alertas_tipos_tratamientos.id')->select('tipos_alertas.nombre', 'alertas_tipos_tratamientos.nombre', 'alertas.*')->
			#where('tipo_transaccion_id', $tipo_transaccion_id)->
			#orderBy('nombre', 'asc')->
			paginate();
		return view('alertas.index', compact('alertas', 'tipos_alertas', 'alertas_tipos_tratamientos', 'funciones'))
			->with('i', ($request->input('page', 1) - 1) * $alertas->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$alertas =
			Alerta::leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')->
			leftJoin('alertas_tipos_tratamientos', 'alertas.tipos_tratamientos_id', '=', 'alertas_tipos_tratamientos.id')->
			select('tipos_alertas.nombre as tipo_alerta', 'alertas_tipos_tratamientos.nombre as tipo_tratamiento', 'alertas.*')->
			orderBy('nombre', 'asc')->
			get();
		$data = array();
		#dd($alertas);
		foreach ($alertas as $r) {
			$detalleAlerta = route('alertas_detalles', ['id' => $r->id]);
			$accion = '<a class="btn btn-sm btn-info" href="' . $detalleAlerta . '" title="Detalle Alerta">Detalle Alerta</a>';

			$accion .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas(' . "'" . $r->id .
				"'" . ')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alerta(' . "'" . $r->id .
				"'" . ')"><i class="bi bi-trash"></i></a>';

			$data[] = array(
				$r->nombre,
				$r->descripcion,
				$r->tipo_alerta,
				$accion
			);
		}
		#dd($data);
		$output = array(
			"recordsTotal" => $alertas->count(),
			"recordsFiltered" => $alertas->count(),
			"data" => $data
		);

		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_store(Request $request, MyController $myController)
	{
		#dd($request->all());
		// Limpia el campo nombre
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
	
		// Validar los datos básicos
		$validatedData = Validator::make($request->all(), [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/',
				Rule::unique('alertas', 'nombre'), // Asegura la unicidad
			],
			'descripcion' => 'required|string|max:255',
			'tipos_alertas_id' => 'required|integer|exists:tipos_alertas,id',
			//'tipos_tratamientos_id' => 'required|integer|exists:alertas_tipos_tratamientos,id',
			'funciones_id' => 'required|array', // Asegura que funciones_id sea un array
			'funciones_id.*' => 'integer|exists:funciones,id', // Cada elemento debe ser un ID válido
		], [
			'nombre.regex' => 'El nombre solo puede contener letras, números y espacios.',
			'nombre.unique' => 'Este nombre ya está en uso.',
			'tipos_alertas_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_alertas_id.exists' => 'El tipo de campo seleccionado no es válido.',
			//'tipos_tratamientos_id.required' => 'Este campo no puede quedar vacío.',
			//'tipos_tratamientos_id.exists' => 'El tipo de campo seleccionado no es válido.',
			'funciones_id.required' => '"Detalles de la Alerta" no puede quedar vacío.',
			'funciones_id.*.exists' => 'Una o más funciones seleccionadas no son válidas.',
		]);
	
		// Si la validación inicial falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
	
		// Validación adicional: Verificar duplicados en funciones_id
		$funcionesId = $request->input('funciones_id');
		if (count($funcionesId) !== count(array_unique($funcionesId))) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => ['funciones_id' => ['No se permiten funciones duplicadas dentro del mismo registro.']]
			]);
		}
	
		$validated = $validatedData->validated(); // Datos validados
	
		// Crear la alerta
		$alerta = Alerta::create([
			'nombre' => $validated['nombre'],
			'descripcion' => $validated['descripcion'],
			'tipos_alertas_id' => $validated['tipos_alertas_id'],
			//'tipos_tratamientos_id' => $validated['tipos_tratamientos_id'],
		]);
	
		// Crear detalles de alerta
		foreach ($validated['funciones_id'] as $index => $funcionId) {
			AlertaDetalle::create([
				'alertas_id' => $alerta->id,
				'funciones_id' => $funcionId,
				'fecha_desde' => \Carbon\Carbon::parse($request['fecha_desde'][$index])->format('d-m-Y'),
				'fecha_hasta' => \Carbon\Carbon::parse($request['fecha_hasta'][$index])->format('d-m-Y'),
			]);
		}
	
		// Loguear la acción
		$clientIP = \Request::ip();
		$clientIP_sc = str_replace('"', '', $clientIP);
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó la alerta \"{$validated['nombre']}\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Alerta creada correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_edit($id)
	{
		$alerta = Alerta::where('id', $id)->first();
		$alertasDetalles = AlertaDetalle::where('alertas_id', $id)->get();
		$response = response()->json([
			'alertas' => $alerta,
			'alertas_detalles' => $alertasDetalles,
			'funciones' => Funcion::all(), // Todas las funciones disponibles
			'alertas_tipos_tratamientos' => AlertaTipoTratamiento::all(), // Todos los tipos de tratamiento disponibles
		]);
		return $response;
	}


	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/


	 public function alertasUpdate(Request $request, Alerta $alerta, MyController $myController)
	 {
		 #dd($request->all());
		 // Validación de los datos
		 $validatedData = Validator::make($request->all(), [
			 'nombre' => 'required|string|max:255|min:3|regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', 
			 Rule::unique('alertas', 'nombre'),
			 'descripcion' => 'required|string|max:255',
			 'tipos_alertas_id' => 'required|integer|exists:tipos_alertas,id',
			 'funciones_id' => 'required|array',
			 'funciones_id.*' => 'integer|exists:funciones,id',
			 'fecha_desde' => 'required|array',
			 'fecha_desde.*' => 'date|date_format:Y-m-d',
			 'fecha_hasta' => 'required|array',
			 'fecha_hasta.*' => 'date|date_format:Y-m-d',
		 ], [
			 'fecha_desde.required' => 'El campo fecha_desde es obligatorio.',
			 'fecha_desde.*.date' => 'Cada fecha en fecha_desde debe ser una fecha válida.',
			 'fecha_desde.*.date_format' => 'Cada fecha en fecha_desde debe tener el formato Y-m-d.',
			 'fecha_hasta.required' => 'El campo fecha_hasta es obligatorio.',
			 'fecha_hasta.*.date' => 'Cada fecha en fecha_hasta debe ser una fecha válida.',
			 'fecha_hasta.*.date_format' => 'Cada fecha en fecha_hasta debe tener el formato Y-m-d.',
		 ]);
	 
		 if ($validatedData->fails()) {
			 return response()->json([
				 'status' => 0,
				 'message' => '',
				 'errors' => $validatedData->errors()
			 ]);
		 }
		 
		// Validación adicional: Verificar duplicados en funciones_id
		$funcionesId = $request->input('funciones_id');
		if (count($funcionesId) !== count(array_unique($funcionesId))) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => ['funciones_id' => ['No se permiten funciones duplicadas dentro del mismo registro.']]
			]);
		}
		 $validated = $validatedData->validated();
	 
		 // Obtener el registro existente (usando findOrFail para garantizar un modelo único)
		 $alertaExistente = Alerta::where('id', $request->alertas_id)->first();
		 if (!$alertaExistente) {
			 return response()->json([
				 'status' => 0,
				 'message' => 'Alerta no encontrada.'
			 ]);
		 }
	 
		 // Eliminar los detalles antiguos que no estén en la nueva solicitud
		 // Esto eliminará los registros de AlertaDetalle que no están en el array de funciones_id
		 if (isset($validated['funciones_id']) && is_array($validated['funciones_id'])) {
			 // Obtener los IDs de los detalles actuales
			 $funcionesIds = $validated['funciones_id'];
			 
			 // Eliminar los detalles que no estén en los nuevos datos
			 AlertaDetalle::where('alertas_id', $request->alertas_id)
				 ->whereNotIn('funciones_id', $funcionesIds)
				 ->delete();
		 }
	 
		 // Construir el mensaje de cambios
		 $cambios = [];
		 foreach (['nombre', 'descripcion', 'tipos_alertas_id'] as $campo) {
			 if ($alertaExistente->$campo != $validated[$campo]) {
				 $cambios[] = "cambiando $campo de \"{$alertaExistente->$campo}\" a \"{$validated[$campo]}\"";
			 }
		 }
	 
		 $mensajeCambios = implode(', ', $cambios);
		 $username = Auth::user()->username;
		 $message = "Actualizó la alerta \"{$alertaExistente->nombre}\" $mensajeCambios.";
	 
		 // Actualizar los datos de la alerta
		 $alertaExistente->update($validated);
	 
		 // Crear detalles de alerta (o actualizarlos)
		 foreach ($validated['funciones_id'] as $index => $funcionId) {
			 AlertaDetalle::updateOrInsert(
				 [
					 'alertas_id' => $request->input('alertas_id')[0],
					 'funciones_id' => $funcionId,
				 ],
				 [
					 'fecha_desde' => \Carbon\Carbon::parse($validated['fecha_desde'][$index])->format('d-m-Y'),
					 'fecha_hasta' => \Carbon\Carbon::parse($validated['fecha_hasta'][$index])->format('d-m-Y'),
				 ]
			 );
		 }
	 
		 // Loguear la acción
		 $clientIP = \Request::ip();
		 $userAgent = \Request::userAgent();
		 $myController->loguear($clientIP, $userAgent, $username, $message);
	 
		 return response()->json([
			 'status' => 1,
			 'message' => 'Alerta actualizada correctamente.'
		 ]);
	 }

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController)
	{
		/*         $permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			return response()->json(["error"=>"No tienes permiso para realizar esta acción, contáctese con un administrador."], "405");
		}
*/
		$alerta = Alerta::find($id);
		$alertas_detalles = AlertaDetalle::where('alertas_id', $alerta->id)->first();
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó la alerta \"$alerta->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$alertas_detalles->delete();
		$alerta->delete();
		return response()->json(["status" => true]);
	}
}
