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
#dd($campos_adicionales);// ->toSql(), $campos_adicionales->getBindings());

		$data = array();
		foreach ($campos_adicionales as $r) {
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-pencil"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-trash"></i></a>';
			$data[] = array(
				$r->nombre_campo,
				$r->nombre_mostrar,
				$r->orden_listado,
				$r->requerido == 1 ? 'Sí' : 'No', 
				$r->tipo_nombre,
				$r->valores = json_decode($r->valores),
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
		#dd($id);
		#$data = TipoTransaccionCampoAdicional::where('id',$id)->first();
		$data = TipoTransaccionCampoAdicional::find($id);

		if (!$data) {
			return response()->json(['error' => 'Registro no encontrado'], 404);
		}

		return response()->json($data);
	}
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController)
	{
		/*$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
				if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		} */
		#print_r($id);
		$campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		$nombre = $campos_adicionales->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " borró el tipo de transacción " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$campos_adicionales->delete();
		return response()->json(["status" => true]);
	}


	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_guardar_columna(Request $request, MyController $myController)
	{
		#print_r($request->all());
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
				'regex:/^[a-zA-Z\s]+$/', // Solo letras sin acentos y espacios
				Rule::unique('tipos_transacciones_campos_adicionales', 'nombre_campo'),
			],
			'nombre_mostrar' => 'required|string|max:255',
			'tipo' => 'required|integer|exists:tipos_campos,id',
			'posicion' => 'required|integer|min:1|max:100',
			'tipo_transaccion_id' => 'integer',
			'visible' => 'required|integer',
			'requerido' => 'required|integer',
			'valores' => 'nullable|array', // Si tipo == 4, esto será validado manualmente
		], [
			'nombre_campo.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre_campo.unique' => 'Este nombre de campo adicional ya está en uso.',
			'tipo.required' => 'Este campo no puede quedar vacío.',
			'tipo.exists' => 'El tipo de campo seleccionado no es válido.',
			'requerido.required' => 'Este campo no puede quedar vacío.',
		]);
		#print_r($validatedData);
		// Verificar si la validación falla
		if ($validatedData->fails()) {
			$response = [
				'status' => 0,
				'message' => 'error en validacion',
				'errors' => $validatedData->errors()
			];
			return response()->json($response);
		}

		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		$inserted_id = TipoTransaccionCampoAdicional::create($validated);
		$orden_listado = $validated['posicion'];
		$inserted_id->orden_listado = $orden_listado;
		$inserted_id->save();
		// Si el tipo es "selector" (id = 4), verificar valores
		if ($validated['tipo'] == 4 ) {
			if (empty($validated['tipo'])) {
				return response()->json([
					'errors' => ['valores' => 'Se deben agregar valores para el selector.']
				], 400);
			} else {
				$valores = $validated['valores'];
				$inserted_id->valores = json_encode($valores);
				$inserted_id->save();
			}
		}
	 	$nombre_campo = $validated['nombre_campo'];
		$tipo = $validated['tipo'];

		// Crear la columna en la tabla
		$tabla = 'transacciones';
		$tipoDB = TipoCampo::where('id', $tipo)->value('tipo');
		if (!$tipoDB) {
			return response()->json([
				'status' => 0,
				'message' => "El tipo $tipo no es válido para crear una columna en la tabla.",
			], 400);
		} else {
			if (!Schema::hasColumn($tabla, $nombre_campo)) {
				$tipoDB = TipoCampo::where('id', $tipo)->value('tipo');
				Schema::table($tabla, function (Blueprint $table) use ($nombre_campo, $tipoDB) {
					$table->{$tipoDB}($nombre_campo)->nullable()->after('nombre');
				});
			}
		
		}

	
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "$username creó el campo adicional para tipo de transacción $nombre_campo";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
			/*$response = [
			'status' => 0,
			'message' => $validatedData->errors()
		];*/
	// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Campo adicional creado correctamente.'
		];
		return response()->json($response);
	}



	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function create(MyController $myController): View
	{
		/* 		$permiso_agregar_roles = $myController->tiene_permiso('add_rol');
		if (!$permiso_agregar_roles) {
			abort(403, '.');
			return false;
		} */
		$campos_adicionales = new CampoAdicionalTipoTransaccion();
		return view('rol.create', compact('roles'));
	}

	/*******************************************************************************************************************************
	 * 
	 ********************************************************************************************************************************/

	public function store(Request $request, MyController $myController)
	{
		#dd($request->valores);

		#dd($request->valores);
		$formData = [];
		foreach ($request->input('form_data') as $input) {
			$formData[$input['name']] = $input['value'];
		}
		// Validar los datos del usuario
		$validatedData = Validator::make($formData, [
			'nombre_campo' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
				Rule::unique('tipos_transacciones_campos_adicionales'),
			],
			'nombre_mostrar' => [
				'string',
				'max:255',
				'min:3',
				'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
			],
			'visible' => [
				'integer',
			],
			'requerido' => [
				'integer',
			],
			'tipo' => [
				'integer',
			],
			[
				'nombre_campo.regex' => 'El nombre solo puede contener letras y espacios.',
				'nombre_campo.unique' => 'Este nombre de tipo de transacción ya está en uso.',
			]
		]);

		if ($validatedData->fails()) {
			$response = [
				'status' => 0,
				'message' => 'Error en validacion',
				'errors' => $validatedData->errors()
			];
			return response()->json($response);
		}

		/*         $tipoTransacciónExistente = CampoAdicionalTipoTransaccion::where('nombre', $request->input('nombre'))->first();
		if ($tipoTransacciónExistente) {
			return redirect()->back()->withErrors(['nombre' => 'Este nombre de tipo de transacción ya está en uso.'])->withInput();
		}
		*/
		//$validatedData = $validatedData->validated();
		$campo = TipoTransaccionCampoAdicional::create($validatedData);
		$valores = implode(',', $request->valores);
		$campo->valores = $valores;
		$campo->save();
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el campo adicional para tipo de transacción " . $request->input('nombre');
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$response = [
			'status' => 1,
			'message' => 'Campo adicional para tipo de transacción creado exitosamente.'
		];
		return response()->json($response);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/

	public function update(Request $request)
	{
		#dd($request);
		// Validar los datos
		
		$formData = [];
		foreach ($request->input('form_data') as $input) {
			if( $input['name'] == "posicion" ){
				$formData['orden_listado'] = $input['value'];
			} else {
				$formData[$input['name']] = $input['value'];
			}
		}
		
		$validatedData = Validator::make($formData, [
			'nombre_campo' => 'required|string|max:255',
			'nombre_mostrar' => 'required|string|max:255',
			'tipo' => 'required|integer',
			'visible' => 'required|integer',
			'requerido' => 'required|integer',
			'orden_listado' => 'required|integer'
		]);

		if ($validatedData->fails()) {
			$response = [
				'status' => 0,
				'message' => 'Error en validacion',
				'errors' => $validatedData->errors()
			];
			return response()->json($response);
		}
		$validated = $validatedData->validated(); // Obtiene los datos validados como array

		#dd($validatedData);
		$tipoTransaccionId = $formData['tipo_transaccion_id'];
		#dd($tipoTransaccionId);
		$tipo_transaccion_campo_adicional = TipoTransaccionCampoAdicional::findOrFail($formData['id']);
		// Actualizar el modelo con los datos validados
		$tipo_transaccion_campo_adicional->update($validated);

		#return redirect()->route('tipos_transacciones_campos_adicionales')->with('success', 'Campo adicional de tipo de transacción actualizado correctamente.');

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
		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::
			where('tipos_transacciones_campos_adicionales.id', $id)
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
		$message = Auth::user()->username . " borró el campo adicional de tipo de transacción " . $nombre;
		Log::info($message);
		return Redirect::route('tipos_transacciones_campos_adicionales')
			->with('success', 'Campo adicional de tipo de transacción exitosamente borrado');
	}


}
