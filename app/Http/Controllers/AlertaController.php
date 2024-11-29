<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\TipoAlerta;
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
        $alertas = 
            Alerta::
                leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')->
                select( 'alertas.nombre', 'alertas.*')->
                #where('tipo_transaccion_id', $tipo_transaccion_id)->
                #orderBy('nombre', 'asc')->
                paginate();
        return view('alertas.index', compact('alertas', 'tipos_alertas', 'funciones'))
			->with('i', ($request->input('page', 1) - 1) * $alertas->perPage());
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$alertas = Alerta::leftJoin('tipos_alertas', 'alertas.tipos_alertas_id', '=', 'tipos_alertas.id')
        ->select('tipos_alertas.nombre as tipo_nombre', 'alertas.*')
        #->where('tipo_transaccion_id', $tipo_transaccion_id)
        ->orderBy('nombre', 'asc')
        ->get();
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
                $r->tipo_nombre,
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
			'funciones_id' => 'required|exists:funciones,id',
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre de alerta ya está en uso.',
			'tipos_alertas_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_alertas_id.exists' => 'El tipo de campo seleccionado no es válido.',
		]);
		// Si la validación falla
		if ($validatedData->fails()) {
			return response()->json([
				'status' => 0,
				'message' => 'Error de Ingreso de Datos',
				'errors' => $validatedData->errors()
			]);
		}
		
		$validated = $validatedData->validated(); // Obtiene los datos validados como array
		#$inserted_id = Alerta::create($validated);

		$alerta = Alerta::create([
			'nombre' => $validated['nombre'],
			'descripcion' => $validated['descripcion'],
			'tipos_alertas_id' => $validated['tipos_alertas_id'],
		]);
	#dd($alerta->id);
		AlertaDetalle::create([
			'alertas_id' => $alerta->id,
			'funciones_id' => implode(',', $validated['funciones_id']), // Convertir array a cadena separada por comas
			'fecha_desde' => implode(',', $request['fecha_desde']),
			'fecha_hasta' => implode(',', $request['fecha_hasta']),
		]);

		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "$username creó una nueva alerta: $validated[nombre]";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	#dd($validatedData->errors());
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
		]);
		return $response;
	}


    /*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function alertasUpdate(Request $request, Alerta $alerta, MyController $myController)
	{
		#dd(request()->all());
		/* $permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
		if (!$permiso_editar_funciones) {
			abort(403, '.');
			return false;
		} */
	
	
		// Validación de los datos
		#$validatedData = $request->validate([
		$validatedData = Validator::make($request->all(), [
			'nombre' => 'required|string|max:255|min:3|regex:/^[a-zA-Z\s]+$/', // Solo letras sin acentos y espacios
			Rule::unique('alertas', 'nombre'),
			'descripcion' => 'required|string|max:255',
			'tipos_alertas_id' => 'required|integer|exists:tipos_alertas,id',
			'funciones_id' => 'required|exists:funciones,id',
			], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre de alerta ya está en uso.',
			'tipos_alertas_id.required' => 'Este campo no puede quedar vacío.',
			'tipos_alertas_id.exists' => 'El tipo de campo seleccionado no es válido.',
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
		#dd($request->alertas_id);
		// Obtener el modelo
		$alerta_id = Alerta::where('id',$request->alertas_id)->first()['id'];
		$updated_id = DB::table('alertas')
		->where('id', $alerta_id)
		->update
		([
			'nombre' => $validated['nombre'],
			'descripcion' => $validated['descripcion'],
			'tipos_alertas_id' => $validated['tipos_alertas_id'],
		]);

/* 		$alerta = Alerta::update([
			'nombre' => $validatedData['nombre'],
			'descripcion' => $validatedData['descripcion'],
			'tipos_alertas_id' => $validatedData['tipos_alertas_id'],
		]);
 */#	dd($validated['funciones_id']);
		#dd($request->alertas_id[0]);

		DB::table('detalles_alertas')
		->updateOrInsert(
			['alertas_id' => $request->alertas_id],
			['funciones_id' => implode(',', $validated['funciones_id']),
			'fecha_desde' => $request['fecha_desde'][0] ?? null,
			'fecha_hasta' => $request['fecha_hasta'][0] ?? null
		]);

/* 		$alerta_detalle_id = AlertaDetalle::where('alertas_id',$request->alertas_id)->first()['id'];
		$alertas_id = AlertaDetalle::where('alertas_id',$request->alertas_id)->first()['alertas_id'];
		#dd($alertas_id);
		$updated_id = DB::table('detalles_alertas')
		->where('id', $alerta_detalle_id)
		->update
		([
			'alertas_id' => $alertas_id,
			'funciones_id' => implode(',', $validated['funciones_id']), // Convertir array a cadena separada por comas
			'fecha_desde' => $request['fecha_desde'][0] ?? null, // Usar el primer valor del arreglo, o null si no existe
			'fecha_hasta' => $request['fecha_hasta'][0] ?? null, // Usar el primer valor del arreglo, o null si no existe
		]);
 */




		// Loguear la acción
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el alerta " . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
		#return redirect()->route('alertasIndex')->with('success', 'alerta actualizada correctamente.');
		$response = [
			'status' => 1,
			'message' => 'Alerta actualizada correctamente.'
		];
		return response()->json($response);

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
