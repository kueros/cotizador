<?php

namespace App\Http\Controllers;

use App\Models\Funcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use App\Models\TipoTransaccionCampoAdicional;
use App\Models\TipoTransaccion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class FuncionController extends Controller
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
		$funciones = Funcion::paginate();
		return view('funcion.index', compact('funciones'))
			->with('i', ($request->input('page', 1) - 1) * $funciones->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$funciones = Funcion::get();
		#dd($funciones);
		$data = array();
		foreach ($funciones as $r) {
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_funcion(' . "'" . $r->id .
				"'" . ')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_funcion(' . "'" . $r->id .
				"'" . ')"><i class="bi bi-trash"></i></a>';
			$r->formula = str_replace(",", " ", $r->formula);

			$data[] = array(
				$r->nombre,
				" = " . $r->formula,
				$accion
			);
		}
		$output = array(
			"recordsTotal" => $funciones->count(),
			"recordsFiltered" => $funciones->count(),
			"data" => $data
		);

		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_store(Request $request, MyController $myController)
	{
		/*     $permiso_agregar_funciones = $myController->tiene_permiso('add_funcion');
		if (!$permiso_agregar_funciones) {
			abort(403, '.');
			return false;
		}
	*/

		// Validar los datos del usuario manualmente
		$validator = \Validator::make($request->all(), [
			'nombre' => 'required|string|max:255|regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/|unique:funciones,nombre',
			'formula' => 'required',
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
			'nombre.unique' => 'Este nombre de función ya está en uso.',
			'nombre.required' => 'El nombre de la función es requerido.',
			'formula.required' => 'La fórmula de la función es requerida.',
		]);

		// Si la validación falla, devolver errores
		if ($validator->fails()) {
			return response()->json([
				'status' => 0,
				'message' => 'Error de ingreso de datos',
				'errors' => $validator->errors()
			]);
		}

		// Crear la función si la validación es exitosa
		$validatedData = $validator->validated();
		$campo = funcion::create($validatedData);
		$campo->formula = $validatedData['formula'];
		$campo->save();

		// Loguear la acción
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó la función " . $request->input('nombre');
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		return response()->json([
			'status' => 1,
			'message' => 'Función creada correctamente.'
		]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_edit($id)
	{
		$funcion = Funcion::find($id);
		if (is_null($funcion->formula)) {
			$funcion->formula = [];  // Asegúrate de que sea un array vacío si no tiene valor
		}
		$formulaArray = collect(explode(',', rtrim($funcion->formula, ','))) // Separa y elimina la última coma
			->map(function ($item) {
				// Clasifica el elemento como operator o value
				$type = is_numeric($item) ? 'value' : 'operator';
				return ['type' => $type, 'value' => $item];
			});
		$resultado = [
			'id' => $funcion->id,
			'nombre' => $funcion->nombre,
			'formula' => $formulaArray,
		];
		$data = json_encode($resultado, JSON_PRETTY_PRINT);
		#dd($data);
		return response()->json($data);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function funcionesUpdate(Request $request, Funcion $funcion, MyController $myController)
	{
		#dd($request->all());
		/* 		$permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
		if (!$permiso_editar_funciones) {
			abort(403, '.');
			return false;
		}
*/
		// Validación de los datos
		$validatedData = Validator::make($request->all(), [
			'nombre' => 'required|string|max:255|min:3|regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/', // Solo letras sin acentos y espacios
			Rule::unique('alertas', 'nombre'),
			'formula' => 'required|string|max:255',
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre de alerta ya está en uso.',
			'formula.required' => 'Este campo no puede quedar vacío.',
		]);
		if ($validatedData->fails()) {
			$response = [
				'status' => 0,
				'message' => 'error en validacion',
				'errors' => $validatedData->errors()
			];
			return response()->json($response);
		}

		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		// Obtener el modelo
		$funciones_id = Funcion::where('id', $request->id)->first()['id'];
		$updated_id = DB::table('funciones')
			->where('id', $funciones_id)
			->update([
				'nombre' => $validated['nombre'],
				'formula' => $validated['formula'],
			]);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó la función " . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);
		$response = [
			'status' => 1,
			'message' => 'Función actualizada correctamente.'
		];
		return response()->json($response);
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
		$funcion = Funcion::find($id);
		$nombre = $funcion->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " eliminó la función " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$funcion->delete();
		return response()->json(["status" => true]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	#Route::post('/funciones/campos-transacciones', [TransaccionController::class, 'getCamposTransacciones']);

	public function obtenerCamposTransacciones(Request $request)
	{
		$tipoTransaccion = $request->input('tipo_transaccion');
		// Validar el dato recibido
		if (!$tipoTransaccion) {
			return response()->json(['error' => 'Tipo de transacción no especificado.'], 400);
		}

		$tipoTransaccionId = TipoTransaccion::where('nombre', $tipoTransaccion)->first()['id'];

		// Obtener los campos relacionados
		$campos = TipoTransaccionCampoAdicional::where('tipo_transaccion_id', $tipoTransaccionId)->get(['nombre_campo']);

		if ($campos->isEmpty()) {
			return response()->json(['error' => 'No se encontraron campos para el tipo seleccionado.'], 404);
		}

		// Transformar los datos al formato requerido
		$nombresCampos = $campos->pluck('nombre_campo')->map(function ($campo) {
			// Convertir a formato con primera letra en mayúscula
			return ucfirst(strtolower(str_replace('campo ', '', $campo)));
		});

		return response()->json($nombresCampos);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function obtenerTiposTransacciones()
	{
		$campos = DB::table('tipos_transacciones')->pluck('nombre');
		return response()->json($campos);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function contarTransacciones(Request $request)
	{
		#dd($request->input('nombre_mostrar'));
		$nombre_mostrar = $request->input('nombre_mostrar');
		$tipoTransaccionId = DB::table('tipos_transacciones_campos_adicionales')
			->where('nombre_mostrar', $nombre_mostrar)->value('tipo_transaccion_id');
		#dd($tipoTransaccionId);

		if (!$tipoTransaccionId) {
			return response()->json(['error' => 'ID de tipo de transacción no proporcionado'], 400);
		}

		$contador = DB::table('transacciones')
			->where('tipo_transaccion_id', $tipoTransaccionId)
			->count();

		return response()->json(['contador' => $contador]);
	}
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function acumularTransacciones(Request $request)
	{
		#dd($request->input('nombre_mostrar'));
		$nombre_mostrar = $request->input('nombre_mostrar');
		$tipoTransaccionId = DB::table('tipos_transacciones_campos_adicionales')
			->where('nombre_mostrar', $nombre_mostrar)->value('tipo_transaccion_id');
		#dd($tipoTransaccionId);

		if (!$tipoTransaccionId) {
			return response()->json(['error' => 'ID de tipo de transacción no proporcionado'], 400);
		}

		$acumulador = DB::table('transacciones')
			->where('tipo_transaccion_id', $tipoTransaccionId)
			->sum('monto');

		return response()->json(['acumulador' => $acumulador]);
	}
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function listado(Request $request)
	{
		$funciones = Funcion::select('id', 'nombre')->get();

		// Devuelve el arreglo de objetos JSON directamente
		return response()->json($funciones);
	}
}
