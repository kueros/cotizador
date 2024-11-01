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
use App\Models\Rol;
use App\Http\Controllers\MyController;
use App\Models\Permiso_x_Rol;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Password;
use illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\Variable;
use Illuminate\Validation\Rule;

class UserController extends Controller
{

	/**************************************************************************
	*
	**************************************************************************/
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request, MyController $myController): View
	{
		$permiso_listar_usuarios = $myController->tiene_permiso('list_usr');
		$permiso_agregar_usuario = $myController->tiene_permiso('add_usr');
		$permiso_editar_usuario = $myController->tiene_permiso('edit_usr');
		$permiso_eliminar_usuario = $myController->tiene_permiso('del_usr');
		$permiso_deshabilitar_usuario = $myController->tiene_permiso('enable_usr');
		if (!$permiso_listar_usuarios) {
			abort(403, '.');
			return false;
		}
		$reset_password_30_dias = Variable::where('nombre', 'reset_password_30_dias')
			->first()['valor'];
		$configurar_claves = Variable::where('nombre', 'configurar_claves')
			->first()['valor'];
			#dd($variables);
		$users = User::withoutTrashed()
			->leftJoin('roles', 'users.rol_id', '=', 'roles.rol_id')
			->select('users.*', 'roles.nombre as nombre_rol')
			->paginate();
		$roles = Rol::all();
		return view('user.index', compact('users', 'permiso_agregar_usuario', 
			'permiso_editar_usuario', 'permiso_eliminar_usuario', 'permiso_deshabilitar_usuario', 
			'reset_password_30_dias', 'configurar_claves', 'roles'))
			->with('i', ($request->input('page', 1) - 1) * $users->perPage());
	}

	/**************************************************************************
	*
	**************************************************************************/
	/**
	 * Show the form for creating a new resource.
	 */
	public function create(MyController $myController): View
	{
		$permiso_agregar_usuario = $myController->tiene_permiso('add_usr');
		if (!$permiso_agregar_usuario) {
			abort(403, '.');
			return false;
		}
		$roles = Rol::all();
		$user = new User();
		return view('user.create', compact('user', 'roles'));
	}


	/**************************************************************************
	*
	**************************************************************************/
	/**
	 * Store a newly created resource in storage.
	 */
	public function store(Request $request, MyController $myController): RedirectResponse
	{
		#dd($request);
		$permiso_agregar_usuario = $myController->tiene_permiso('add_usr');
		if (!$permiso_agregar_usuario) {
			abort(403, '.');
			return false;
		}
		$validatedData = $request->validate([
			'username' => [
				'required',
				'string',
				'max:255',
				Rule::unique('users')->whereNull('deleted_at'), // Verifica unicidad sin registros eliminados
			],
			'nombre' => 'required|string|max:255',
			'apellido' => 'required|string|max:255',
			'email' => [
				'required',
				'string',
				'email',
				'max:255',
				Rule::unique('users')->whereNull('deleted_at'), // Verifica unicidad sin registros eliminados
			],
			'rol_id' => 'required|exists:roles,rol_id',
			'habilitado' => 'required|boolean',
		]);
		// Verificar si el usuario con el mismo username o email está soft deleted
		$existingUser = User::onlyTrashed()
			->where(function ($query) use ($validatedData) {
				$query->where('username', $validatedData['username'])
					->orWhere('email', $validatedData['email']);
			})
			->first();
		#dd($existingUser);
		if ($existingUser) {
			// Restaurar el usuario si está soft deleted
			$existingUser->restore();
			$existingUser->ultima_fecha_restablecimiento = now(); // Establecer fecha actual
			$existingUser->save();

			// Actualizar los datos del usuario restaurado con los nuevos valores
			$existingUser->update($validatedData);

			$clientIP = \Request::ip();
			$userAgent = \Request::userAgent();
			$username = Auth::user()->username;
			$action = "users.restore";
			$message = $username . " restauró el usuario " . $existingUser->username;
			$myController->loguear($clientIP, $userAgent, $username, $action, $message);

			$subject = "Restauración de usuario";
			$body = "Usuario ". $existingUser->username . " restaurado correctamente por ". Auth::user()->username;
			$to = Auth::user()->email;
			$myController->enviar_email($to, $body, $subject);

			Log::info('Correo enviado exitosamente a ' . $to);
			return redirect()->route('users.index')->with('success', 'Usuario restaurado correctamente.');
		} else {

			// Si no existe un usuario soft deleted, crear uno nuevo
			#$validatedData['ultima_fecha_restablecimiento'] = Carbon::now(); // Añadir fecha actual
			#dd($validatedData);
			$user = User::create($validatedData);
			$user->ultima_fecha_restablecimiento = now(); // Establecer fecha actual
			$user->save();

			$clientIP = \Request::ip();
			$userAgent = \Request::userAgent();
			$username = Auth::user()->username;
			$action = "users.store";
			$message = $username . " creó el usuario " . $validatedData['username'];
			$myController->loguear($clientIP, $userAgent, $username, $action, $message);

			#Envío de mail al administrador que creó de la cuenta del usuario
			$subject = "Creación de usuario";
			$body = "Usuario ". $validatedData['username'] . " creado correctamente por ". Auth::user()->username;
			$to = Auth::user()->email;
			$myController->enviar_email($to, $body, $subject);

			#Envío de mail al usuario de la nueva cuenta creada

			$user = User::where('email', $validatedData['email'])
						->first();
			//Genero email por la creacion de usuario
			$email = $user->email;
			$username = $user->username;
			$nombre = $user->nombre;
			$token = Str::random(60);
			$link = route('create_pass_form', ['token' => $token, 'email' => $email]);
			$subject = "Aviso de creación de cuenta y cambio de contraseña";
			$body = '<p>Hola '.$nombre.',</p>Se ha registrado una nueva cuenta en el sistema de gestión Aleph Manager con su email, su nombre de usuario es "'.$username.'" para continuar la verificación y cambiar la contraseña siga el siguiente link a continuacion:<br><a href="'.$link.'">Haz clic aquí</a>';
			$to = $email;
			$myController->enviar_email($to, $body, $subject);

			Log::info('Correo enviado exitosamente a ' . $to);
			return redirect()->route('users.index')->with('success', 'Usuario creado correctamente.');
		}
	}

	/**************************************************************************
	*
	**************************************************************************/

	/**
	 * Display the specified resource.
	 */
	public function show($id): View
	{
		$user = User::find($id);
	return view('user.show', compact('user'));
	}

	/**************************************************************************
	*
	**************************************************************************/

	/**
	 * Show the form for editing the specified resource.
	 */
	public function edit($id, MyController $myController): View
	{
		$permiso_editar_usuario = $myController->tiene_permiso('edit_usr');
		if (!$permiso_editar_usuario) {
			abort(403, '.');
			return false;
		}
		$roles = Rol::all();
		$users = User::find($id);
		return view('user.edit', compact('users', 'roles'));
	}

	/**************************************************************************
	*
	**************************************************************************/

	/**
	 * Update the specified resource in storage.
	 */
	public function usersUpdate(UserRequest $request, User $user, MyController $myController): RedirectResponse
	{
		#dd($request);
		$permiso_editar_usuario = $myController->tiene_permiso('edit_usr');
		if (!$permiso_editar_usuario) {
			abort(403, '.');
			return false;
		}
		try {
		$messages = [
			'username.unique' => 'El nombre de usuario ya está en uso por otro usuario.',
			'email.unique' => 'El correo electrónico ya está registrado por otro usuario.',
			'rol_id.required' => 'El rol es obligatorio.',
			'rol_id.exists' => 'El rol seleccionado no es válido.',
		];
		// Validar los datos del usuario, ignorando al usuario actual
		$validatedData = $request->validate([
			'username' => 'required|string|max:255|unique:users,username,' . $user->user_id . ',user_id',
			'nombre' => 'required|string|max:255',
			'apellido' => 'required|string|max:255',
			'email' => 'required|string|email|max:255|unique:users,email,' . $user->user_id . ',user_id',
			'rol_id' => 'required|exists:roles,rol_id', // Cambiar "id" por el nombre correcto de la columna primaria
		], $messages);
		// Encuentra el usuario por su ID
		$user = User::find($user->user_id);
		#dd($user);
		#dd($validatedData);
		// Si el usuario no existe, redirige con un mensaje de error
		if (!$user) {
			return redirect('/users')->with('error', 'El usuario no existe.');
		}

		// Almacena el usuario antes de modificarlo
		$username = $user->username;
		$message = Auth::user()->username . " actualizó el usuario " . $username;
		Log::info($message);

		$subject = "Actualización de usuario";
		$body = "El usuario " . $username . " correspondiente a ". $user->nombre ." ". $user->apellido ." fue actualizado por " . Auth::user()->nombre . " " . Auth::user()->apellido;

		#dd($user->email." - ".$validatedData['email']);
		if ($user->email != $validatedData['email']) {
			$to = $user->email.",".$validatedData['email'];
			$body = "El email del usuario ". $user->nombre ." ". $user->apellido ." fue actualizado de ".$user->email." a ".$validatedData['email']." por " . Auth::user()->nombre . " " . Auth::user()->apellido;
		} else {
			$to = $user->email;
		}

		if ($user->username != $validatedData['username']) {
			$to = $validatedData['email'];
			$body = "El username del usuario ". $user->nombre ." ". $user->apellido ." fue actualizado de ".$user->username." a ".$validatedData['username']." por " . Auth::user()->nombre . " " . Auth::user()->apellido;
		} else {
			$to = $user->email;
		}

		// Llamar a enviar_email de MyController
		$myController->enviar_email($to, $body, $subject);

		Log::info('Correo enviado exitosamente a ' . $to);
		if(Auth::user()->username != "omar"){
			#dd("1".Auth::user()->username );
		}

		// Actualizar el usuario con los datos validados
		$user->update($validatedData);



		return redirect()->route('users.index')->with('success', 'Usuario actualizado correctamente.');
		} catch (ValidationException $e) {
			// Si ocurre un error de validación, redirigir con los mensajes de error
			return redirect()->back()->withErrors($e->validator)->withInput();
		} catch (TokenMismatchException $e) {
			// Si se detecta un error 419, redirigir al login
			return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Inicia sesión nuevamente.');
		} catch (\Exception $e) {
			Log::error('Error en la actualización del usuario: '.$e->getMessage());
			#dd('Error: '.$e->getMessage());  // Muestra el mensaje exacto del error
			return redirect()->back()->with('error', 'Ocurrió un error inesperado.');
		}
	}

	/**************************************************************************
	*
	**************************************************************************/

	public function destroy($id, MyController $myController): RedirectResponse
	{
		$permiso_borrar_usuario = $myController->tiene_permiso('del_usr');
		if (!$permiso_borrar_usuario) {
			abort(403, '.');
			return false;
		}
		if(Auth::user()->user_id != $id) 
		{
			// Encuentra el usuario por su ID
			$user = User::withTrashed()->find($id);
			// Almacena el nombre de usuario antes de eliminarlo
			$username = $user->username;
			// Elimina el usuario
			$user->delete();
			$message = Auth::user()->username . " borró el usuario " . $username;
			Log::info($message);
			$subject = "Borrado de usuario";
			$body = "Usuario " . $username . " borrado correctamente por " . Auth::user()->username;
			$to = "omarliberatto@yafoconsultora.com";
			// Llamar a enviar_email de MyController
			$myController->enviar_email($to, $body, $subject);
			Log::info('Correo enviado exitosamente a ' . $to);
			return Redirect::route('users.index')
			->with('success', 'Usuario eliminado correctamente.');
		} else {
			return Redirect::route('users.index')
			->with('error', 'No se puede borrar tu propia cuenta.');
			#return response()->json('No se puede borrar tu propia cuenta', 403);
		}
	}

	/**************************************************************************
	*
	**************************************************************************/

	public function guardar_opciones(Request $request)
	{
		$reset_password_30_dias = $request->reset_password_30_dias ? 1 : 0;
		$configurar_claves = $request->configurar_claves ? 1 : 0;
			Variable::where('nombre', 'reset_password_30_dias')->update([
				'valor' => $reset_password_30_dias
			]);
			Variable::where('nombre', 'configurar_claves')->update([
				'valor' => $configurar_claves
			]);
		return redirect()->route('users.index')->with('success', 'Opciones guardadas correctamente.');
	}				

	/**************************************************************************
	*
	**************************************************************************/

	public function mostrar_opciones(Request $request)
	{
		echo($request);
	}				

	/**************************************************************************
	*
	**************************************************************************/
	public function deshabilitar_usuario(Request $request, $user_id, MyController $myController) {
		if(Auth::user()->user_id != $user_id) {
			$permiso_habilitar_usuario = $myController->tiene_permiso('del_usr');
			if (!$permiso_habilitar_usuario) {
				abort(403, '.');
				return false;
			}
			try {
				$user = User::findOrFail($user_id);
				#echo "sinhab ".$user->habilitado;
				$user->habilitado = ( $user->habilitado != 1 ) ? 1 : 0;
				# $request->input('temporal'); // Valor enviado por AJAX
				#echo $user->habilitado;
				$user->save(); // Guardar los cambios en la base de datos
				return response()->json(['success' => true]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		} else {
			return response()->json('No se puede deshabilitar tu propio usuario.', 403);
		}
	}

	/**************************************************************************
	*
	**************************************************************************/
	public function deshabilitar_usuario_temporal(Request $request, $user_id, MyController $myController) {
		if(Auth::user()->user_id != $user_id) {
			$permiso_habilitar_usuario = $myController->tiene_permiso('del_usr');
			if (!$permiso_habilitar_usuario) {
				abort(403, '.');
				return false;
			}
			try {
				$user = User::findOrFail($user_id);
				#echo "hab ".$user->habilitado;
				$user->habilitado = ( $user->habilitado != 2 ) ? 2 : 0;
				$user->save(); // Guardar los cambios en la base de datos
				return response()->json(['success' => true]);
			} catch (\Exception $e) {
				return response()->json(['error' => $e->getMessage()], 500);
			}
		} else {
			return response()->json('No se puede deshabilitar tu propio usuario.', 403);
		}
	}


	/**************************************************************************
	*
	**************************************************************************/

	public function unlockAccount(Request $request, $userId, MyController $myController)
	{
		$permiso_desbloquear_usuario = $myController->tiene_permiso('enable_usr');
		if (!$permiso_desbloquear_usuario) {
			abort(403, '.');
			return false;
		}
		// Buscar al usuario por ID
		$user = User::find($userId);

		if (!$user || !$user->bloqueado) {
			return redirect()->route('login')->withErrors('Cuenta no encontrada o ya está desbloqueada.');
		}

		// Desbloquear al usuario
		$user->bloqueado = 0;
		$user->intentos_login = 0;
		$user->save();

		// Redirigir a la página para cambiar la contraseña
		return redirect()->route('password.change', ['userId' => $user->user_id]);
	}

	/**************************************************************************
	* Blanqueo de passwords.
	*/

	public function blanquear_password($user_id, MyController $myController)
	{
		#echo "kdk1 ".$user_id;
		if(Auth::user()->user_id != $user_id) 
		{
			// Actualiza la contraseña del usuario
			$user = User::find($user_id);
			if ($user) {
				// Poner el campo 'password' a null y resetear los intentos de login
				$user->password = null;
				$user->intentos_login = 0;
				$user->bloqueado = 0; // Desbloquea al usuario si estaba bloqueado
				$user->save();
				// Generar el token de restablecimiento de contraseña
				$token = Password::createToken($user);
				$email = $user->email;

				// Generar la URL para el restablecimiento de contraseña con el token
				#$resetUrl = route('users.password.reset?token='.$token);
				#$resetUrl = route('password.reset') . '?token=' . $token;
				$resetUrl = route('blank_pass_form', ['token' => $token, 'email' => $email]);
				// Enviar correo
				$subject = "Aviso de blanqueo de contraseña";
				$body = 'Se ha realizado un blanqueo de su contraseña en Aleph Manager, para continuar y cambiar la contraseña siga el siguiente enlace:<br><a href="'.$resetUrl.'">Haz clic aquí</a>';
				$to = $user->email;
				#echo "kdk2 ".$resetUrl;
				$myController->enviar_email($to, $body, $subject);
				return response()->json(['success' => true, 'message' => 'Contraseña blanqueada y correo enviado con éxito.']);
			}
		} else {
			return response()->json('No se puede blanquear tu propia clave', 403);
		}
	}

	/**************************************************************************
	* Set password expiration.
	*/
	public function setPasswordExpiration(User $user)
	{
		// Define el periodo de expiración, por ejemplo, 90 días
		$expirationDays = config('auth.password_expiration_days', 90);

		$user->password_expires_at = Carbon::now()->addDays($expirationDays);
		$user->save();
	}


	/**************************************************************************
	* Set password expiration.
	*/
	public function authenticate(Request $request)
	{
		// Realiza la autenticación habitual
		// ...

		// Verifica si la contraseña ha expirado
		if ($user->password_expires_at && $user->password_expires_at->isPast()) {
			return redirect()->route('password.expired');
		}

		// Continua con el flujo de autenticación
	}
	/**************************************************************************
	* Display a listing of the resource.
	*

	public function showChangePasswordForm(Request $request)
	{
		echo 'token '.$request;
		$token = $request->input('token');
		return view('auth.passwords.reset', ['token' => $token]);
	}

	/**************************************************************************
	 * Display a listing of the resource.
	 *

	public function changePassword(Request $request, $userId)
	{
		$request->validate([
			'password' => [
				'required',
				'confirmed',
				'min:8',
				'regex:/[a-z]/',      // Al menos una letra minúscula
				'regex:/[A-Z]/',      // Al menos una letra mayúscula
				'regex:/[0-9]/',      // Al menos un número
				'regex:/[@$!%*?&#.]/', // Al menos un carácter especial
			],
		]);

		// Buscar al usuario y actualizar la contraseña
		$user = User::find($userId);
		$user->password = Hash::make($request->input('password'));
		$user->save();

		return redirect()->route('login')->with('success', 'Contraseña actualizada con éxito. Inicie sesión con su nueva contraseña.');
	}

	/**************************************************************************
	 * Display a listing of the resource.
	 *

	public function showPasswordForm($id)
	{
		// Busca el usuario por ID
		$selectedUser = User::find($id);
	
		// Obtiene la lista de todos los usuarios (tal como en la función index)
		$users = User::withoutTrashed()
			->leftJoin('roles', 'users.rol_id', '=', 'roles.rol_id')
			->select('users.*', 'roles.nombre as nombre_rol')
			->paginate();
	
		// Devuelve la vista de usuarios con la lista de usuarios y el formulario de cambio de contraseña activo
		return view('user.index', compact('users', 'selectedUser'))
			->with('i', (request()->input('page', 1) - 1) * $users->perPage());
	}

	/**************************************************************************
	* Display a listing of the resource.
	**************************************************************************

	public function showResetForm(Request $request)
	{
		dd($request);
		$token = $request->query('token');
		$email = $request->query('email');
		return view('auth.password_reset', ['token' => $token],['email' => $email]);
	}

*/

}
