<?php

namespace App\Http\Controllers;

use App\Models\Rol;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

class RolController extends Controller
{
	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function index(Request $request, MyController $myController): View
	{
		$permiso_listar_roles = $myController->tiene_permiso('list_roles');
		if (!$permiso_listar_roles) {
			abort(403, '.');
			return false;
		}
		$roles = Rol::paginate();
		return view('rol.index', compact('roles'))
			->with('i', ($request->input('page', 1) - 1) * $roles->perPage());
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$roles = Rol::all();
		$data = array();
        foreach($roles as $r) {
            $accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_rol('."'".$r->rol_id.
				"'".')"><i class="bi bi-pencil-fill"></i></a>';

            if($r->id != 1){
                $accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_rol('."'".$r->rol_id.
					"'".')"><i class="bi bi-trash"></i></a>';
            }

            $data[] = array(
                $r->nombre,
                $accion
            );
        }
        $output = array(
            "recordsTotal" => $roles->count(),
            "recordsFiltered" => $roles->count(),
            "data" => $data
        );

		return response()->json($output);

	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_edit($id){

        $data = Rol::find($id);
		return response()->json($data);
    }

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController){
        $permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			return response()->json(["error"=>"No tienes permiso para realizar esta acción, contáctese con un administrador."], "405");
		}
		$rol = Rol::find($id);
		$nombre = $rol->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " borró el rol " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$rol->delete();
		return response()->json(["status"=>true]);
    }

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function create( MyController $myController): View
	{
		$permiso_agregar_roles = $myController->tiene_permiso('add_rol');
		if (!$permiso_agregar_roles) {
			abort(403, '.');
			return false;
		}
		$roles = new Rol();
		return view('rol.create', compact('roles'));
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
/* 	public function store(Request $request, MyController $myController): RedirectResponse
	{
		#dd($request);
		$permiso_agregar_roles = $myController->tiene_permiso('add_rol');
		if (!$permiso_agregar_roles) {
			abort(403, '.');
			return false;
		}
		$formData = [];
		foreach ($request->input('form_data') as $input) {
			$formData[$input['name']] = $input['value'];
		}
		// Validar los datos del usuario
		$validatedData = Validator::make($formData, [
			'nombre' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
				Rule::unique('roles'),
			],
		], [
			'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
		]);
		// Verifica si la validación falla
		if ($validatedData->fails()) {
			$response["message"] = 'Error en la validación de los datos.';
			$response["errors"] = $validatedData->errors();
			return response()->json($response);
		}
		$validatedData = $validatedData->validated();
		Rol::create($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $message);
		return Redirect::route('roles.index')
			->with('success', 'Rol creado exitosamente.');
	} */

	public function store(Request $request, MyController $myController): RedirectResponse
{
	#dd($request->input('nombre'));
    $permiso_agregar_roles = $myController->tiene_permiso('add_rol');
    if (!$permiso_agregar_roles) {
        abort(403, '.');
        return false;
    }

	$request->merge([
        'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
    ]);
    // Validar los datos del usuario
	$validatedData = $request->validate([
		'nombre' => [
			'required',
			'string',
			'max:255',
			'min:3',
			'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
			Rule::unique('roles'),
		],
	], [
		'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
		'nombre.unique' => 'Este nombre de rol ya está en uso.',
	]);

	$rolExistente = Rol::where('nombre', $request->input('nombre'))->first();
	if ($rolExistente) {
		return redirect()->back()->withErrors(['nombre' => 'Este nombre de rol ya está en uso.'])->withInput();
	}

    Rol::create($validatedData);

    $clientIP = \Request::ip();
    $userAgent = \Request::userAgent();
    $username = Auth::user()->username;
    $message = $username . " creó el rol " . $request->input('nombre');
    $myController->loguear($clientIP, $userAgent, $username, $message);

    return Redirect::route('roles.index')->with('success', 'Rol creado exitosamente.');
}
	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function show($id): View
	{
		$rol = Rol::find($id);
		return view('rol.show', compact('rol'));
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function edit($id, MyController $myController): View
	{
		#dd($id);
		$permiso_editar_roles = $myController->tiene_permiso('edit_rol');
		if (!$permiso_editar_roles) {
			abort(403, '.');
			return false;
		}
		$roles = Rol::find($id);
		#dd($roles);
		return view('rol.edit', compact('roles'));
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function update(Request $request, Rol $rol, MyController $myController): RedirectResponse
	{
		#dd($request);
		$permiso_editar_roles = $myController->tiene_permiso('edit_rol');
		if (!$permiso_editar_roles) {
			abort(403, '.');
			return false;
		}
		$request->merge([
			'nombre' => preg_replace('/\s+/', ' ', trim($request->input('nombre')))
		]);
		$messages = [
			'nombre.unique' => 'Este nombre de rol ya está en uso.',
			'nombre.required' => 'El nombre de rol es obligatorio.',
		];
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|unique:roles,nombre,' . $rol->rol_id . ',rol_id'
		], $messages);
		#dd($validatedData);
		$rol->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $message);
		return Redirect::route('roles.index')
			->with('success', 'Rol editado con éxito.');
	}

	/*******************************************************************************************************************************
	*******************************************************************************************************************************/
	public function destroy($id, MyController $myController): RedirectResponse
	{
		$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		}
		$rol = Rol::find($id);
		// Almacena el nombre de rol antes de eliminarlo
		$nombre = $rol->nombre;
		// Elimina el rol
		$rol->delete();
		$message = Auth::user()->username . " borró el rol " . $nombre;
		Log::info($message);
		return Redirect::route('roles.index')
			->with('success', 'Rol deleted successfully');
	}

}
