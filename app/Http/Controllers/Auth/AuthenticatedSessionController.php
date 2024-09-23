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
			$users = User::find(Auth::user()->id);
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
		#dd($request->all());
        $request->authenticate();
		#dd($request->all());
		$request->session()->regenerate();
		$message = "Solicitud de autenticacion recibida. " . json_encode($request->all());
		$users = User::find(Auth::user()->id);
		Log::info($message);
		$log = LogAcceso::create([
			'email' => $users->email,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT']
		]);
		$log->save();

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {

		Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}

