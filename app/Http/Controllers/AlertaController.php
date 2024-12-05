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
            Alerta::
					leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')->
					leftJoin('alertas_tipos_tratamientos', 'alertas.tipos_tratamientos_id', '=', 'alertas_tipos_tratamientos.id')->
					select( 'tipos_alertas.nombre', 'alertas_tipos_tratamientos.nombre', 'alertas.*')->
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
			Alerta::
					leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')->
					leftJoin('alertas_tipos_tratamientos', 'alertas.tipos_tratamientos_id', '=', 'alertas_tipos_tratamientos.id')->
					select( 'tipos_alertas.nombre as tipo_alerta', 'alertas_tipos_tratamientos.nombre as tipo_tratamiento', 'alertas.*')->
					orderBy('nombre', 'asc')->
			        get();
		$data = array();
		#dd($alertas);
        foreach($alertas as $r) {
			$detalleAlerta = route('alertas_detalles', ['id' => $r->id]); 
			$accion = '<a class="btn btn-sm btn-info" href="' . $detalleAlerta . '" title="Detalle Alerta">Detalle Alerta</a>';

            $accion .= '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alerta('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';

            $data[] = array(
                $r->nombre,
                $r->descripcion,
                $r->tipo_alerta,
				$r->tipo_tratamiento,
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
		// Limpia el campo nombre
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
		// Validar los datos
		$validatedData = Validator::make($request->all(), [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[a-zA-Z\s]+$/', // Solo letras sin acentos y espacios
				Rule::unique('alertas', 'nombre'), // Asegura la unicidad
			],
			'descripcion' => 'required|string|max:255',
			'tipos_alertas_id' => 'required|integer|exists:tipos_alertas,id',
			'tipos_tratamientos_id' => 'required|integer|exists:alertas_tipos_tratamientos,id',
			'funciones_id' => 'required|exists:funciones,id',
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre  ya está en uso.',
			'tipos_alertas_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_alertas_id.exists' => 'El tipo de campo seleccionado no es válido.',
			'tipos_tratamientos_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_tratamientos_id.exists' => 'El tipo de campo seleccionado no es válido.',
		]);
		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => '',
				'errors' => $validatedData->errors()
			]);
		}
		
		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		#$inserted_id = Alerta::create($validated);

		$alerta = Alerta::create([
			'nombre' => $validated['nombre'],
			'descripcion' => $validated['descripcion'],
			'tipos_alertas_id' => $validated['tipos_alertas_id'],
			'tipos_tratamientos_id' => $validated['tipos_tratamientos_id'],
		]);
	#dd($alerta->id);
	AlertaDetalle::create([
		'alertas_id' => $alerta->id,
		'funciones_id' => implode(',', $validated['funciones_id']), // Convertir array a cadena separada por comas
		'fecha_desde' => implode(',', array_map(function ($fecha) {
			return \Carbon\Carbon::parse($fecha)->format('d-m-Y');
		}, $request['fecha_desde'])),
		'fecha_hasta' => implode(',', array_map(function ($fecha) {
			return \Carbon\Carbon::parse($fecha)->format('d-m-Y');
		}, $request['fecha_hasta'])),
	]);

		// Loguear la acción
		$clientIP = \Request::ip();
		$clientIP_sc = str_replace('"', '', $clientIP);
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "Creó el alerta \"$validated[nombre]\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);
		#dd($clientIP_sc);
		$response = [
		'status' => 0,
		'message' => $validatedData->errors()
		];
	// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Alerta creada correctamente.'
		];
		#dd($response);
		return response()->json($response);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){
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
		// Validación de los datos
		$validatedData = Validator::make($request->all(), [
			'nombre' => 'required|string|max:255|min:3|regex:/^[a-zA-Z\s]+$/',
			Rule::unique('alertas', 'nombre'),
			'descripcion' => 'required|string|max:255',
			'tipos_alertas_id' => 'required|integer|exists:tipos_alertas,id',
			'tipos_tratamientos_id' => 'required|integer|exists:alertas_tipos_tratamientos,id',
			'funciones_id' => 'required|array',
			'funciones_id.*' => 'exists:funciones,id',
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre de alerta ya está en uso.',
			'tipos_alertas_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_alertas_id.exists' => 'El tipo de campo seleccionado no es válido.',
			'tipos_tratamientos_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_tratamientos_id.exists' => 'El tipo de campo seleccionado no es válido.',
		]);
	
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => 'Error en validación',
				'errors' => $validatedData->errors()
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
	
		// Construir el mensaje de cambios
		$cambios = [];
		foreach (['nombre', 'descripcion', 'tipos_alertas_id', 'tipos_tratamientos_id'] as $campo) {
			if ($alertaExistente->$campo != $validated[$campo]) {
				$cambios[] = "cambiando $campo de \"{$alertaExistente->$campo}\" a \"{$validated[$campo]}\"";
			}
		}
	
		$mensajeCambios = implode(', ', $cambios);
		$username = Auth::user()->username;
		$message = "Actualizó el alerta \"{$alertaExistente->nombre}\" $mensajeCambios.";
	
		// Actualizar los datos
		$alertaExistente->update($validated);
	
		DB::table('detalles_alertas')
			->updateOrInsert(
				['alertas_id' => $alertaExistente->id],
				[
					'funciones_id' => implode(',', $validated['funciones_id']),
					'fecha_desde' => implode(',', array_map(function ($fecha) {
						return \Carbon\Carbon::parse($fecha)->format('d-m-Y');
					}, $request['fecha_desde'])),
					'fecha_hasta' => implode(',', array_map(function ($fecha) {
						return \Carbon\Carbon::parse($fecha)->format('d-m-Y');
					}, $request['fecha_hasta'])),
				]
			);
	
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
	public function ajax_delete($id, MyController $myController){
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
		$message = "Eliminó el alerta \"$alerta->nombre\"";
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$alertas_detalles->delete();
		$alerta->delete();
		return response()->json(["status"=>true]);
    }
}
