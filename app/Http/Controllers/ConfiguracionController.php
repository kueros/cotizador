<?php

namespace App\Http\Controllers;

use App\Models\Variable;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Log;
use Exception;
use App\Http\Controllers\MyController;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\LogAdministracion;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ConfiguracionController extends Controller
{
	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function index(MyController $myController): View
	{
		$permiso_configuraciones_software = $myController->tiene_permiso('setup_soft');
		if (!$permiso_configuraciones_software) {
			abort(403, '.');
			return false;
		}
		$variables = Variable::where('nombre', 'like', '%noti%')
			->orWhere('nombre', 'like', '%opav%')
			->orWhere('nombre', 'like', '%copa%')
			->orWhere('nombre', 'like', '%background%')
			->get(['nombre', 'nombre_menu', 'valor']);
		$variables = $variables->filter(function ($variable) {
			return !is_null($variable);
		});
		return view('configuracion.index', compact('variables'));
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function variables(MyController $myController)
	{
		$permiso_configuraciones_software = $myController->tiene_permiso('setup_soft');
		if (!$permiso_configuraciones_software) {
			abort(403, '.');
			return false;
		}
		$variables = Variable::all();
		return view('configuracion.variables', compact('variables'));
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function guardar_estado(Request $request, MyController $myController)
	{
		$permiso_guardar_configuraciones_software = $myController->tiene_permiso('save_setup_soft');
		if (!$permiso_guardar_configuraciones_software) {
			abort(403, '.');
			return false;
		}
		try {
			// Guardo los datos de los estados.
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
			$message = "Configuración global modificada por " . Auth::user()->apellido . ", " . Auth::user()->nombre . ": " . $nombre . " al valor: " . $valor;
			$users = User::find(Auth::user()->user_id);
			Log::info($message);
			$log = LogAdministracion::create([
				'username' => Auth::user()->username,
				'action' => "guardar_estado",
				'detalle' => $message,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			]);
			$log->save();
			session()->flash('success', 'Estado guardado correctamente');
			return response()->json(['success' => 'Estado guardado correctamente']);
		} catch (\Exception $e) {
			// Registrar el error para depuración
			$message = "Guardar estado recibido. " . json_encode($request->all());
			$users = User::find(Auth::user()->user_id);
			Log::info($message);
			$log = LogAdministracion::create([
				'username' => Auth::user()->username,
				'action' => "guardar_estado",
				'detalle' => $message,
				'ip_address' => $_SERVER['REMOTE_ADDR'],
				'user_agent' => $_SERVER['HTTP_USER_AGENT']
			]);
			$log->save();
			Log::error('Error al guardar estado: ' . $e->getMessage());
			session()->flash('error', 'Hubo un error al guardar el estado');
			return response()->json(['error' => 'Hubo un error al guardar el estado'], 500);
		}
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/

	public function guardarImagenHome(Request $request)
	{
		try {
			// Validar que el archivo es una imagen
			$request->validate([
				'copa_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
			], [
				'copa_path.required' => 'Debes seleccionar una imagen.',
				'copa_path.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png, gif o webp.',
				'copa_path.max' => 'El archivo no debe superar los 5 MB.',
			]);

			$file = $request->file('copa_path');
			$time = date('Ymd_His');
			$filename = "background_home_" . $time . ".jpg";
			$path_filename = 'images/' . $filename;
			$variable = Variable::where('nombre', '=', 'background_home_custom_path')->first();
			if (is_null($variable)) {
				return redirect()->back()->with('error', 'La variable no existe.');
			}
			$path_oldfilename = public_path($variable->valor);
			if (file_exists($path_oldfilename)) {
				unlink($path_oldfilename);
			}

			$path = $file->move(public_path('images'), $filename);
			$variable->valor = $path_filename;
			$variable->save();

			return redirect()->back()->with('success', 'Imagen guardada correctamente.');
		} catch (\Exception $ex) {
			return redirect()->back()->with('error', 'La imagen no se pudo cargar.');
		}
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/

	public function guardarImagenLogin(Request $request)
	{
		try {
			// Validar que el archivo es una imagen
			$request->validate([
				'copa_path' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
			], [
				'copa_path.required' => 'Debes seleccionar una imagen.',
				'copa_path.mimes' => 'La imagen debe ser de tipo: jpeg, jpg, png, gif o webp.',
				'copa_path.max' => 'El archivo no debe superar los 5 MB.',
			]);
			// Obtener el archivo de la solicitud
			$file = $request->file('copa_path');
			$time = date('Ymd_His');
			$filename = "background_login_" . $time . ".jpg";
			$path_filename = 'images/' . $filename;
			$variable = Variable::where('nombre', '=', 'background_login_custom_path')->first();
			if (is_null($variable)) {
				return redirect()->back()->with('error', 'La variable no existe.');
			}
			$path_oldfilename = public_path($variable->valor);
			if (file_exists($path_oldfilename)) {
				unlink($path_oldfilename);
			}

			$path = $file->move(public_path('images'), $filename);
			$variable->valor = $path_filename;
			$variable->save();

			return redirect()->back()->with('success', 'Imagen guardada correctamente.');
		} catch (\Exception $ex) {
			return redirect()->back()->with('error', 'La imagen no se pudo cargar.');
		}
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function guardar_remitente(Request $request, MyController $myController)
	{
		$permiso_guardar_configuraciones_software = $myController->tiene_permiso('save_setup_soft');
		if (!$permiso_guardar_configuraciones_software) {
			abort(403, '.');
			return false;
		}

		$from = trim($_POST['from']);
		$from_name = trim($_POST['from_name']);
		$users = User::find(Auth::user()->user_id);
		$message = "Se modificó el remitente guardado a: Email = " . $from . " Nombre = " . $from_name . " por " . $users->apellido . ", " . $users->nombre;
		Log::info($message);
		$log = LogAdministracion::create([
			'username' => Auth::user()->username,
			'action' => "guardar_remitente",
			'detalle' => $message,
			'ip_address' => $_SERVER['REMOTE_ADDR'],
			'user_agent' => $_SERVER['HTTP_USER_AGENT']
		]);
		$log->save();
		if ($from != '' || $from_name != '') {
			Variable::where('nombre', '_notificaciones_email_from')->update(['valor' => $from]);
			Variable::where('nombre', '_notificaciones_email_from_name')->update(['valor' => $from_name]);
			session()->flash('success', 'Datos del remitente guardados.');
			return response()->json(['success' => 'Datos del remitente guardados.']);
		} else {
			session()->flash('error', 'Ninguno de los 2 valores puede quedar vacío.');
			return response()->json(['error' => 'Ninguno de los 2 valores puede quedar vacío.']);
		}
		return; // redirect()->route('configuracion');
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function add_parametro_email(Request $request, MyController $myController)
	{
		$permiso_guardar_configuraciones_software = $myController->tiene_permiso('save_setup_soft');
		if (!$permiso_guardar_configuraciones_software) {
			abort(403, '.');
			return false;
		}

		$parametro = trim($_POST['parametro']);
		$valor = trim($_POST['valor']);
		//dd($parametro, $valor); //234 y 234
		if ($parametro != '' && $valor != '') {
			$notificaciones_email_config = Variable::where('nombre', '_notificaciones_email_config')->first();
			$configs = json_decode($notificaciones_email_config->valor);

			$mail_config = array();
			if ($configs != '') {
				foreach ($configs as $key => $config) {
					$mail_config[$key] = $config;
				}
			}

			if (!isset($mail_config[$parametro])) {
				$mail_config[$parametro] = $valor;

				Variable::where('nombre', '_notificaciones_email_config')->update(['valor' => json_encode($mail_config)]);

				$users = User::find(Auth::user()->user_id);
				$message = "Se agregó el parámetro " . $valor . " al email por " . $users->apellido . ", " . $users->nombre;
				Log::info($message);
				$log = LogAdministracion::create([
					'username' => Auth::user()->username,
					'action' => "guardar_remitente",
					'detalle' => $message,
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT']
				]);
				$log->save();

				return redirect('/configuracion')->with('success', 'Parametro guardado.');
			} else {
				return redirect('/configuracion')->with('error', 'El parametro ya existe.');
			}

			#$this->guardar_log("Modificó los parametros para envio de emails");
		} else {
			return redirect('/configuracion')->with('error', 'Los valores no pueden estar vacios.');
		}
		return redirect('/configuracion');
	}

	/*****************************************************************************************************************
	 *****************************************************************************************************************/
	public function ajax_delete_parametro_email(MyController $myController)
	{
		$permiso_guardar_configuraciones_software = $myController->tiene_permiso('save_setup_soft');
		if (!$permiso_guardar_configuraciones_software) {
			abort(403, '.');
			return false;
		}

		$parametro = trim($_POST['parametro']);

		if ($parametro != '') {
			$notificaciones_email_config = Variable::where('nombre', '_notificaciones_email_config')->first();
			$configs = json_decode($notificaciones_email_config->valor);
			$mail_config = array();
			foreach ($configs as $key => $config) {
				$mail_config[$key] = $config;
			}
			if (isset($mail_config[$parametro])) {
				$users = User::find(Auth::user()->user_id);
				$message = "Se eliminó el parámetro " . $mail_config[$parametro] . " del email por " . $users->apellido . ", " . $users->nombre;
				Log::info($message);
				$log = LogAdministracion::create([
					'username' => Auth::user()->username,
					'action' => "guardar_remitente",
					'detalle' => $message,
					'ip_address' => $_SERVER['REMOTE_ADDR'],
					'user_agent' => $_SERVER['HTTP_USER_AGENT']
				]);
				$log->save();
				unset($mail_config[$parametro]);
			}
			session()->flash('success', 'Se eliminó el parámetro.');
			Variable::where('nombre', '_notificaciones_email_config')->update(['valor' => json_encode($mail_config)]);

			return true;
		} else {
			session()->flash('error', 'Error al borrar el parámetro.');
			return false;
		}
	}
}
