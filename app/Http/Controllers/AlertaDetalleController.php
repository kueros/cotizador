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
        $funciones = Funcion::all();
		$alertas_nombre = Alerta::where('id', $id)->first()['nombre'];
		$detalles_alertas = 
							AlertaDetalle::
								leftJoin('funciones', 'detalles_alertas.funciones_id', '=', 'funciones.id')->
								select( 'funciones.nombre as nombre_funcion', 'detalles_alertas.*')->
								where('alertas_id', $id)->
								orderBy('nombre', 'asc')->
								paginate();
		#dd($detalles_alertas);
		return view('alertas_detalles.index', compact('detalles_alertas', 'id', 'funciones', 'alertas_nombre'))
			->with('i', ($request->input('page', 1) - 1) * $detalles_alertas->perPage());
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		#dd($request);
		$alertas_id = $request->input('alertas_id');
		$detalles_alertas = 
							AlertaDetalle::
								leftJoin('funciones', 'detalles_alertas.funciones_id', '=', 'funciones.id')->
								select( 'funciones.nombre as nombre_funcion', 'detalles_alertas.*')->
								where('alertas_id', $alertas_id)->
								orderBy('nombre', 'asc')->
								get();
		#dd($detalles_alertas['nombre_funcion']);
		#dd($detalles_alertas);


		$datos = $detalles_alertas->map(function ($detalle) {
			return [
  				'funciones_id' => explode(',', $detalle->funciones_id),
				'fecha_desde' => explode(',', $detalle->fecha_desde),
				'fecha_hasta' => explode(',', $detalle->fecha_hasta),
			];
		})->toArray();
		
		
		
		$detalles = $detalles_alertas->map(function ($detalle) {
			// Separar los valores de los campos con comas
			$funciones = explode(',', $detalle->funciones_id);
			$fechasDesde = explode(',', $detalle->fecha_desde);
			$fechasHasta = explode(',', $detalle->fecha_hasta);
		
			// Construir filas para cada elemento
			$rows = [];
			for ($i = 0; $i < count($funciones); $i++) {
				$rows[] = [
					$detalle->nombre_funcion, // Nombre de la función
					$fechasDesde[$i] ?? null, // Fecha desde
					$fechasHasta[$i] ?? null, // Fecha hasta
				];
			}
			return $rows;
		})->flatten(1)->toArray();
		
/* 		// Resultado final
		return view('tu.vista', compact('detalles'));		
 */		
		
		
		#dd($detalles);

/* 
		$data = array();
		for ($i = 0; $i < count($funciones); $i++) {
			$accion = "";
			#$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas_detalles('."'".$r->id.
			#		"'".')"><i class="bi bi-pencil-fill"></i></a>';

			#$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alertas_detalles('."'".$r->id.
			#		"'".')"><i class="bi bi-trash"></i></a>';
			$data[] = [
				'funcion_id' => $funciones[$i],
				'fecha_desde' => $fechasDesde[$i],
				'fecha_hasta' => $fechasHasta[$i],
			];
		}
 */




		$output = array(
			"recordsTotal" => $detalles_alertas->count(),
			"recordsFiltered" => $detalles_alertas->count(),
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
		dd($validatedData);
		Alerta::create($validatedData);
		
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el alerta " . $request->input('nombre');
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return Redirect::route('alertas.index')->with('success', 'alerta creada exitosamente.');
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){
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
              ->update(['alertas_id' => $form_data_array['alertas_id'],
			  			'funciones_id' => $form_data_array['funciones_id'],
						'fecha_desde' => $form_data_array['fecha_desde'],
						'fecha_hasta' => $form_data_array['fecha_hasta']
					]);

		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el detalle de alerta ";// . $alerta_nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return redirect()->route('alertas_detalles', ['id' => $alertas_id])
		->with('success', 'Detalle de alerta actualizado correctamente.');

	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController){
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
		$message = $username . " borró el alerta " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$alerta->delete();
		return response()->json(["status"=>true]);
	}
}
