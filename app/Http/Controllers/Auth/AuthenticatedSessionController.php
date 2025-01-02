<?php

	namespace App\Http\Controllers\Auth;

	use App\Http\Controllers\Controller;
	use App\Http\Controllers\MyController;
	use App\Http\Requests\Auth\LoginRequest;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\View\View;
	use Illuminate\Support\Facades\Log;
	use App\Models\LogAcceso;
	use App\Models\LogAdministracion;
	use App\Models\User;
	use App\Models\Variable;
	use Illuminate\Support\Facades\Password;

	/*
		Log::emergency($message);
		Log::alert($message);
		Log::critical($message);
		Log::error($message);
		Log::warning($message);
		Log::notice($message);
		Log::info($message);
		Log::debug($message);

	*/
	class AuthenticatedSessionController extends Controller
	{
	/**************************************************************************
	*
	**************************************************************************/
	public function create(): View
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$message = "Solicitud de autenticacion recibida.1 " . json_encode($_POST);
			$users = User::find(Auth::user()->user_id);
			Log::info($message);
/* 			$log = LogAcceso::create([
				'email' => $users->email,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			]);
 */			#$log->save();
		}
		#return view('auth.login');

		// Verificar si hay un mensaje flash en la sesión (por ejemplo, de restablecimiento de contraseña)
		$successMessage = session()->get('success', null);
		$successMessage = "mierda";
		// Retornar la vista pasando el mensaje si existe
		$background_login_custom_path = Variable::where('nombre', 'background_login_custom_path')
								->value('valor');
		$copa_background_login_custom = Variable::where('nombre', 'copa_background_login_custom')
								->value('valor');
		return view('auth.login', [
			'successMessage' => $successMessage,
			'copa_background_login_custom' => $copa_background_login_custom,
			'background_login_custom_path' => $background_login_custom_path
		]);	



	}

	/**************************************************************************
	*
	**************************************************************************/
	public function store(LoginRequest $request): RedirectResponse
	{
		try {
			// Verificamos si la entrada es un email o un username
			$loginField = filter_var($request->input('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
			// Buscar el usuario para verificar intentos previos
			$user = User::where($loginField, $request->input('email'))->first();
			if ($user && $user->bloqueado) {
				return redirect()->route('login')
								->withErrors(['email' => 'Su cuenta está bloqueada debido a múltiples intentos fallidos.'])
								->withInput($request->only('email'));
			}
			if ($user && $user->habilitado == 0) {
				return redirect()->route('login')
								->withErrors(['email' => 'Su cuenta no está habilitada, comuníquese con el administrador.'])
								->withInput($request->only('email'));
			}
			// Intentamos autenticar al usuario con el campo detectado
			$credentials = [
				$loginField => $request->input('email'),
				'password' => $request->input('password'),
			];
			$reset_password_30_dias = Variable::where('nombre', 'reset_password_30_dias')
				->first()['valor'];
			if($reset_password_30_dias){
				$fecha_actual = time();
				$ultima_fecha_restablecimiento = strtotime($user->ultima_fecha_restablecimiento);
				if($ultima_fecha_restablecimiento && ($fecha_actual - $ultima_fecha_restablecimiento) > 2592000){
					$token = Password::createToken($user);
					$email = $user->email;
					$link = route('reset_pass_form', ['token' => $token, 'email' => $email]);
					#dd($link);
					return redirect()->route('login')
						->withErrors(['email' => 'Su clave ha expirado, debe actualizarla para seguir usando la cuenta, para continuar y cambiar la contraseña haga click en "Olvidé mi contraseña"'])
						->withInput($request->only('email'));
				}
			}

			if (Auth::attempt($credentials)) {
				$request->session()->regenerate();
				// Registro del login exitoso
				$message = "Solicitud de autenticación recibida. " . json_encode($request->all());
				Log::info($message);
				// Restablecer los intentos fallidos
				$user->intentos_login = 0;
				$user->ultimo_login = now();
				$user->save();
				// Guardar en la tabla de logs de acceso
				LogAcceso::create([
					'email' => $user->email,
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT'],
				]);
				$copa_background_home_custom = Variable::where('nombre', 'copa_background_home_custom')
										->value('valor');
				$background_home_custom_path = Variable::where('nombre', 'background_home_custom_path')
										->value('valor');
				return redirect()->route('dashboard')->with([
					'copa_background_home_custom' => $copa_background_home_custom,
					'background_home_custom_path' => $background_home_custom_path
				]);
			} else {
				// Incrementar el número de intentos fallidos
				if ($user) {
					$user->intentos_login += 1;
					// Bloquear la cuenta si hay 3 intentos fallidos
					if ($user->intentos_login >= 3) {
						$user->bloqueado = 1;
					}
					$user->save();
				}
				return redirect()->route('login')
								->withErrors(['email' => 'Username, email o contraseña incorrectos'])
								->withInput($request->only('email'));
			}
		} catch (\Exception $e) {
			// Manejar la excepción
			return redirect()->route('login')
							->withErrors(['email' => 'Se produjo un error en el inicio de sesión'])
							->withInput($request->only('email'));
		}
	}
	/**************************************************************************
	*
	**************************************************************************/
	public function destroy(Request $request, MyController $myController): RedirectResponse
	{

		#dd("8".Auth::user()->username );
		$message = "Solicitud de logout recibida. " . json_encode($request->all());
		$users = User::find(Auth::user()->user_id);
		Log::info($message);

/* 		$log = LogAdministracion::create([
			'username' => Auth::user()->username,
			'action' => "logout",
			'detalle' => $message,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT']
		]);
		$log->save();
 */
		Auth::guard('web')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();


		$copa_background_login_custom = Variable::where('nombre', 'copa_background_login_custom')
								->value('valor');
		$background_login_custom_path  = Variable::where('nombre', 'background_login_custom_path')
								->value('valor');
		return redirect()->route('login')->with([
			'copa_background_login_custom' => $copa_background_login_custom,
			'background_login_custom_path' => $background_login_custom_path
		]);


	}
}
