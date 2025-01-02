<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use app\Models\User;

class VerificarPermisos
{
    public function handle(Request $request, Closure $next, $permiso)
    {
        $user = Auth::user();

        // Verificar si el usuario tiene el rol y permiso adecuado
        $tienePermiso = $user->roles()
                             ->whereHas('permisos', function ($query) use ($permiso) {
                                 $query->where('nombre', $permiso);
                             })->exists();

        if (!$tienePermiso) {
            return redirect()->route('home')->withErrors('No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}