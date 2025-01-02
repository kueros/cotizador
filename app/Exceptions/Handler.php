<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;

class Handler extends ExceptionHandler
{

    public function render($request, Throwable $exception)
    {
        // Verifica si es un error 419 (CSRF token mismatch)
        if ($exception instanceof TokenMismatchException) {
            // Redirige al usuario con un mensaje de que la sesión ha expirado
            return redirect()->route('login')->with('error', 'Tu sesión ha expirado. Por favor, inicia sesión de nuevo para continuar.');
        }

        return parent::render($request, $exception);
}
}
