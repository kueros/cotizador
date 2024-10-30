<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\View\View;
use App\Models\LogAcceso;
use App\Http\Requests\LogAccesoRequest;
use App\Models\LogAdministracion;


class MonitoreoController extends Controller
{
	/**
	 * Muestro la vista de monitoreo.
	 */
    public function index()
    {
		$data = [
			'breadcumb_deshabilitado' => 'Monitoreo',
            'breadcumb_habilitado' => ['Inicio' => "" /*base_url('dashboard')*/]
		];
		$breadcumb_deshabilitado = 'Monitoreo';
		$breadcumb_habilitado = ['Inicio' => "" /*base_url('dashboard')*/];
        return view('monitoreo.index')->with($data);
    }

	/**
	 * Muestro la vista de logs de accesos.
	 */
	public function log_accesos(Request $request): View
	{
		$logs_accesos = LogAcceso::orderBy('created_at', 'desc')->get();
		#dd($logs_accesos);
		return view('monitoreo.logs_accesos', compact('logs_accesos'));
	}


	/**
	 * Muestro la vista de logs de accesos.
	 */
	public function log_administracion(Request $request): View
	{
		$logs_administracion = LogAdministracion::select(['id', 'username', 'detalle', 'created_at', 'ip_address', 'user_agent'])
			->orderBy('created_at', 'desc')->get();
		#dd($logs_administracion);
		return view('monitoreo.logs_administracion', compact('logs_administracion'));
	}
	
	public function ajax_log_administracion(){
        ini_set('memory_limit', '-1');

		$logs_administracion = LogAdministracion::select(['id', 'username', 'detalle', 'created_at', 'ip_address', 'user_agent'])
			->orderBy('created_at', 'desc')->get();
		
		$data = array();
		foreach($logs_administracion as $r){

			$data[] = array(
				$r->id,
				$r->username,
				$r->detalle,
				$r->created_at,
				$r->ip_address,
				$r->user_agent
			);
		}

		$output = [
            "recordsTotal" => $logs_administracion->count(),
            "recordsFiltered" => $logs_administracion->count(),
            "data" => $data
        ];

		return response()->json($output);
    }

	public function ajax_log_acceso(){
        ini_set('memory_limit', '-1');

		$logs_acceso = LogAcceso::select(['id', 'email', 'created_at', 'ip_address', 'user_agent'])
			->orderBy('created_at', 'desc')->get();
		
		$data = array();
		foreach($logs_acceso as $r){

			$data[] = array(
				$r->id,
				$r->email,
				$r->created_at,
				$r->user_agent,
				$r->ip_address
			);
		}

		$output = [
            "recordsTotal" => $logs_acceso->count(),
            "recordsFiltered" => $logs_acceso->count(),
            "data" => $data
        ];

		return response()->json($output);
    }
}
