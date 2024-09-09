<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\View\View;


class ConfiguracionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): View
	{
		$variables = Variable::paginate();

		return view('configuracion.index', compact('variables'))
			->with('i', ($request->input('page', 1) - 1) * $variables->perPage());
	}


	/**
	 * Muestro la vista de variables.
	 */
	public function variables()
	{
		$variables = Variable::all();
		return view('configuracion.variables', compact('variables'));
	}
}
