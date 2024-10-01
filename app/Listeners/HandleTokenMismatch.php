<?php

namespace App\Listeners;

use Closure;
use Illuminate\Session\TokenMismatchException;

class HandleTokenMismatch
{
    public function handle($request, Closure $next)
    {
        try {
            return $next($request);
        } catch (TokenMismatchException $e) {
            // Redirigir al login con mensaje de error si la sesión ha expirado
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo.');
        }
    }
}
