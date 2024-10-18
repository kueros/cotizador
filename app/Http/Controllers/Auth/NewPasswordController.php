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
    public function create($token, $email)
    {
		#dd($email);
		return view('auth.reset-password', ['token' => $token, 'email' => $email]);    }

    /**
     * Handle an incoming new password request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // Here we will attempt to reset the user's password. If it is successful we
        // will update the password on an actual user model and persist it to the
        // database. Otherwise we will parse the error and return the response.
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                event(new PasswordReset($user));
            }
        );

        // If the password was successfully reset, we will redirect the user back to
        // the application's home authenticated view. If there is an error we can
        // redirect them back to where they came from with their error message.
        return $status == Password::PASSWORD_RESET
                    ? redirect()->route('login')->with('status', __($status))
                    : back()->withInput($request->only('email'))
                            ->withErrors(['email' => __($status)]);
    }

    public function showResetForm(Request $request)
	{
		dd($request);
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

		#dd($request->only('email', 'password', 'password_confirmation', 'token'));
        #dd($status);
        #dd(Password::PASSWORD_RESET);
        if ($status == Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('success', 'Contraseña actualizada con éxito. Inicie sesión con su nueva contraseña.');
        } else {
            return back()->withErrors(['email' => [__($status)]]);
        }
    }
/*

Hay que ver por qué no está llegando el email que es por lo que falla la validación de la línea 95

*/

}
