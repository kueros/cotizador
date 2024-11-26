<?php

namespace App\Http\Controllers;

use App\Models\AlertaDetalle;
use App\Models\Funcion;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AlertaDetalleController extends Controller
{
	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function index($id, Request $request, MyController $myController): View
	{
        $funciones = Funcion::all();
		$detalles_alertas = 
							AlertaDetalle::
								leftJoin('funciones', 'detalles_alertas.funciones_id', '=', 'funciones.id')->
								select( 'funciones.nombre as nombre_funcion', 'detalles_alertas.*')->
								where('alertas_id', $id)->
								orderBy('nombre', 'asc')->
								paginate();
		return view('alertas_detalles.index', compact('detalles_alertas', 'id', 'funciones'))
			->with('i', ($request->input('page', 1) - 1) * $detalles_alertas->perPage());
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$alertas_id = $request->input('alertas_id');

		$detalles_alertas = 
							AlertaDetalle::
								leftJoin('funciones', 'detalles_alertas.funciones_id', '=', 'funciones.id')->
								select( 'funciones.nombre as nombre_funcion', 'detalles_alertas.*')->
								where('alertas_id', $alertas_id)->
								orderBy('nombre', 'asc')->
								get();
		#dd($detalles_alertas);
		$data = array();
		foreach($detalles_alertas as $r) {
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas_detalles('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alertas_detalles('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';

			$data[] = array(
				$r->nombre_funcion,
				$r->fecha_desde,
				$r->fecha_hasta,
				$accion
			);
		}
		$output = array(
			"recordsTotal" => $detalles_alertas->count(),
			"recordsFiltered" => $detalles_alertas->count(),
			"data" => $data
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
		dd($request->input('alertas_id'));
/* 		$permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
		if (!$permiso_editar_funciones) {
			abort(403, '.');
			return false;
		}
*/

		// Obtener el modelo
		$alerta_detalle = AlertaDetalle::findOrFail($request->id);

		$alertas_id = $request->input('alertas_id');
		#dd($alerta_detalle_id);
		// Actualizar el modelo con los datos validados
		$alerta_detalle->update($request->all());
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el detalel de alerta " . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return redirect()->route('alertas_detalles', ['id' => $alertas_id])
		->with('success', 'Campo adicional de tipo de transacción actualizado correctamente.');

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
