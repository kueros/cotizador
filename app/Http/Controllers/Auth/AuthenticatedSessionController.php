<?php

	namespace App\Http\Controllers\Auth;

	use App\Http\Controllers\Controller;
	use App\Models\User;
	use App\Http\Requests\Auth\LoginRequest;
	use Illuminate\Http\RedirectResponse;
	use Illuminate\Http\Request;
	use Illuminate\Support\Facades\Auth;
	use Illuminate\View\View;
	use Illuminate\Support\Facades\Log;
	use App\Models\LogAcceso;
	use App\Models\LogAdministracion;
	use App\Http\Controllers\MyController;

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
	/**
	 * Display the login view.
	 */
	public function create(): View
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$message = "Solicitud de autenticacion recibida.1 " . json_encode($_POST);
			$users = User::find(Auth::user()->user_id);
			Log::info($message);
			$log = LogAcceso::create([
				'email' => $users->email,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			]);
			$log->save();
		}
		return view('auth.login');
	}

	/**
	 * Handle an incoming authentication request.
	 */
	public function store(LoginRequest $request): RedirectResponse
	{
        #dd($_REQUEST);
		try {
			$request->authenticate();
			$request->session()->regenerate();

			$message = "Solicitud de autenticacion recibida. " . json_encode($request->all());
			$users = User::find(Auth::user()->user_id);
			Log::info($message);

			$log = LogAcceso::create([
				'email' => $users->email,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			]);
			$log->save();
            #dd("6".Auth::user()->username );
			return redirect()->intended(route('dashboard', absolute: false));
		} catch (\Exception $e) {
			// Captura la excepción y redirige con un mensaje de error
            #dd("7".Auth::user()->username );
			return redirect()->route('login')
							->withErrors(['email' => 'Username, email o contraseña incorrectos.'])
							->withInput($request->only('email'));
		}
	}

	/**
	 * Destroy an authenticated session.
	 */
	public function destroy(Request $request, MyController $myController): RedirectResponse
	{

		#dd("8".Auth::user()->username );
		$message = "Solicitud de logout recibida. " . json_encode($request->all());
		$users = User::find(Auth::user()->user_id);
		Log::info($message);

		$log = LogAdministracion::create([
			'username' => Auth::user()->username,
			'action' => "logout",
			'detalle' => $message,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT']
		]);
		$username = $users->username;
		$subject = "Logout";
		$body = "Usuario " . $username . " ha cerrado sesión correctamente.";
		$to = "omarliberatto@yafoconsultora.com";

		$myController->enviar_email($to, $body, $subject);

		$log->save();

		Auth::guard('web')->logout();
		$request->session()->invalidate();
		$request->session()->regenerateToken();

		return redirect()->route('login');
	}
	}

