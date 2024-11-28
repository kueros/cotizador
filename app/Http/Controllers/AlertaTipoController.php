<?php

namespace App\Http\Controllers;

use App\Models\Alerta;
use App\Models\TipoAlerta;
use App\Models\AlertaDetalle;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class AlertaTipoController extends Controller
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
        $tipos_alertas = TipoAlerta::paginate();

        return view('alertas_tipos.index', compact('tipos_alertas'))
			->with('i', ($request->input('page', 1) - 1) * $tipos_alertas->perPage());
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
        $tipos_alertas = TipoAlerta::all();
		$data = array();
        foreach($tipos_alertas as $r) {
#dd($r);
			$accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_alertas_tipos('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_alertas_tipos('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';

            $data[] = array(
                $r->nombre,
                $accion
            );
        }
        $output = array(
            "recordsTotal" => $tipos_alertas->count(),
            "recordsFiltered" => $tipos_alertas->count(),
            "data" => $data
        );
 
		return response()->json($output);
    }

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_store(Request $request, MyController $myController)
	{
		#dd($request->all());
		// Validar los datos del usuario
		$validatedData = Validator::make($request->all(), [
			'nombre' => 'required|string|max:255|min:3|regex:/^[a-zA-Z\s]+$/', // Solo letras sin acentos y espacios
				Rule::unique('alertas_tipos', 'nombre'),
			], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios, no acepta caracteres acentuados ni símbolos especiales.',
			'nombre.unique' => 'Este nombre de alerta ya está en uso.',
		]);
		#dd($validatedData);
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
		#$inserted_id = Alerta::create($validated);

		$alerta = TipoAlerta::create([
			'nombre' => $validated['nombre'],
		]);
		// Loguear la acción
		$clientIP = $request->ip();
		$userAgent = $request->userAgent();
		$username = Auth::user()->username;
		$message = "$username creó un nuevo tipo de alerta: $validated[nombre]";
		$myController->loguear($clientIP, $userAgent, $username, $message);
	
			$response = [
			'status' => 0,
			'message' => $validatedData->errors()
		];
	// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Tipo de Alerta creado correctamente.'
		];
		return response()->json($response);
	}


	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){

        $data = TipoAlerta::find($id);
		return response()->json($data);
    }

    /*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function update(Request $request, TipoAlerta $alertas_tipos, MyController $myController): RedirectResponse
	{
		#dd($request->id);
/* 		$permiso_editar_funciones = $myController->tiene_permiso('edit_funcion');
		if (!$permiso_editar_funciones) {
			abort(403, '.');
			return false;
		}
*/
		// Validar los datos


		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255',
		]);

		// Obtener el modelo
		$alertas_tipos = TipoAlerta::findOrFail($request->id);

		// Actualizar el modelo con los datos validados
		$alertas_tipos->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el tipo de alerta " . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return redirect()->route('alertas_tipos')->with('success', 'Tipo de Alerta actualizado correctamente.');

	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController){
/*         $permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			return response()->json(["error"=>"No tienes permiso para realizar esta acción, contáctese con un administrador."], "405");
		}
*/
		$alerta_tipo = TipoAlerta::find($id);
		$nombre = $alerta_tipo->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " borró el tipo de alerta " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$alerta_tipo->delete();
		return response()->json(["status"=>true]);
    }
}
