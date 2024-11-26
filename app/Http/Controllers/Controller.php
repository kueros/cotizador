<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use App\Http\Middleware\CustomCsrfMiddleware;
use Illuminate\Routing\Controller as BaseController; // Asegúrate de extender la clase correcta

abstract class Controller extends BaseController // Extiende de BaseController de Laravel
{
    public function __construct()
    {
        $this->middleware(CustomCsrfMiddleware::class);
    }

    public function get_variable($nombre)
	{
		$variable = Variable::where('nombre', '=', $nombre)->first();

		if( !is_null($variable) ) {
			return $variable->valor;
		} else {
			return false;
		}
	}
}