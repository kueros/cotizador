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

class RolController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
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

	public function ajax_listado(Request $request)
	{
		$roles = Rol::all();
		$data = array();
        foreach($roles as $r) {
            $accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_rol('."'".$r->rol_id.
				"'".')"><i class="bi bi-pencil"></i></a>';

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

	public function ajax_edit($id){

        $data = Rol::find($id);
		return response()->json($data);
    }

	public function ajax_delete($id, MyController $myController){
        $permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
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

	/**
	 * Show the form for creating a new resource.
	 */
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

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, MyController $myController): RedirectResponse
	{
		$permiso_agregar_roles = $myController->tiene_permiso('add_rol');
		if (!$permiso_agregar_roles) {
			abort(403, '.');
			return false;
		}
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|unique:roles,nombre',
		]);
		Rol::create($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $message);
		return Redirect::route('roles.index')
			->with('success', 'Rol created successfully.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id): View
	{
		$rol = Rol::find($id);
		return view('rol.show', compact('rol'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
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

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Rol $rol, MyController $myController): RedirectResponse
	{
		#dd($request);
		$permiso_editar_roles = $myController->tiene_permiso('edit_rol');
		if (!$permiso_editar_roles) {
			abort(403, '.');
			return false;
		}
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|unique:roles,nombre',
		]);
		#dd($validatedData);
		$rol->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " actualizó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $message);
		return Redirect::route('roles.index')
			->with('success', 'Rol updated successfully');
	}

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

	/*public function opciones_submit(){
        
    }*/
}
