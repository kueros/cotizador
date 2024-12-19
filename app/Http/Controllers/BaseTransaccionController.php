<?php

namespace App\Http\Controllers;

use App\Models\Transaccion;
use App\Models\TipoTransaccion;
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


class BaseTransaccionController extends Controller
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
/* 		$transacciones = Transaccion::
		leftJoin('tipos_transacciones', 'transacciones.tipo_transaccion_id', '=', 'tipos_transacciones.id')->
		select('tipos_transacciones.nombre', 'transacciones.*')->
		paginate();
*/
		$tipos_transacciones = TipoTransaccion::paginate();
		return view('base_transacciones.index', compact('tipos_transacciones'))
			->with('i', ($request->input('page', 1) - 1) * $tipos_transacciones->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function listado(Request $request)
	{
		$tipos_transacciones = TipoTransaccion::all();
		$data = array();
		foreach ($tipos_transacciones as $r) {
			$ver_transacciones = route('transacciones', ['id' => $r->id] );
			#dd($ver_transacciones);
			$accion = '<a class="btn btn-sm btn-info" href="' . $ver_transacciones . '" title="Ver Transacciones">Ver Transacciones</a>';

			$data[] = array(
				$r->nombre,
				$accion
			);
		}
		$output = array(
			"recordsTotal" => $tipos_transacciones->count(),
			"recordsFiltered" => $tipos_transacciones->count(),
			"data" => $data
		);

		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function store(Request $request, MyController $myController)
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
				'max:100',
				'min:3',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', // Solo letras sin acentos y espacios
				Rule::unique('transacciones', 'nombre') // Verifica la unicidad en la tabla
			],
			'descripcion' => [
				'string',
				'max:255',
				'min:3'
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
		$validated = $validatedData->validated(); // Datos validados

		// Crear el registro
		$transaccion = Transaccion::create([
			'nombre' => $validated['nombre'],
			'descripcion' => $validated['descripcion']
		]);

		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó la transacción \"$transaccion->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Transacción creada correctamente.'
		]);
	}


	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function edit($id)
	{

		$data = Transaccion::find($id);
		return response()->json($data);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function update(Request $request, Transaccion $transacciones,  MyController $myController)
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
				Rule::unique('transacciones', 'nombre') // Verifica la unicidad en la tabla
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
		$transacciones_nombre_viejo = Transaccion::where('id', $request->id)->first()['nombre'];
		// Obtener el modelo
		$transacciones = Transaccion::where('id', $request->id)->first();
		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		// Actualizar el modelo con los datos validados
		$transacciones->update($validated);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Actualizó el tipo de alerta \"$transacciones_nombre_viejo\" a \"$request->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Transacción actualizada correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function delete($id, MyController $myController)
	{
		// Validar si existe el tipo de alerta
		$transaccion = Transaccion::find($id);
		if (!$transaccion) {
			return response()->json(["error" => "El tipo de alerta no existe."], 404);
		}

		// Datos para el log
		$nombre = $transaccion->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó la transacción \"$nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Eliminar el tipo de alerta
		$transaccion->delete();

		return response()->json(["status" => true]);
	}
}
