<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use App\Models\User;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class NewPasswordController extends Controller
{
	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
    /**
     * Display the password reset view.
     */
    public function create_pass($token, $email)
    {
		#dd($email);
		return view('auth.create_password', ['token' => $token, 'email' => $email]);    
	}


	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
	/**
     * Display the password reset view.
     */
    public function blank_pass($token, $email)
    {
		#dd($email);
		#return view('auth.reset_password', ['token' => $token, 'email' => $email]);    
		return view('auth.blank_password', ['token' => $token, 'email' => $email]);    
	}


	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
	/**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
	public function store(Request $request): RedirectResponse
	{
        $validatedData = $request->validate([
			'password' => [
				'required',
				'confirmed',
				'min:8',
				'regex:/[a-z]/',      // Al menos una letra minúscula
				'regex:/[A-Z]/',      // Al menos una letra mayúscula
				'regex:/[0-9]/',      // Al menos un número
				'regex:/[@$!%*?&#.]/', // Al menos un carácter especial
			],
			'token' => 'required',		]);
			$user = DB::table('users')
						->where('email', $request->email)
						->update([	'password' =>  Hash::make($validatedData['password']),
									'ultima_fecha_restablecimiento' => now()]);

			return redirect()->route('login')->with('success', 'Contraseña creada con éxito. Inicie sesión con su nueva contraseña.');
	}

	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
    public function showResetForm(Request $request)
	{
		#dd($request);
		$token = $request->query('token');
		return view('auth.password_reset', ['token' => $token]);
	}

	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
    public function password_reset(Request $request)
    {
		#dd($request);
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
			'token' => 'required',		]);

		$status = Password::reset(
			$request->only('email', 'password', 'password_confirmation', 'token'),
			function ($user, $password) {
				$user->forceFill([
					'password' => Hash::make($password),
					'remember_token' => Str::random(60),
				])->save();
				event(new PasswordReset($user));
			}
		);

		if ($status == Password::PASSWORD_RESET) {
			// Enviar email de verificación si el usuario no lo ha verificado
			$user = User::where('email', $request->email)->first();
			$user->sendEmailVerificationNotification();
			return redirect()->route('login')->with('success', 'Contraseña actualizada con éxito. Inicie sesión con su nueva contraseña.');
		} else {
			return back()->withErrors(['email' => [__($status)]]);
		}
    }

	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
	public function blanquear_password(Request $request)
	{
		#dd($request);
		$validated = $request->validate([
            'password' => [
                'required',
                'confirmed',
                'min:8',
                'regex:/[a-z]/',      // Al menos una letra minúscula
                'regex:/[A-Z]/',      // Al menos una letra mayúscula
                'regex:/[0-9]/',      // Al menos un número
                'regex:/[@$!%*?&#.]/', // Al menos un carácter especial
            ],
            'token' => 'required',		]);
		if(Auth::user()){
			$user_id = Auth::user()->user_id;
		} else {
			$user_id = User::where('email', $request->email)->first()['user_id'];
		}

		// Verificar que la nueva contraseña no se repita en las últimas 12
		$previousPasswords = PasswordHistory::where('user_id', $user_id)
			->orderBy('created_at', 'desc')
			->take(12)
			->pluck('password');

			foreach ($previousPasswords as $oldPassword) {
			if (Hash::check($request->password, $oldPassword)) {
				return back()->withErrors(['password' => 'La contraseña no puede ser igual a una de las últimas 12.']);
			}
		}

		// Contar cuántas contraseñas tiene en el historial
	    $passwordCount = PasswordHistory::where('user_id', $user_id)->count();

		if ($passwordCount >= 12) {
			PasswordHistory::where('user_id', $user_id)
				->orderBy('created_at', 'asc')
				->first()
				->delete();
		}
		
/* 		$request->user()->update([
            'password' => Hash::make($validated['password']),
            'bloqueado' => 0,
            'intentos_login' => 0,
        ]);
 */

		User::where('email', $request->email)->update([
            'password' => Hash::make($validated['password']),
			'bloqueado' => 0,
			'intentos_login' => 0,
			'ultima_fecha_restablecimiento' => now()
		]);
		// Guardar la nueva contraseña en el historial
		PasswordHistory::create([
			'user_id' => $user_id,
			'password' => Hash::make($validated['password']),
		]);

		#return back()->with('status', 'Contraseña actualizada correctamente.');
		return redirect()->route('login')->with('success', 'Contraseña actualizada correctamente.');
	}

}
