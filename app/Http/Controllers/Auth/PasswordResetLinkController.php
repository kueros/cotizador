<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use App\Models\User;
use App\Http\Controllers\MyController;
use App\Models\PasswordHistory;
use Illuminate\Support\Facades\Hash;

class PasswordResetLinkController extends Controller
{
    /**
     * Display the password reset link request view.
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request, MyController $myController)
    {
        #dd($request);
        $request->validate([
            'email' => ['required', 'email'],
        ]);


			// Actualiza la contraseña del usuario
			$user = User::where('email', $request->only('email'))
                        ->first();
            #dd($user);
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
				$resetUrl = route('reset_pass_form', ['token' => $token, 'email' => $email]);
				// Enviar correo
				$subject = "Aviso de restablecimiento de contraseña";
                $body = 'Está recibiendo este mail porque Ud. solicitó un restablecimiento de contraseña o porque esta expiró, para continuar y cambiar la contraseña siga el siguiente enlace:<br><a href="'.$resetUrl.'">Haz clic aquí</a>';
				$to = $user->email;
				#echo "kdk2 ".$resetUrl;
				$myController->enviar_email($to, $body, $subject);
				#return response()->json(['success' => true, 'message' => 'Contraseña blanqueada y correo enviado con éxito.']);
                return redirect()->route('login')->with('success', 'Contraseña blanqueada y correo enviado con éxito.');
			} else {
				return redirect()->route('password.email')
				->withErrors(['email' => 'Email incorrecto.'])
				->withInput($request->only('email'));

			}
    }

	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
	/**
     * Display the password reset view.
     */
    public function reset_pass($token, $email)
    {
		#dd($email);
		return view('auth.reset_password', ['token' => $token, 'email' => $email]);    
	}


	/****************************************************************************************************************************************************
	*
	****************************************************************************************************************************************************/
	public function updatePassword(Request $request)
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
		#dd($request->email);
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
