<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;
use App\Models\Permiso;
use Illuminate\Support\Facades\Gate;


class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 */
	public function register(): void
	{
		//
	}

	/**
	 * Bootstrap any application services.
	 */
	public function boot()
	{
		if (config('app.env') === 'production') {
			URL::forceScheme('https');
		}
        // Recorre todos los permisos y define un Gate para cada uno
        $permisos = Permiso::get();  // Asegúrate de tener las relaciones configuradas
        foreach ($permisos as $permisoRol) {
		#dd($permisoRol->nombre);
            Gate::define($permisoRol->nombre, function ($user) use ($permisoRol) {
                return $user->roles->contains($permisoRol->rol_id) && $permisoRol->habilitado;
            });
        }
	}
}

