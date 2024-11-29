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
        foreach($funciones as $r) {
            $accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_funcion('."'".$r->id.
					"'".')"><i class="bi bi-pencil-fill"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_funcion('."'".$r->id.
					"'".')"><i class="bi bi-trash"></i></a>';
			$r->formula = str_replace(",", "", $r->formula);

			$data[] = array(
                $r->nombre,
                $r->formula,
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
		#dd($request->all());
		#dd($request->input('nombre'));
	/*     $permiso_agregar_funciones = $myController->tiene_permiso('add_funcion');
		if (!$permiso_agregar_funciones) {
			abort(403, '.');
			return false;
		}
	*/

	// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|regex:/^[a-zA-Z\s]+$/|unique:funciones,nombre',
			'formula' => 'required', 
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
			'nombre.unique' => 'Este nombre de funcion ya está en uso.',
			'nombre.required' => 'El nombre de la funcion es requerido.',
			'formula.required' => 'La formula de la funcion es requerida.',
		]);
#dd($validatedData);
		$campo = funcion::create($validatedData);
		$campo->formula = $validatedData['formula'];
		$campo->save();

		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó la funcion " . $request->input('nombre');
		$myController->loguear($clientIP, $userAgent, $username, $message);

		// Respuesta exitosa
		$response = [
			'status' => 1,
			'message' => 'Función creada correctamente.'
		];
		#dd($response);
		return response()->json($response);
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){
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
	public function funcionesupdate(Request $request, Funcion $funcion, MyController $myController): RedirectResponse
	{
		#dd($request->all());
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
		$funcion = Funcion::findOrFail($request->id);

		// Actualizar el modelo con los datos validados
		$funcion->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó la función " . $request->nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return redirect()->route('funciones.index')->with('success', 'Función actualizado correctamente.');

		#return Redirect::route('funciones.index')
		#	->with('success', 'Función editada con éxito.');
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController){
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
		$message = $username . " borró la función " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$funcion->delete();
		return response()->json(["status"=>true]);
    }



}
