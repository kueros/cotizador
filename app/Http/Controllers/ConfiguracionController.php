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
								->get(['nombre', 'nombre_menu', 'valor']);

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

    public function guardar_estado(Request $request)
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

	public function guardar_remitente_email()
	{
		#dd($_POST);
		$from = trim($_POST['from']);
		$from_name = trim($_POST['from_name']);
		if ($from != '' || $from_name != '') {
			Variable::where('nombre', 'notificaciones_email_from')->update(['valor' => $from]);
			Variable::where('nombre', 'notificaciones_email_from_name')->update(['valor' => $from_name]);

			return response()->json(['success' => 'Datos del remitente guardados.']);
		} else {
			return response()->json(['error' => 'Ninguno de los 2 valores puede quedar vacío.']);
		}

		return; // redirect()->route('configuracion');
	}


}


