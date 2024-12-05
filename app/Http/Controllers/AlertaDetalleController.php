<?php

namespace App\Http\Controllers;

use App\Models\AlertaDetalle;
use App\Models\Funcion;
use App\Models\Alerta;
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

class AlertaDetalleController extends Controller
{
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function index($id, Request $request, MyController $myController): View
	{
		#dd($request);
		$funciones = Funcion::all()->keyBy('id'); // Indexar por ID para un acceso rápido
		$alertas_nombre = Alerta::where('id', $id)->first()['nombre'];

		// Obtener los detalles de las alertas
		$alertas_detalles = AlertaDetalle::where('alertas_id', $id)->get();

		// Transformar los detalles
		$detalles = $alertas_detalles->flatMap(function ($detalle) use ($funciones) {
			$funcionesIds = explode(',', $detalle->funciones_id);
			$fechasDesde = explode(',', $detalle->fecha_desde);
			$fechasHasta = explode(',', $detalle->fecha_hasta);

			$rows = [];
			foreach ($funcionesIds as $index => $funcionId) {
				$nombreFuncion = $funciones[$funcionId]->nombre ?? null; // Obtener nombre de la función
				$rows[] = [
					'nombre_funcion' => $nombreFuncion,
					'alertas_id' => $detalle->alertas_id,
					'fecha_desde' => $fechasDesde[$index] ?? null,
					'fecha_hasta' => $fechasHasta[$index] ?? null,
					'created_at' => $detalle->created_at,
					'updated_at' => $detalle->updated_at,
				];
			}
			return $rows;
		})->toArray();
		#dd($detalles);
		return view('alertas_detalles.index', compact('detalles', 'id', 'funciones', 'alertas_nombre'))
			->with('i', ($request->input('page', 1) - 1) * $alertas_detalles->count());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		#dd($request->alertas_id);
		$alertas_id = $request->input('alertas_id');
		if (!$alertas_id) {
			return response()->json(['error' => 'El ID de la alerta es requerido'], 400);
		}
		#dd($alertas_id);	
		$funciones = Funcion::all()->keyBy('id'); // Indexar por ID para un acceso rápido
		$alertas_nombre = Alerta::where('id', $alertas_id)->first()['nombre'];

		// Obtener los detalles de las alertas
		$alertas_detalles = AlertaDetalle::where('alertas_id', $alertas_id)->get();
		if ($alertas_detalles->isEmpty()) {
			return response()->json(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
		}

		// Transformar los detalles
		$detalles = $alertas_detalles->flatMap(function ($detalle) use ($funciones) {
			$funcionesIds = explode(',', $detalle->funciones_id);
			$fechasDesde = explode(',', $detalle->fecha_desde);
			$fechasHasta = explode(',', $detalle->fecha_hasta);

			$rows = [];
			foreach ($funcionesIds as $index => $funcionId) {
				$nombreFuncion = $funciones[$funcionId]->nombre ?? null; // Obtener nombre de la función
				$rows[] = [
					'nombre_funcion' => $nombreFuncion,
					'alertas_id' => $detalle->alertas_id,
					'fecha_desde' => $fechasDesde[$index] ?? null,
					'fecha_hasta' => $fechasHasta[$index] ?? null,
					'created_at' => $detalle->created_at,
					'updated_at' => $detalle->updated_at,
				];
			}
			return $rows;
		})->toArray();

		#dd($detalles);


		$output = array(
			"recordsTotal" => count($detalles),
			"recordsFiltered" => count($detalles),
			"data" => $detalles
		);
		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function store(Request $request, MyController $myController): RedirectResponse
	{
		#dd($request);
		/*     $permiso_agregar_funciones = $myController->tiene_permiso('add_funcion');
			if (!$permiso_agregar_funciones) {
				abort(403, '.');
				return false;
			}
		*/
		$validatedData = $request->validate([
			'nombre' => [
				'required',
				'string',
				'max:255',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü\s]+$/'
			],
			'descripcion' => [
				'required',
				'string',
				'max:255',
				'regex:/^[a-zA-ZÁÉÍÓÚáéíóúÑñÜü0-9\s,.]+$/'
			],
			'tipos_alertas_id' => 'required|exists:tipos_alertas_id',
		], [
			'nombre.required' => 'El campo Nombre es obligatorio.',
			'nombre.regex' => 'El campo Nombre solo puede contener letras y espacios.',
			'descripcion.required' => 'El campo Descripción es obligatorio.',
			'descripcion.regex' => 'El campo Descripción solo permite letras, números, espacios, comas y puntos.',
			'tipo.required' => 'Debe seleccionar un Tipo de Alerta.',
			'tipo.exists' => 'El Tipo de Alerta seleccionado no es válido.',
		]);
		#dd($validatedData);
		Alerta::create($validatedData);

		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . "Creó el detalle de alerta \"$request->input('nombre')\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return Redirect::route('alertas.index')->with('success', 'alerta creada exitosamente.');
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_edit($id)
	{
		$data = AlertaDetalle::find($id);
		return response()->json($data);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function update(Request $request, AlertaDetalle $alerta_detalle, MyController $myController): RedirectResponse
	{
		/* 		$permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
				if (!$permiso_editar_funciones) {
					abort(403, '.');
					return false;
				}
		*/

		$form_data = $request->input('form_data');
		$form_data_array = collect($form_data)->pluck('value', 'name')->toArray();
		$alertas_id = $form_data_array['alertas_id'] ?? null;
		$alerta_detalle = DB::table('detalles_alertas')
			->where('id', $form_data_array['id'])
			->update([
				'alertas_id' => $form_data_array['alertas_id'],
				'funciones_id' => $form_data_array['funciones_id'],
				'fecha_desde' => $form_data_array['fecha_desde'],
				'fecha_hasta' => $form_data_array['fecha_hasta']
			]);

		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Actualizó el detalle de alerta \"$alerta_nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return redirect()->route('alertas_detalles', ['id' => $alertas_id])
			->with('success', 'Detalle de alerta actualizado correctamente.');
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
		$nombre = $alerta->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = "Eliminó la alerta \"$nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$alerta->delete();
		return response()->json(["status" => true]);
	}
}
