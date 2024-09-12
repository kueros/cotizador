<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;

class ConfiguracionController extends Controller
{
	/**
	 * Display a listing of the resource.
	 */
	public function index(Request $request): View
	{
		$variables = Variable::where('nombre', 'like', '%noti%')
								->orWhere('nombre', 'like', '%opav%')
								->orWhere('nombre', 'like', '%copa%')
								->get(['nombre', 'valor']);

		return view('configuracion.index', compact('variables'));
	}


	/**
	 * Muestro la vista de variables.
	 */
	public function variables()
	{
		$variables = Variable::all();
		return view('configuracion.variables', compact('variables'));
	}

    public function guardarEstado(Request $request)
    {
        try {
            // Itera sobre las variables enviadas y guarda sus valores
            foreach ($request->all() as $nombre => $valor) {
                // Saltar _token
                if ($nombre === '_token') {
                    continue;
                }
                // Encuentra la variable y actualiza su valor
                $variable = Variable::where('nombre', $nombre)->first();
                if ($variable) {
                    $variable->valor = $valor;
                    $variable->save();
                }
            }
            return response()->json(['success' => 'Estado guardado correctamente']);
        } catch (\Exception $e) {
            // Registrar el error para depuración
            Log::error('Error al guardar estado: ' . $e->getMessage());
            return response()->json(['error' => 'Hubo un error al guardar el estado'], 500);
        }
    }

}


