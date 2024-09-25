<?php

namespace App\Http\Controllers;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use App\Http\Requests\UserRequest;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\LogAdministracion;
use App\Models\Rol;
use App\Http\Controllers\MyController;
use Illuminate\Support\Facades\DB;
use Exception;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
	public function index(Request $request): View
	{
		#$users = User::paginate();

		$users = User::withoutTrashed()
		->leftJoin('roles', 'users.rol_id', '=', 'roles.id')
		->select('users.*', 'roles.nombre as nombre_rol')
		->paginate();
	
		return view('user.index', compact('users'))
			->with('i', ($request->input('page', 1) - 1) * $users->perPage());
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		$roles = Rol::all();
		$user = new User();
		return view('user.create', compact('user', 'roles'));
	}


	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, MyController $myController): RedirectResponse
	{
		// Validar los datos del usuario
		$validatedData = $request->validate([
			'username' => 'required|string|max:255|unique:users,username',
			'nombre' => 'required|string|max:255',
			'apellido' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users,email',
			'rol_id' => 'required|exists:roles,id',
			'habilitado' => 'required|boolean',
		]);

		// Crear el usuario con los datos validados
		$user = User::create($validatedData);
		
		#$user = User::create($_POST);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		#dd($userAgent);
		$message = Auth::user()->username . " creó el usuario " . $_POST['username'];
		Log::info($message);
		$log = LogAdministracion::create([
			'username' => Auth::user()->username,
			'action' => "users.store",
			'detalle' => $message,
			'ip_address' => json_encode($clientIP),
			'user_agent' => json_encode($userAgent)
		]);
		$log->save();

		/* 		$notificacion = Notificacion::create([
			'user_id' => Auth::user()->id,
			'mensaje' => $message,
			'estado' => 1,
			'user_emisor_id' => Auth::user()->id,
			'asunto' => "Creación de usuario"
		]);
		$notificacion->save();
		*/
		$subject = "Creación de usuario";
		$body = "Usuario ". $_POST['username'] . " creado correctamente por ". Auth::user()->username;
		$to = "omarliberatto@yafoconsultora.com";

		try {
			// Llamar a enviar_email de MyController
			$myController->enviar_email($to, $body, $subject);
			Log::info('Correo enviado exitosamente a ' . $to);
		} catch (Exception $e) {
			// Manejo de la excepción
			Log::error('Error al enviar el correo: ' . $e->getMessage());

			// Puedes redirigir al usuario con un mensaje de error
			return redirect('/users')->with('error', 'Hubo un problema al enviar el correo. Por favor, intenta nuevamente.');
		}

		return Redirect::route('users.index')
		->with('success', 'Usuario creado correctamente.');
	}

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $user = User::find($id);
    return view('user.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
		$roles = Rol::all();
        $users = User::find($id);
        return view('user.edit', compact('users', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
	public function update(UserRequest $request, User $user, MyController $myController): RedirectResponse
	{
		#dd($request->all());
		// Definir los mensajes de error personalizados
		$messages = [
			'username.unique' => 'El nombre de usuario ya está en uso por otro usuario.',
			'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
			'rol_id.required' => 'El rol es obligatorio.',
			'rol_id.exists' => 'El rol seleccionado no es válido.',
		];
		// Validar los datos del usuario, ignorando al usuario actual
		$validatedData = $request->validate([
			'username' => 'required|string|max:255|unique:users,username,' . $user->id,
			'nombre' => 'required|string|max:255',
			'apellido' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
			'rol_id' => 'required|exists:roles,id',
		], $messages);
		#dd('asdf1234');

		// Actualizar el usuario con los datos validados
		$user->update($validatedData);
		// Encuentra el usuario por su ID
		$user = User::find($user->id);

		// Si el usuario no existe, redirige con un mensaje de error
		if (!$user) {
			return redirect('/users')->with('error', 'El usuario no existe.');
		}

		// Almacena el nombre de usuario antes de eliminarlo
		$username = $user->username;
		$message = Auth::user()->username . " actualizó el usuario " . $username;
		Log::info($message);
		$subject = "Actualización de usuario";
		$body = "Usuario " . $username . " actualizado correctamente por " . Auth::user()->username;
		$to = "omarliberatto@yafoconsultora.com";

		try {
			// Llamar a enviar_email de MyController
			$myController->enviar_email(
				$to,
				$body,
				$subject
			);
			Log::info('Correo enviado exitosamente a ' . $to);
		} catch (Exception $e) {
			// Manejo de la excepción
			Log::error('Error al enviar el correo: ' . $e->getMessage());
			return redirect('/users')->with('error', 'Hubo un problema al enviar el correo. Por favor, intenta nuevamente.');
		}
		#return Redirect::route('users.index')
		#	->with('success', 'Usuario actualizado correctamente');
		return redirect()->route('users.index')->with('status', 'profile-updated');
	}

	
	public function destroy($id, MyController $myController): RedirectResponse
	{
		// Encuentra el usuario por su ID
		$user = User::withTrashed()->find($id);

		// Si el usuario no existe, redirige con un mensaje de error
		if (!$user) {
			return redirect('/users')->with('error', 'El usuario no existe.');
		}

		// Almacena el nombre de usuario antes de eliminarlo
		$username = $user->username;
		// Elimina el usuario
		$user->delete();

		$message = Auth::user()->username . " borró el usuario " . $username;
		Log::info($message);

		$subject = "Borrado de usuario";
		$body = "Usuario " . $username . " borrado correctamente por " . Auth::user()->username;
		$to = "omarliberatto@yafoconsultora.com";

		try {
			// Llamar a enviar_email de MyController
			$myController->enviar_email($to, $body, $subject);
			Log::info('Correo enviado exitosamente a ' . $to);
		} catch (Exception $e) {
			// Manejo de la excepción
			Log::error('Error al enviar el correo: ' . $e->getMessage());

			// Puedes redirigir al usuario con un mensaje de error
			return redirect('/users')->with('error', 'Hubo un problema al enviar el correo. Por favor, intenta nuevamente.');
		}

		return Redirect::route('users.index')
		->with('success', 'Usuario eliminado correctamente.');
	}
}
