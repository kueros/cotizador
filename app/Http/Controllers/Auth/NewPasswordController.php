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

class NewPasswordController extends Controller
{
    /**
     * Display the password reset view.
     */
    public function create_pass($token, $email)
    {
		#dd($email);
		return view('auth.create_password', ['token' => $token, 'email' => $email]);    
	}


	/**
     * Display the password reset view.
     */
    public function reset_pass($token, $email)
    {
		#dd($email);
		return view('auth.reset_password', ['token' => $token, 'email' => $email]);    
	}


	/**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
	public function store(Request $request): RedirectResponse
	{
		#dd($request->email);
		// Validar los datos del nuevo usuario
/* 		$validatedData = $request->validate([
			'email' => ['required', 'email', 'unique:users,email'], // Asegurar que el email no esté registrado
/* 			'password' => [
				'required',
				'confirmed',
				#Rules\Password::defaults(), // Aplicar las reglas de contraseña por defecto o personalizadas
			],
 		]);
 */		// Buscar el nuevo usuario
		$user = User::where('email', $request->email)->first();
		$user->update([
			'password' => Hash::make($request->password), // Hasheamos la contraseña
		]);/* 		$user = User::update([
			'email' => $request->email,
			'password' => Hash::make($request->password), // Hashear la contraseña
		]);
 */		// Enviar la notificación de verificación de correo electrónico
		#$user->sendEmailVerificationNotification();

		// Redirigir al usuario o mostrar un mensaje de éxito
		#return redirect()->route('login')->with('success', 'Usuario creado con éxito. Por favor, verifica tu correo electrónico para activar tu cuenta.');
		return redirect()->route('login');
	}

    public function showResetForm(Request $request)
	{
		#dd($request);
		$token = $request->query('token');
		return view('auth.password_reset', ['token' => $token]);
	}

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
                'regex:/[@$!%*?&#]/', // Al menos un carácter especial
            ],
            'token' => 'required',
        ]);

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
		#está enviando el email de verificacion a la casilla de yafo y tendría que
		#enviarlo a la de gmail 1967
		#dd(Password::PASSWORD_RESET);
		if ($status == Password::PASSWORD_RESET) {
			// Enviar email de verificación si el usuario no lo ha verificado
			$user = User::where('email', $request->email)->first();
			#dd($user);
			#if (!$user->hasVerifiedEmail()) {
				$user->sendEmailVerificationNotification();
				#return redirect()->route('verification.notice')->with('success', 'Contraseña actualizada con éxito. Por favor, verifica tu correo electrónico.');
			#}
			return redirect()->route('login')->with('success', 'Contraseña actualizada con éxito. Inicie sesión con su nueva contraseña.');
		} else {
			return back()->withErrors(['email' => [__($status)]]);
		}
    }
}
