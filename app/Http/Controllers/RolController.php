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
	public function index(Request $request): View
	{
		$roles = Rol::paginate();
		return view('rol.index', compact('roles'))
			->with('i', ($request->input('page', 1) - 1) * $roles->perPage());
	}

	public function ajax_listado(Request $request): View
	{
		/*$roles = Rol::paginate();
		return view('rol.index', compact('roles'))
			->with('i', ($request->input('page', 1) - 1) * $roles->perPage());*/

		$roles = Rol::paginate();

		// Realiza las búsquedas, ordenamiento y paginación que DataTables envía en la solicitud.
		/*if ($request->has('search.value')) {
			$query->where('columna1', 'like', '%' . $request->input('search.value') . '%');
		}*/
		
		$totalData = $roles->count();
		
		// Paginación y ordenamiento
		$roles->skip($request->input('start'))->take($request->input('length'));
		
		/*if ($request->has('order.0.column')) {
			$columns = ['columna1', 'columna2', 'columna3']; // Ajusta a las columnas reales
			$orderColumn = $columns[$request->input('order.0.column')];
			$query->orderBy($orderColumn, $request->input('order.0.dir'));
		}*/
		
		// Obtener los datos con paginación
		$data = $roles->get();
	
		// Respuesta en formato JSON
		return response()->json([
			'draw' => $request->input('draw'),
			'recordsTotal' => $totalData,
			'recordsFiltered' => $totalData,
			'data' => $data,
		]);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		$roles = new Rol();
		return view('rol.create', compact('roles'));
	}

	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, MyController $myController): RedirectResponse
	{
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|unique:roles,nombre',
		]);
		Rol::create($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$action = "roles.store";
		$message = $username . " creó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $action, $message);
		$subject = "Creación de rol";
		$body = "Rol ". $_POST['nombre'] . " creado correctamente por ". Auth::user()->username;
		$to = Auth::user()->email;
		// Llamar a enviar_email de MyController
		$myController->enviar_email($to, $body, $subject);
		Log::info('Correo enviado exitosamente a ' . $to);
		return Redirect::route('roles.index')
			->with('success', 'Rol created successfully.');
	}

	/**
	 * Display the specified resource.
	 */
	public function show($id): View
	{
		$role = Rol::find($id);
		return view('rol.show', compact('role'));
	}

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id): View
	{
		$roles = Rol::find($id);
		return view('rol.edit', compact('roles'));
	}

	/**
	 * Update the specified resource in storage.
	 */
	public function update(Request $request, Rol $rol, MyController $myController): RedirectResponse
	{
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre' => 'required|string|max:255|unique:roles,nombre',
		]);
		$rol->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$action = "roles.update";
		$message = $username . " actualizó el rol " . $_POST['nombre'];
		$myController->loguear($clientIP, $userAgent, $username, $action, $message);
		$subject = "Actualización de rol";
		$body = "Rol ". $_POST['nombre'] . " actualizado correctamente por ". Auth::user()->username;
		$to = Auth::user()->email;
		// Llamar a enviar_email de MyController
		$myController->enviar_email($to, $body, $subject);
		Log::info('Correo enviado exitosamente a ' . $to);
		return Redirect::route('roles.index')
			->with('success', 'Rol updated successfully');
	}

	public function destroy($id, MyController $myController): RedirectResponse
	{
		$rol = Rol::find($id);
		// Almacena el nombre de rol antes de eliminarlo
		$nombre = $rol->nombre;
		// Elimina el rol
		$rol->delete();
		$message = Auth::user()->username . " borró el rol " . $nombre;
		Log::info($message);
		$subject = "Borrado de rol";
		$body = "Rol " . $nombre . " borrado correctamente por " . Auth::user()->username;
		$to = "omarliberatto@yafoconsultora.com";
		// Llamar a enviar_email de MyController
		$myController->enviar_email($to, $body, $subject);
		Log::info('Correo enviado exitosamente a ' . $to);
		return Redirect::route('roles.index')
			->with('success', 'Rol deleted successfully');
	}
}
