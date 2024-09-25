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

								$variables = $variables->filter(function ($variable) {
									return !is_null($variable);
								});
								
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

	public function guardar_remitente(Request $request)
	{
		#dd($_POST);
		#por el momento está funcionando el envío de emails desde el controlador OrderShipmentController.php
		$from = trim($_POST['from']);
		$from_name = trim($_POST['from_name']);
		if ($from != '' || $from_name != '') {
			Variable::where('nombre', '_notificaciones_email_from')->update(['valor' => $from]);
			Variable::where('nombre', '_notificaciones_email_from_name')->update(['valor' => $from_name]);

			return response()->json(['success' => 'Datos del remitente guardados.']);
		} else {
			return response()->json(['error' => 'Ninguno de los 2 valores puede quedar vacío.']);
		}

		return; // redirect()->route('configuracion');
	}
    /**
     * Enviar el pedido dado.
     */
    public function enviar_mail(Request $request, MyController $myController): RedirectResponse
    {
        #$user = User::findOrFail($request->user_id);
        $user = User::all();
        // Enviar el pedido...
#dd("kdkdkdkdkdkdk ".$user);
        $subject = "Prueba de envío de correo desde Laravel";
        $body = "Prueba de envío de correo desde Laravel. Esto es el body del correo.";
        $to = "omarliberatto@yafoconsultora.com";



        /*

        OK, FUNCIONA, AHORA HAY QUE DESHARDCODEAR EL SUBJECT, EL BODY Y EL TO!!!!!!

        */

        try {
            // Llamar a enviar_email de MyController
            $myController->enviar_email($to, $body, $subject);
            Log::info('Correo enviado exitosamente a ' . $to);
        } catch (Exception $e) {
            // Manejo de la excepción
            Log::error('Error al enviar el correo: ' . $e->getMessage());

            // Puedes redirigir al usuario con un mensaje de error
            return redirect('/users')->with('error', 'Hubo un problema al enviar el correo. Por favor, intenta nuevamente.');
        }

/*         // Envío del correo utilizando Mailable
        try {
            Mail::to("omarliberatto@yafoconsultora.com")->send(new OrderShipped($user));
            Log::info('Correo Mailable enviado exitosamente a omarliberatto@yafoconsultora.com');
        } catch (Exception $e) {
            // Manejo de la excepción al enviar con Mailable
            Log::error('Error al enviar el correo con Mailable: ' . $e->getMessage());

            // Redirigir con mensaje de error
            return redirect('/users')->with('error', 'No se pudo enviar el correo Mailable. Inténtalo de nuevo.');
        }
 */
        return redirect('/users')->with('success', 'Correo enviado correctamente.');
    }

	public function add_parametro_email(Request $request){

		$parametro = trim($_POST['parametro']);
		$valor = trim($_POST['valor']);
		#dd($parametro, $valor); //234 y 234
		if($parametro != '' && $valor != ''){
			$notificaciones_email_config = Variable::where('nombre', 'notificaciones_email_config')->first();
			$configs = json_decode($notificaciones_email_config->valor);
			$mail_config = array();
			if($configs != ''){
				foreach($configs as $key => $config){
				$mail_config[$key] = $config;
				}
			}

			if(!isset($mail_config[$parametro])){
				$mail_config[$parametro] = $valor;

				Variable::where('nombre', 'notificaciones_email_config')->update(['valor' => json_encode($mail_config)]);
				return redirect('/configuracion')->with('success', 'Parametro guardado.');
			}else{
				return redirect('/configuracion')->with('error', 'El parametro ya existe.');
			}

			#$this->guardar_log("Modificó los parametros para envio de emails");
		}else{
			return redirect('/configuracion')->with('error', 'Los valores no pueden estar vacios.');
		}
		return redirect('/configuracion');
	}

	public function ajax_delete_parametro_email(){
	
		$parametro = trim($_POST['parametro']);

		if($parametro != ''){
			$notificaciones_email_config = Variable::where('nombre', 'notificaciones_email_config')->first();
			$configs = json_decode($notificaciones_email_config->valor);
			$mail_config = array();
			foreach($configs as $key => $config){
				$mail_config[$key] = $config;
			}

			if(isset($mail_config[$parametro])){
				unset($mail_config[$parametro]);
			}

			Variable::where('nombre', 'notificaciones_email_config')->update(['valor' => json_encode($mail_config)]);

			return true;
		}else{
			return false;
		}
	}


	
}


