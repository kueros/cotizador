<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use App\Models\TipoTransaccionCampoAdicional;
use App\Models\TipoCampo;
use App\Models\Transaccion;
use App\Models\TipoTransaccion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;

class TipoTransaccionCampoAdicionalController extends Controller
{
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function index($id, Request $request, MyController $myController): View
	#public function index(Request $request, MyController $myController): View
	{
		/* 		$permiso_listar_roles = $myController->tiene_permiso('list_roles');
		if (!$permiso_listar_roles) {
			abort(403, '.');
			return false;
		}
*/
		#dd($id);
		$ultima_posicion = TipoTransaccionCampoAdicional::where('tipo_transaccion_id', $id)->max('orden_listado');
		$ultima_posicion = $ultima_posicion ? $ultima_posicion + 1 : 1;
		#dd($ultima_posicion);
		$tipo_transaccion_nombre = TipoTransaccion::where('id', $id)->first()['nombre'];
		$tipos_campos = TipoCampo::all();
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->paginate();
		return view('tipos_transacciones_campos_adicionales.index', compact('campos_adicionales', 'tipos_campos', 'id', 'tipo_transaccion_nombre', 'ultima_posicion'))
			->with('i', ($request->input('page', 1) - 1) * $campos_adicionales->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$tipo_transaccion_id = $request->input('tipo_transaccion_id');
	
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->where('tipo_transaccion_id', $tipo_transaccion_id)
			->orderBy('orden_listado', 'asc')
			->get();
	
		$data = array();
		foreach ($campos_adicionales as $r) {
			// Decodificar el campo valores si no está vacío o nulo
			$valores_decoded = !empty($r->valores) ? json_decode($r->valores, true) : [];
			
			// Formatear los valores como una cadena separada por comas
			$valores_formateados = is_array($valores_decoded) ? implode(', ', $valores_decoded) : '';
	
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-pencil"></i></a>';
			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-trash"></i></a>';
	
			$data[] = array(
				$r->nombre_campo,
				$r->nombre_mostrar,
				$r->orden_listado,
				$r->requerido == 1 ? 'Sí' : 'No',
				$r->tipo_nombre,
				$valores_formateados, // Usar los valores formateados aquí
				$accion
			);
		}
	
		$output = array(
			"recordsTotal" => $campos_adicionales->count(),
			"recordsFiltered" => $campos_adicionales->count(),
			"data" => $data
		);
	
		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_edit($id)
	{
		$data = TipoTransaccionCampoAdicional::find($id);
		if (!$data) {
			return response()->json(['error' => 'Registro no encontrado'], 404);
		}
		return response()->json($data);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController, Request $request)
	{
		/*$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
				if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		} */
		#print_r($id);
		$campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		$nombre = $campos_adicionales['nombre_mostrar'];
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó el campo adicional para tipo de transacción \"$nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
		Log::info($message);

		$campos_adicionales->delete();

		// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Campo adicional eliminado correctamente.'
		];
		return response()->json($response);
	}


	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_store(Request $request, MyController $myController)
	{
		// Validar los datos del usuario
		$formData = [];
		foreach ($request->input('form_data') as $input) {
			$formData[$input['name']] = $input['value'];
		}
		$validatedData = Validator::make($formData, [
			'nombre_campo' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/',
				Rule::unique('tipos_transacciones_campos_adicionales', 'nombre_campo'),
			],
			'nombre_mostrar' => 'required|string|max:255',
			'tipo' => 'required|integer|exists:tipos_campos,id',
			'posicion' => 'required|integer|min:1|max:100',
			'tipo_transaccion_id' => 'integer',
			'visible' => 'required|integer',
			'requerido' => 'required|integer',
			'valores' => 'nullable|array', // Validar valores si es necesario
		], [
			'nombre_campo.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre_campo.unique' => 'Este nombre de campo adicional ya está en uso.',
		]);
	
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
	
		$validated = $validatedData->validated();
	
		// Crear registro en tipos_transacciones_campos_adicionales
		$inserted_id = TipoTransaccionCampoAdicional::create($validated);
		$orden_listado = $validated['posicion'];
		$inserted_id->orden_listado = $orden_listado;
	
		if ($validated['tipo'] == 4) { // Tipo selector
			$formData = $request->input('form_data');
	
			$valores = array_map(function ($item) {
				return $item['value'];
			}, array_filter($formData, function ($item) {
				return $item['name'] === 'valores[]';
			}));
	
			if (empty($valores)) {
				return response()->json([
					'errors' => ['valores' => 'Se deben agregar valores para el selector.']
				], 400);
			}
	
			$inserted_id->valores = json_encode($valores);
			$inserted_id->save();
		}
	
		$nombre_campo = $validated['nombre_campo'];
		$tipo = $validated['tipo'];
		$tabla = 'transacciones';
	
		$tipoDB = TipoCampo::where('id', $tipo)->value('tipo');
		if (!$tipoDB) {
			return response()->json([
				'status' => 0,
				'message' => "El tipo $tipo no es válido para crear una columna en la tabla.",
			], 400);
		} else {
			if (!Schema::hasColumn($tabla, $nombre_campo)) {
				Schema::table($tabla, function (Blueprint $table) use ($nombre_campo, $tipoDB) {
					$table->{$tipoDB}($nombre_campo)->nullable()->after('tipo_transaccion_id');
				});
			}
	
			// Insertar valores JSON en la columna si es un selector
			if ($tipo == 4) {
				DB::table($tabla)->update([
					$nombre_campo => json_encode($valores)
				]);
			}
		}
	
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó el campo adicional para tipo de transacción \"$nombre_campo\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Campo adicional creado correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function update(Request $request, MyController $myController)
	{
		// Extraer el contenido de form_data
		$formData = [];
		foreach ($request->input('form_data') as $input) {
			// Si el nombre contiene "[]" (indicando un array), almacenamos múltiples valores
			if (str_ends_with($input['name'], '[]')) {
				$key = rtrim($input['name'], '[]');
				$formData[$key][] = $input['value'];
			} else {
				$formData[$input['name']] = $input['value'];
			}
		}
	
		// Convertir el array 'valores' a JSON
		if (isset($formData['valores']) && is_array($formData['valores'])) {
			$formData['valores'] = json_encode($formData['valores']);
		}
	
		// Validar los datos
		$validatedData = Validator::make($formData, [
			'nombre_campo' => 'required|string|max:255',
			'nombre_mostrar' => 'required|string|max:255',
			'tipo' => 'required|integer',
			'visible' => 'required|integer',
			'requerido' => 'required|integer',
			'posicion' => 'required|integer',
			'valores' => 'string', // Ahora será un string JSON
		]);
	
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
	
		$validated = $validatedData->validated();
	
		$tipo_transaccion_campo_adicional = TipoTransaccionCampoAdicional::findOrFail($formData['id']);
	
		// Obtener el registro existente (usando findOrFail para garantizar un modelo único)
		$campoExistente = TipoTransaccionCampoAdicional::where('id', $formData['id'])->first();
		if (!$campoExistente) {
			return response()->json([
				'status' => 0,
				'message' => 'Campo Adicional no encontrado.'
			]);
		}



		// Actualizar el modelo con los datos validados
		$tipo_transaccion_campo_adicional->update($validated);
	
		// Asignar 'valores' si es necesario
		if (isset($validated['valores'])) {
			$tipo_transaccion_campo_adicional->valores = $validated['valores'];
			$tipo_transaccion_campo_adicional->save();
		}
	


		// Construir el mensaje de cambios
		$cambios = [];
		foreach (['nombre_campo', 'nombre_mostrar', 'visible', 'requerido', 'tipo', 'valores'] as $campo) {
			if ($campoExistente->$campo != $validated[$campo]) {
				$cambios[] = "cambiando $campo de \"{$campoExistente->$campo}\" a \"{$validated[$campo]}\"";
			}
		}
		$mensajeCambios = implode(', ', $cambios);
		$username = Auth::user()->username;
		$message = "Actualizó el campo adicional \"{$campoExistente->nombre_campo}\" $mensajeCambios.";


		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		#$message = "Actualizó el campo adicional para tipo de transacción ".$validated['nombre_campo'].".";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$response = [
			'status' => 1,
			'message' => 'Campo adicional de tipo de transacción actualizado correctamente.'
		];
		return response()->json($response);
	}





	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/

	public function edit($id, MyController $myController): View
	{
		#dd($id);
		/* 		$permiso_editar_roles = $myController->tiene_permiso('edit_rol');
		if (!$permiso_editar_roles) {
			abort(403, '.');
			return false;
		}
*/
		/* 		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		#dd($roles);
		return view('tipos_transacciones_campos_adicionales.edit', compact('tipos_transacciones_campos_adicionales'));
*/
		$tipos_campos = TipoCampo::all();
		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::where('tipos_transacciones_campos_adicionales.id', $id)
			->leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->first();
		#dd($tipos_transacciones_campos_adicionales);
		return view('tipos_transacciones_campos_adicionales.edit', compact('tipos_transacciones_campos_adicionales', 'tipos_campos'));
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function destroy($id, MyController $myController): RedirectResponse
	{
		/* 		$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		}
*/
		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		// Almacena el nombre de rol antes de eliminarlo
		$nombre = $tipos_transacciones_campos_adicionales->nombre_campo;
		// Elimina el rol
		$tipos_transacciones_campos_adicionales->delete();
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó el campo adicional para tipo de transacción \"$nombre_campo\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
		Log::info($message);

		// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Campo adicional creado correctamente.'
		];
		return response()->json($response);
	}

}
