<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Session\TokenMismatchException;

class CustomCsrfMiddleware extends Middleware
{
	public function handle($request, Closure $next)
	{
		#dd($_REQUEST);

		try {
			#dd('asdf12345678');
			// Ejecutar la lógica de verificación de CSRF normal
			return parent::handle($request, $next);
		} catch (TokenMismatchException $exception) {
			#dd('asdf1234abcd');
			// Aquí puedes manejar el error 419 y personalizar la respuesta
			if ($request->expectsJson()) {
				#dd('asdf1234qwer');
				return response()->json(['error' => 'Sesión expirada, recarga la página.'], 419);
			}

			return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.');
		}
	}
}