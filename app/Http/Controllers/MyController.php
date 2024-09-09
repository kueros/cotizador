<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MyController extends Controller
{
	private $datos_vista = array();

	public function __construct()
	{
		Parent::__construct();
		header('X-Frame-Options: DENY');

		//Cargo los modelos base
/* 		$this->load->model("Generic_model", "generic");
		$this->load->model("Usuario_model", "usuarios");
 */
		//Verifico si tiene habilitada whitelist
		$bloquea_ip = $this->get_variable("whitelist_status");

		define('ACCESO_FORM_ENCUADRAMIENTO', $this->get_variable("acceso_formulario_encuadramiento"));
		define('ACCESO_MODULO_AUDITORIA', $this->get_variable("habilita_modulo_auditoria"));
		define('ACCESO_CANALES_CRITICIDAD_ESCENARIOS', $this->get_variable("habilita_canales_criticidad_escenarios"));
		define('ACCESO_CONTRATACION_STI', $this->get_variable("habilita_proceso_contratacion_sti"));
		define('ACCESO_CMDB_CORPORATIVA', $this->get_variable("mostrar_cmdb_corporativa"));

		if ($bloquea_ip) {
			if (strpos($_SERVER['REQUEST_URI'], "API") === false) {
				if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
					$ip_1 = $_SERVER['HTTP_CF_CONNECTING_IP'];
				} else {
					$ip_1 = false;
				}

				if (isset($_SERVER['REMOTE_ADDR'])) {
					$ip_2 = $_SERVER['REMOTE_ADDR'];
				} else {
					$ip_2 = false;
				}

				if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
					$ip_3 = $_SERVER['HTTP_X_FORWARDED_FOR'];
				} else {
					$ip_3 = false;
				}

				if (!$this->check_ip_whitelist($ip_1)) {
					if (!$this->check_ip_whitelist($ip_2)) {
						if (!$this->check_ip_whitelist($ip_3)) {
							header("HTTP/1.1 403 Forbidden");
							exit;
						}
					}
				}
			}
		}

		$this->check_status_ambiente();

		$modo_debug = $this->get_variable("habilitar_modo_debug");

		$servers_debug = array(
			'aleph.localhost',
			'dev.alephmanager.com'
		);

		//Si esta en modo debug habilitado o es localhost muestro los errores
		if ((isset($_SERVER['SERVER_NAME']) && in_array($_SERVER['SERVER_NAME'], $servers_debug)) || $modo_debug && $modo_debug == 1) {
			ini_set('display_errors', 1);
			ini_set('display_startup_errors', 1);
			error_reporting(E_ALL);
		} else {
			error_reporting(0);
		}

		//Verifico que el ambiente este habilitado
		$activado = $this->get_variable("activado");

		if ($activado !== false) {
			if (strpos($_SERVER['REQUEST_URI'], 'API') == false) {
				if ($activado == '0') {
					$this->mostrar_error("El ambiente se encuentra deshabilitado. Si cree que esto es un error contáctese con <a href=\"mailto:soporte@alephmanager.com\">soporte@alephmanager.com</a>.");
				}
			}
		}

		//Chequeo si tiene login con azure
		$pagina_azure = strpos($_SERVER['REQUEST_URI'], 'login/azure_ad');

		if ($pagina_azure != 1) {
			$this->load->library('session');
		}

		if (strpos($_SERVER['REQUEST_URI'], 'cambiar_password') === false && strpos($_SERVER['REQUEST_URI'], 'logout') === false && isset($_SESSION['cambiar_password'])) {
			$this->session->set_flashdata('error_message_fixed', 'Por seguridad debe cambiar su contraseña predeterminada.');
			redirect(base_url('usuarios/cambiar_password'));
		}

		if (strpos($_SERVER['REQUEST_URI'], 'cambiar_password') === false && strpos($_SERVER['REQUEST_URI'], 'logout') === false && isset($_SESSION['cambiar_password_externo'])) {
			$this->session->set_flashdata('error_message_fixed', 'Por seguridad debe cambiar su contraseña predeterminada.');
			redirect(base_url('usuarios_externos/cambiar_password'));
		}

		//Google Auth
		if (isset($_GET['code']) && isset($_GET['scope'])) {
			redirect(base_url('usuarios/login') . "?google_data=" . json_encode($_GET));
		}

		if (isset($_SESSION['reset_password'])) {
			if ($_SERVER['REQUEST_URI'] != '/usuarios/cambiar_password' && $_SERVER['REQUEST_URI'] != '/usuarios/logout') {
				$this->session->set_flashdata('error_message_fixed', 'Su clave ha expirado, debe actualizarla para seguir usando la cuenta.');
				redirect(base_url('/usuarios/cambiar_password'));
			}
		}

		//Chequeo los modulos que estan habilitados
		$modulos_activos = array();
		$existe_tabla = $this->generic->check_table("modulos");
		if ($existe_tabla) {
			$modulos = $this->generic->get_from_table("modulos", array("estado" => 1));

			if (!empty($modulos)) {
				foreach ($modulos as $modulo) {
					$modulos_activos[] = $modulo->nombre;
				}
			}
		}

		//Traigo los niveles del modelo para armar el menu
		$cont = 0;
		$menu_organizacion = array();
		$niveles_ro = $this->generic->get_from_table("modelo_niveles", array("disabled" => 0), "posicion asc,dependencia_id desc");
		if (!empty($niveles_ro)) {
			foreach ($niveles_ro as $nivel) {
				if (isset($nivel->disabled) && $nivel->disabled == 1) {
					continue;
				}
				$menu_organizacion[$cont]['nombre'] = $nivel->nombre;
				$menu_organizacion[$cont]['id'] = $nivel->id;
				$cont++;
			}
		}

		//Verifico si esta logueado y tiene permisos para ambiente coorporativo para mostrarlo en el menu
		if ($this->is_logged_in() && $this->es_usuario_interno()) {
			$acceso_coorporativo = $this->has_permission(388, false, false);

			if ($acceso_coorporativo) {
				$existe_tabla = $this->generic->check_table("entidades");

				if ($existe_tabla) {
					$entidades = array();
					$entidades_corporacion = $this->generic->get_from_table("entidades", false, "nombre asc");

					if (!empty($entidades_corporacion)) {
						$cont = 0;
						foreach ($entidades_corporacion as $entidad) {
							$entidades[$cont]['nombre'] = $entidad->nombre;
							$entidades[$cont]['url'] = $entidad->url;
							$cont++;
						}
					}
					define('ENTIDADES_CORPORACION', $entidades);
				}
			}
		}

		$niveles_modelo = $this->obtener_array_niveles_modelo();

		//Verifico los datos para crear las definiciones del menu
		define('MENU_ORG', $menu_organizacion);
		define('NIVELES_MODELO', $niveles_modelo);
		define('MODULOS_ACTIVOS', $modulos_activos);

		//Para usuarios externos cargo configuración de menú
		if ($this->es_usuario_externo()) {
			define('externos_habilita_menu_formulario_control', $this->get_variable('externos_habilita_menu_formulario_control'));
			define('externos_habilita_menu_encuestas_control', $this->get_variable('externos_habilita_menu_encuestas_control'));
			define('externos_titulo_menu_formulario_control', $this->get_variable('externos_titulo_menu_formulario_control'));
			define('externos_titulo_menu_encuestas_control', $this->get_variable('externos_titulo_menu_encuestas_control'));
		}

		//OBTENGO LOS ESTILOS DE LA HERRAMIENTA
		$_SESSION['leyenda_ambiente'] = $this->get_variable('leyenda_ambiente');
		$_SESSION['leyenda_ambiente_color'] = $this->get_variable('leyenda_ambiente_color');
		$_SESSION['leyenda_ambiente_texto'] = $this->get_variable('leyenda_ambiente_texto');
		$_SESSION['aleph_estilo_logotipo_default'] = $this->get_variable('aleph_estilo_logotipo_default');
		$imagen_logo_custom = $this->get_variable('aleph_estilo_logotipo_custom_path');
		$path_logo_custom = UPLOADS_URL . '/images/' . $imagen_logo_custom;
		$url_logo_custom = UPLOADS_URL . '/images/' . $imagen_logo_custom;
		$_SESSION['aleph_estilo_logotipo_custom_path'] = $path_logo_custom;
		$_SESSION['aleph_estilo_logotipo_custom_url'] = $url_logo_custom;
		$_SESSION['aleph_estilo_menu_default'] = $this->get_variable('aleph_estilo_menu_default');
		$_SESSION['aleph_estilo_color_barra_menu'] = $this->get_variable('aleph_estilo_color_barra_menu');
		$_SESSION['aleph_estilo_color_titulos_menu'] = $this->get_variable('aleph_estilo_color_titulos_menu');
		$_SESSION['aleph_estilo_color_mouseover_menu'] = $this->get_variable('aleph_estilo_color_mouseover_menu');

		$version = $this->get_variable("version");
		$fecha_version = $this->get_variable("fecha_version");

		if (isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'aleph.localhost') {
			$session_time = 9999999;
		} else {
			$session_time = $this->get_variable("session_time");

			if (!$session_time) {
				$session_time = 900;
			}
		}

		//Cargo traducciones
		$this->cargar_traducciones();

		define('SESSION_TIME', $session_time);
		define('VERSION_ALEPH', "v" . $version . " - " . $fecha_version);

		//21/04/2022 Filtro los contenidos por POST
		$this->filtrar_contenido_post();
		$this->filtrar_contenido_get();
	}

	protected function check_status_ambiente()
	{
		//Recupero cuando se realizó la última version
		$fecha_version = $this->get_variable("fecha_version");

		//Verifico contra fecha actual
		$fecha_actual = date('Y-m-d');

		$fecha_version = new DateTime($fecha_version);
		$fecha_actual = new DateTime($fecha_actual);

		$diferencia = $fecha_version->diff($fecha_actual);
		$cantidad_dias = $diferencia->days;

		if ($cantidad_dias > 300) {
			$this->mostrar_error('El ambiente requiere una actualización');
		}
	}

	public function obtener_array_niveles_modelo()
	{
		$niveles = array();

		$niveles_modelo = $this->generic->get_from_table("modelo_niveles", false, "posicion asc, dependencia_id asc");
		$niveles[0]['nombre'] = 'Inventario tecnológico';
		$niveles[0]['tabla'] = 'tabla_activos';
		if (!empty($niveles_modelo)) {
			foreach ($niveles_modelo as $nivel_modelo) {
				$campos = array();
				$campos_nivel = $this->generic->get_from_table("registros_oyp_posicion", array("nivel_id" => $nivel_modelo->id), "posicion asc");

				foreach ($campos_nivel as $index => $campo) {
					foreach ($campo as $key => $value) {
						$campos[$index][$key] = $value;
					}
				}

				$niveles[$nivel_modelo->id]['id'] = $nivel_modelo->id;
				$niveles[$nivel_modelo->id]['nombre'] = $nivel_modelo->nombre;
				$niveles[$nivel_modelo->id]['tabla'] = $nivel_modelo->tabla;
				$niveles[$nivel_modelo->id]['campos'] = $campos;

				if ($nivel_modelo->dependencia_id) {
					$niveles[$nivel_modelo->id]['dependencia_id'] = $nivel_modelo->dependencia_id;
				} else {
					$niveles[$nivel_modelo->id]['dependencia_id'] = false;
				}
			}
		}

		return $niveles;
	}

	private function filtrar_contenido_post()
	{
		$ajax = false;
		if (strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') == 'xmlhttprequest') {
			$ajax = true;
		}

		if (isset($_POST) && !empty($_POST)) {
			if (isset($_SESSION['userdata']) && !$ajax) {
				$form_token = false;
				if (isset($_POST['form_token'])) {
					$form_token = $_POST['form_token'];
				}

				if (!$form_token || $form_token !== $_SESSION['form_token']) {
					log_message('error', 'Error 405 obtenido, datos del post: ' . json_encode($_POST) . '. Datos de la session.' . json_encode($_SESSION));

					if (!isset($_POST['image'])) {
						header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
						exit;
					} else {
						return true;
					}
				}
			}

			foreach ($_POST as $key => $value) {
				if (is_string($value)) {
					$nuevo_texto = '';
					$i = 0;
					//Mientras este recorriendo y sea vacio lo salto
					while ($i < strlen($value)) {
						if ($value[$i] != ' ') {
							$nuevo_texto .= $value[$i];
						} else {
							//Si no esta vacío si puedo insertar espacios
							if ($nuevo_texto != '') {
								$nuevo_texto .= $value[$i];
							}
						}

						$i++;
					}

					$text = $nuevo_texto;

					if (strlen($text) > 10) {
						$text = strip_tags(trim($text));
					}

					$text = ltrim($text, '"');
					$text = rtrim($text, '"');
					$content = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $text);
					$_POST[$key] = $content;
					$_POST[$key] = rtrim($_POST[$key], '"');
					$_POST[$key] = ltrim($_POST[$key], '"');
				}
			}

			$url = $this->obtener_url_completa();

			$user_id = $this->get_user_id();
			if ($user_id) {
				if (!$this->generic->check_field_exist("url", "log_posts")) {
					$query_update = "ALTER TABLE log_posts ADD COLUMN url TEXT";
					$this->generic->run_query($query_update);
				}

				//Salto el acceso por post de scripts
				if (isset($_POST['script']) && count($_POST) == 1) {
					return true;
				}

				// //No logueo contraseñas
				// if(isset($_POST['password'])){
				//     unset($_POST['password']);
				// }
				// if(isset($_POST['psw'])){
				//     unset($_POST['psw']);
				// }
				// if(isset($_POST['repsw'])){
				//     unset($_POST['repsw']);
				// }

				// $log_data = array(
				//     "user_id" => $this->get_user_id(),
				//     "url" => $url,
				//     "post" => json_encode($_POST)
				// );

				// $this->generic->save_on_table("log_posts", $log_data);
			}
		}
	}

	private function filtrar_contenido_get()
	{
		if (isset($_GET) && !empty($_GET)) {
			foreach ($_GET as $key => $value) {
				if (is_string($value)) {
					$value = trim($value);
					$text = $value;
					if (strlen($text) > 10) {
						$text = strip_tags(trim($text));
					}
					$text = ltrim($text, '"');
					$text = rtrim($text, '"');
					$content = preg_replace("/&#?[a-z0-9]{2,8};/i", "", $text);
					$_GET[$key] = $content;
					$_GET[$key] = rtrim($_GET[$key], '"');
					$_GET[$key] = ltrim($_GET[$key], '"');
				}
			}
		}

		return true;
	}

	protected function cargar_vista($datos_adicionales)
	{
		//Agrego los datos que vienen del controlador
		foreach ($datos_adicionales as $parametro => $valor) {
			if (!isset($this->datos_vista[$parametro])) {
				$this->datos_vista[$parametro] = $valor;
			}
		}

		if ($this->es_usuario_interno()) {
			$this->datos_vista['permisos_tabla_activos'] = $this->get_variable("permisos_tabla_activos");

			//Cargo la masterview con los datos recibidos
			$this->load->view('master_view', $this->datos_vista);
		} else {
			$this->load->view('master_view_externos', $this->datos_vista);
		}
	}

	private function cargar_traducciones()
	{
		$default_lang = $this->get_variable("default_lang");

		if ($default_lang) {
			if (!isset($_SESSION['LANG'])) {
				$datos_lenguaje = $this->generic->get_row_from_table("lenguajes", array("id" => $default_lang));
				$_SESSION['LANG'] = $default_lang;
				$_SESSION['LANG_COD'] = $datos_lenguaje->codigo;
			}

			$lenguajes_disponibles = $this->generic->get_from_table("lenguajes", false, "codigo asc");
			$_SESSION['lenguajes_disponibles'] = $lenguajes_disponibles;
		} else {
			//Si la variable no esta seteada el default es siempre 1 y no verifico el lenguaje de usuario
			$_SESSION['LANG'] = 1;
			$_SESSION['LANG_COD'] = 'ES';
		}

		$this->datos_vista['stringkey'] = array();
		$existe_tabla = $this->generic->check_table("lenguajes_traduccion");
		if ($existe_tabla) {
			$traducciones = $this->generic->get_from_table("lenguajes_traduccion", array("lenguaje_id" => $_SESSION['LANG']));
			if (!empty($traducciones)) {
				foreach ($traducciones as $traduccion) {
					if (!isset($this->datos_vista['stringkey'][$traduccion->string_key])) {
						$this->datos_vista['stringkey'][$traduccion->string_key] = $traduccion->texto;
					}
				}
			}
		}

		return true;
	}

	public function get_traduccion($stringkeys, $string_key_search)
	{
		if (isset($stringkeys[$string_key_search])) {
			return $stringkeys[$string_key_search];
		} else {
			return "";
		}
	}

	public function cargar_lenguaje_usuario($user_id = false)
	{
		//Obtengo variable de lenguaje default
		$default_lang = $this->get_variable("default_lang");

		if ($default_lang) {
			if ($user_id) {
				//Obtengo datos del usuario
				$datos_usuario = $this->generic->get_row_from_table("usuarios", array("id" => $user_id));
				$datos_lenguaje_usuario = $this->generic->get_row_from_table("lenguajes", array("id" => $datos_usuario->lenguaje_default));
			}

			if (isset($datos_lenguaje_usuario) && $datos_lenguaje_usuario) {
				$_SESSION['LANG'] = $datos_lenguaje_usuario->id;
				$_SESSION['LANG_COD'] = $datos_lenguaje_usuario->codigo;
			} else {
				$datos_lenguaje_default = $this->generic->get_row_from_table("lenguajes", array("id" => $default_lang));
				$_SESSION['LANG'] = $default_lang;
				$_SESSION['LANG_COD'] = $datos_lenguaje_default->codigo;
			}
		} else {
			//Si la variable no esta seteada el default es siempre 1 y no verifico el lenguaje de usuario
			$_SESSION['LANG'] = 1;
			$_SESSION['LANG_COD'] = 'ES';
		}
	}

	// public function get_traducciones($module_id){
	//     define('LANG',1);
	//     $traducciones = array();
	//     $strings = $this->generic->get_from_table("languages_strings",array("language_id" => LANG,"module_id" => $module_id));

	//     if(!empty($strings)){
	//         foreach($strings as $traduccion){
	//             $traducciones[$traduccion->string_key] = $traduccion->texto;
	//         }
	//     }

	//     return $traducciones;
	// }

	protected function check_api_key($api_key)
	{
		$clave = "4l3phm4n4g3r";
		$fecha = date('Y-m-d');

		$clave .= $fecha;
		$clave = sha1($clave);

		if ($api_key == $clave) {
			return true;
		} else {
			return false;
		}
	}

	protected function generate_api_key()
	{
		// //Agrego las paginas excluidas al control de login
		// $excluir = array('api','API','service_now','usuarios/reset_password','guardar_seguimiento_control_proveedor');
		// $verifica_login = true;

		// foreach($excluir as $pagina){
		//     if(strpos($_SERVER['REQUEST_URI'], $pagina) !== false) {
		//         $verifica_login = false;
		//     }
		// }

		// //Si hay session de un usuario externo no se verifica el login
		// if(isset($_SESSION['externo'])){
		//     $verifica_login = false;
		// }

		// if($verifica_login){
		//     $this->verify_logged_in();
		// }

		$clave = "4l3phm4n4g3r";
		$fecha = date('Y-m-d');

		$clave .= $fecha;
		$clave = sha1($clave);

		return $clave;
	}

	protected function check_api_credentials()
	{
		if (isset($_POST['api_key'])) {
			$datos_usuario = $this->generic->get_row_from_table("usuarios", array("api_key" => $_POST['api_key']));

			if ($datos_usuario) {
				if ($datos_usuario->habilita_api == 1) {
					return true;
				} else {
					return false;
				}
			} else {
				//Si no recupera usuario verifico si tiene api key de aleph
				if ($this->check_api_key($_POST['api_key'])) {
					return true;
				} else {
					return false;
				}
			}
		} else {
			header("HTTP/1.1 403");
			exit();
		}
	}

	protected function get_tipo_session()
	{
		if ($this->es_usuario_interno()) {
			return 'interno';
		}

		if ($this->es_usuario_externo()) {
			return 'externo';
		}

		return false;
	}

	// Para validar si es usuario interno pregunto por el username
	protected function es_usuario_interno()
	{
		if (isset($_SESSION['userdata']['username']) && $_SESSION['userdata']['username'] != '' || $this->is_root()) {
			return true;
		} else {
			return false;
		}
	}

	// Para validar si es usuario externo pregunto por el rol
	protected function es_usuario_externo()
	{
		if (isset($_SESSION['userdata']['rol']) && $_SESSION['userdata']['rol'] == 'Externo') {
			return true;
		} else {
			return false;
		}
	}

	// Verifica si el usuario está logueado y es un usuario interno
	public function verify_logged_in($redirigir = true)
	{
		// Detectar si la solicitud es AJAX
		$ajax = strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest';

		// Verificar si el usuario está logueado y es un usuario interno
		if ($this->is_logged_in() && $this->es_usuario_interno()) {
			return true;
		}

		// Si la solicitud es AJAX y se debe redirigir, devolver un mensaje de error y detener la ejecución
		if ($ajax) {
			if ($redirigir) {
				echo "Sesión de usuario caducada";
				exit;
			}
			return false;
		}

		// Si no es una solicitud AJAX y se debe redirigir, guardar la URL de retorno y redirigir a la página de login
		if ($redirigir) {
			$_SESSION['callback_url'] = base_url() . $_SERVER['REQUEST_URI'];
			redirect(base_url('usuarios/login'));
		} else {
			return false;
		}
	}

	public function asignar_permiso()
	{
		//Verifico que este logueado y que sea usuario interno
		if ($this->is_logged_in() && $this->es_usuario_interno()) {
			return true;
		} else {
			$_SESSION['callback_url'] = base_url() . $_SERVER['REQUEST_URI'];
			redirect(base_url('usuarios/login'));
			die();
		}
	}

	public function is_logged_in()
	{
		if (isset($_SESSION['userdata']) && $_SESSION['userdata'] != NULL) {
			return true;
		} else {
			return false;
		}
	}

	public function get_user_data()
	{
		return $_SESSION['userdata'];
	}

	public function get_username()
	{
		if (isset($_SESSION['userdata']['username'])) {
			return $_SESSION['userdata']['username'];
		} else {
			return "Alephmanager";
		}
	}

	public function get_user_id()
	{
		if (isset($_SESSION['userdata']['user_id'])) {
			return $_SESSION['userdata']['user_id'];
		} else {
			return false;
		}
	}

	public function has_permission($permission_id, $ajax = false, $redirect = true)
	{
		$this->ip_allowed();

		//Pregunto primero si esta logueado
		if ($this->is_logged_in() && $this->es_usuario_interno()) {
			$this->load->model("Configuracion_model", "configuracion");

			$permiso = $this->configuracion->get_permiso($permission_id);

			if (!$permiso) {
				return true;
			}

			$roles = array();
			if (isset($_SESSION['userdata']['roles_id'])) {
				$roles = $_SESSION['userdata']['roles_id'];
			} else {
				$roles[] = $_SESSION['userdata']['rol_id'];
			}

			//Pregunto si es root
			if (in_array('9999', $roles)) {
				return true;
			} else {
				$tiene_permiso = false;

				foreach ($roles as $rol_id) {
					$result = $this->configuracion->check_permission($permission_id, $rol_id);

					if ($result && $result->habilitado == 1) {
						$tiene_permiso = true;
					}
				}

				if ($tiene_permiso) {
					return true;
				} else {
					//Busco el nombre del permiso, seccion y modulo para indicar que setear al usuario
					$nombre_permiso = $permiso->nombre;
					$nombre_permiso_padre = "Ocurrió un error al recuperar";
					$nombre_modulo = "Ocurrió un error al recuperar";

					$permiso_padre = $this->generic->get_row_from_table("permisos_padres", array("id" => $permiso->permiso_padre_id));
					if ($permiso_padre) {
						$nombre_permiso_padre = $permiso_padre->nombre;
						$datos_modulo = $this->generic->get_row_from_table("modulos", array("id" => $permiso_padre->modulo_id));
						if ($datos_modulo) {
							$nombre_modulo = $datos_modulo->nombre;
						}
					}

					$descripcion_permiso = 'Detalle del permiso: Módulo "' . $nombre_modulo . '" - Sección "' . $nombre_permiso_padre . '" - Permiso "' . $nombre_permiso . '"';

					if ($ajax) {
						echo "No tienes permiso para realizar esta acción, contáctese con un administrador. " . $descripcion_permiso;
						header("HTTP/1.0 405 Not Allowed");
						exit();
					} else {
						if ($redirect) {
							// $this->mostrar_error('No tienes permiso para realizar esta acción<br>'.$descripcion_permiso);
							$data = array(
								'view' => 'errors/permiso_denegado',
								'descripcion_permiso' => $descripcion_permiso,
								'breadcumb_deshabilitado' => 'Permiso denegado',
								'breadcumb_habilitado' => array('Inicio' => base_url('dashboard'))
							);
							$this->cargar_vista($data);
						} else {
							return false;
						}
					}
				}
			}
		} else {
			if ($ajax) {
				echo "No tienes permiso para realizar esta acción o te encuentras deslogueado, recarga la página y vuelva a intentarlo, si este error persiste contáctese con un administrador.";
				header("HTTP/1.0 405 Not Allowed");
				exit();
			} else {
				$_SESSION['callback_url'] = base_url() . $_SERVER['REQUEST_URI'];
				redirect(base_url('usuarios/login'));
			}
		}
	}

	public function validar_root()
	{
		if ($this->is_root()) {
			return true;
		} else {
			$this->mostrar_error();
		}
	}

	public function is_root()
	{
		if (isset($_SESSION['userdata']) && isset($_SESSION['userdata']['roles_id']) && in_array('9999', $_SESSION['userdata']['roles_id'])) {
			return true;
		} else {
			return false;
		}
	}

	public function es_auditor()
	{
		$this->verify_logged_in();

		$roles = array();
		if (isset($_SESSION['userdata']['roles_id'])) {
			$roles = $_SESSION['userdata']['roles_id'];
		} else {
			if (isset($_SESSION['userdata']['rol_id'])) {
				$roles[] = $_SESSION['userdata']['rol_id'];
			}
		}

		$tiene_permiso = false;
		foreach ($roles as $rol_id) {
			$result = $this->configuracion->check_permission(346, $rol_id);

			if ($result && $result->habilitado == 1) {
				$tiene_permiso = true;
			}
		}

		return $tiene_permiso;
	}


	public function pantalla_guardado($mensaje = false)
	{
		$data = array(
			'view' => 'paginas/formulario_completado',
			'page' => 'Formulario Completado'
		);

		if ($mensaje && $mensaje != '') {
			$data['mensaje'] = $mensaje;
		} else {
			if (isset($_SESSION['success_message'])) {
				$data['mensaje'] = $_SESSION['success_message'];
				unset($_SESSION['success_message']);
			}
		}

		$this->cargar_vista($data);
	}

	public function guardar_log($mensaje, $user_id = false)
	{
		if ($mensaje != '') {
			$this->load->model("Configuracion_model", "configuracion");

			if ($user_id) {
				$usuario_id = $user_id;
			} else {
				$usuario_id = $_SESSION['userdata']['user_id'];
			}

			if ($usuario_id != 0) {
				if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
					$IP_LOG = $_SERVER['HTTP_CF_CONNECTING_IP'];
				} else {
					$IP_LOG = $_SERVER['REMOTE_ADDR'];
				}

				$data = array(
					'usuario_id' => $usuario_id,
					'detalle' => $mensaje,
					'ip' => $IP_LOG,
					'fecha' => date('Y-m-d H:i:s')
				);

				$this->configuracion->guardar_log_accion($data);
			}
		} else {
			$this->reportar_error("Falta asignar un mensaje para guardar en el log de acciones.");
			return false;
		}
	}

	public function es_ambiente_local()
	{
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] == 'aleph.localhost') {
			return true;
		} else {
			return false;
		}
	}

	public function enviar_email($to, $content, $subject, $api = false, $generar_notificacion = true)
	{
		$emails_aleph = array(
			'solllanes18@gmail.com',
			'adrianclaret@gmail.com',
			'adrianclaret@yafoconsultora.com',
			'aclaret1@yafoconsultora.com',
			'aclaret2@yafoconsultora.com',
			'aclaret3@yafoconsultora.com',
			'martingorbik@yafoconsultora.com',
			'soporte@alephmanager.com',
			'marting196@gmail.com'
		);

		$whitelist = array(
			'Marcelo.LeRose@comafi.com.ar'
		);

		if (in_array($to, $emails_aleph)) {
			$habilita_notificaciones_demo = true;
		} else {
			$habilita_notificaciones_demo = false;
		}

		if (!$api) {
			//Si no es por API, siendo root solo notifica a mis correos
			if ($this->is_root() && !$habilita_notificaciones_demo) {
				return true;
			}
		}

		//Si estoy en ambiente local omito el envio de notificaciones
		if ($this->es_ambiente_local() && !$habilita_notificaciones_demo) {
			return true;
		}

		//Verifico el usuario, si esta deshabilitado directamente no hago nada
		if (!is_array($to)) {
			$datos_usuario = $this->generic->get_row_from_table("usuarios", array("email" => $to));
			if ($datos_usuario && $datos_usuario->enabled == 0) {
				//Pregunto si no esta en la whitelist
				if (!in_array($datos_usuario->email, $whitelist)) {
					return false;
				}
			}
		}

		//Verifico si utiliza las notificaciones locales
		$notificaciones_locales = $this->get_variable('notificaciones_locales');
		$notificaciones_email = $this->get_variable('notificaciones_email');
		$notificaciones_email_default = $this->get_variable('notificaciones_email_default');
		$notificaciones_email_config = $this->get_variable('notificaciones_email_config');
		$notificaciones_email_from = $this->get_variable('notificaciones_email_from');
		$notificaciones_email_from_name = $this->get_variable('notificaciones_email_from_name');

		if ($this->es_usuario_externo()) {
			$notificaciones_locales = false;
		}

		if ($generar_notificacion && $notificaciones_locales == 1) {
			$user_id = $this->get_user_id();

			if (isset($datos_usuario) && $datos_usuario) {
				$notificacion = array(
					'user_id' => $datos_usuario->id,
					'mensaje' => $content,
					'asunto' => $subject
				);

				if ($user_id) {
					$notificacion['user_emisor_id'] = $user_id;
					$this->generic->save_on_table("notificaciones", $notificacion);
				} else {
					$this->generic->save_on_table("notificaciones", $notificacion);
				}
			}
		}

		if ($notificaciones_email == 0) {
			return true;
		} else {
			//Verifico si usa configuracion default
			if ($notificaciones_email_default) {
				$api_key = $this->generate_api_key();

				$post_fields = array(
					'api_key' => $api_key,
					'to' => $to,
					'content' => $content,
					'subject' => $subject,
					'base_url' => base_url()
				);


				if (defined('URL_API_EMAILS')) {
					$url_api_emails = URL_API_EMAILS;
				} else {
					$url_api_emails = 'https://panel.alephmanager.com/API/send_email';
				}

				$ch = curl_init();
				curl_setopt($ch, CURLOPT_URL, $url_api_emails);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_fields));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

				//Guardo el email antes de enviarlo
				$id_log_email = $this->generic->save_on_table("log_emails", array("email" => $to, "subject" => $subject));

				//Ejecuto la llamada API
				$server_output = curl_exec($ch);

				curl_close($ch);

				log_message('error', 'Registro respuesta del panel por emails: ' . json_encode($server_output));

				$respuesta = json_decode($server_output);

				if ($respuesta && isset($respuesta->status) && $respuesta->status == 1) {
					//Actualizo el log
					$this->generic->update("log_emails", array("id" => $id_log_email), array("enviado" => 1));
					return true;
				} else {
					return false;
				}
			} else {
				$configs = json_decode($notificaciones_email_config);

				foreach ($configs as $key => $config) {
					$mail_config[$key] = $config;
				}

				$from = $notificaciones_email_from;
				$from_name = $notificaciones_email_from_name;

				$this->load->library('email', $mail_config);
				$this->email->from($from, $from_name);
				$this->email->set_newline("\r\n");
				$this->email->to($to);
				$this->email->subject($subject);

				$body_email = $this->load->view('layaout/email_template.php', array('content' => $content), TRUE);
				$this->email->message($body_email);

				//Guardo el email antes de enviarlo
				$id_log_email = $this->generic->save_on_table("log_emails", array("email" => $to, "subject" => $subject));

				if ($this->email->send()) {
					//Actualizo el log
					$this->generic->update("log_emails", array("id" => $id_log_email), array("enviado" => 1));
					return true;
				} else {
					return false;
				}
			}
		}
	}

	public function send_email_example()
	{
		$config = array(
			'protocol' => 'tls',
			'smtp_host' => 'mail.alephmanager.com',
			'smtp_port' => 465,
			'smtp_user' => 'info@alephmanager.com',
			'smtp_pass' => 'hustle2006',
			'smtp_timeout' => '4',
			'mailtype'  => 'html',
			'charset'   => 'utf-8',
			'wordwrap' => TRUE
		);
		$this->load->library('email', $config);
		$this->email->set_newline("\r\n");

		$this->email->from('info@alephmanager.com', 'Aleph Manager');
		$this->email->to('adrianclaret@gmail.com');

		$this->email->subject('Email Test');
		$this->email->message('Testing the email class.');

		if ($this->email->send()) {
			//TODO: load view...
			echo "email sent";
		} else {
			$to = $this->input->post('email');
			mail($to, 'test', 'Other sent option failed');
			echo $this->input->post('email');
			show_error($this->email->print_debugger());
		}
	}

	public function is_admin()
	{
		$this->load->model("Usuario_model", "usuarios");

		if (in_array(1, $_SESSION['userdata']['roles_id']) || $this->is_root()) {
			return true;
		} else {
			return false;
		}
	}

	public function verify_admin()
	{
		if ($this->is_admin()) {
			return true;
		} else {
			redirect(base_url('dashboard/permiso_denegado'));
		}
	}

	public function verify_admin_ajax()
	{
		if ($this->is_admin()) {
			return true;
		} else {
			echo "No tienes permiso para realizar esta acción, contáctese con un administrador.";
			exit();
		}
	}

	public function get_user_email()
	{
		if (isset($_SESSION['userdata']['email'])) {
			return $_SESSION['userdata']['email'];
		} else {
			return false;
		}
	}

	public function generar_random($longitud)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@$*()_';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $longitud; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function generar_random_file_name($longitud)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $longitud; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}

	public function existe_variable($nombre)
	{
		$variable = $this->generic->get_row_from_table("variables", array("nombre" => $nombre));

		if ($variable) {
			return true;
		} else {
			return false;
		}
	}

	public static function get_variable($nombre)
	{
		$myController = new MyController();
		$variable = $myController->get_variable($nombre);

		if ($variable) {
			return $variable->valor;
		} else {
			return false;
		}
	}

	public function has_rol($rol_id)
	{
		$roles = array();
		if (isset($_SESSION['userdata']['roles_id'])) {
			$roles = $_SESSION['userdata']['roles_id'];
		} else {
			$roles[] = $_SESSION['userdata']['rol_id'];
		}

		//Pregunto si tiene el rol o es root
		if (in_array($rol_id, $roles)) {
			return true;
		} else {
			return false;
		}
	}

	public function has_rol_user($rol_id_busqueda, $user_id)
	{
		$tiene_rol = false;

		$roles_usuario = $this->generic->get_from_table('usuarios_x_rol', array('user_id' => $user_id));

		if (!empty($roles_usuario)) {
			foreach ($roles_usuario as $rol) {
				if ($rol->rol_id == $rol_id_busqueda) {
					$tiene_rol = true;
				}
			}
		}

		return $tiene_rol;
	}

	public function set_variable($nombre, $valor)
	{
		$variable = $this->generic->set_variable($nombre, $valor);
		return $variable;
	}

	public function pantalla_error()
	{
		$data = array(
			'view' => 'error',
			'page' => 'Page error'
		);
		$this->cargar_vista($data);
	}

	public function mostrar_error($mensaje_error = false, $url_anterior = false, $ajax = false)
	{
		if ($ajax) {
			if ($mensaje_error) {
				echo $mensaje_error;
				return false;
				die();
			} else {
				echo "Ocurrió un error interno, recargue la página e intente nuevamente, si este error persiste contáctese con soporte@alephmanager.com";
				return false;
				die();
			}
		}

		$data = array(
			'back_url' => base_url('dashboard'),
			'view' => 'paginas/error',
			'menu_item' => 'inicio',
			'mensaje_error' => $mensaje_error,
			'url_anterior' => $url_anterior,
			'page' => 'Page error'
		);

		if ($this->is_logged_in()) {
			$this->cargar_vista($data);
		} else {
			$this->load->view('master_view_externos', $data);
		}
	}

	public function chequear_modulo_habilitado($nombre_modulo, $mensaje_error = false, $url_anterior = false, $ajax = false)
	{
		if ($this->is_root()) {
			return true;
		}

		$this->verify_logged_in();

		$modulo = $this->generic->get_row_from_table("modulos", array("nombre" => $nombre_modulo));
		$usuarios = $this->generic->get_row_from_table("usuarios", array("username" => $this->get_username()));

		if ($modulo->estado == 0) {
			$data = array(
				'view' => 'paginas/sin_licencia',
				'menu_item' => 'inicio',
				'nombre_modulo' => $nombre_modulo,
				'mensaje_error' => $mensaje_error,
				'url_anterior' => $url_anterior,
				'usuarios' => $usuarios,
				'page' => 'Page error'
			);
			$this->cargar_vista($data);
			/* $this->load->view('master_view_externos',$data); */
		}
	}

	protected function obtener_url_completa()
	{
		if (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'http') {
			$host = "http://";
		} else {
			$host = "https://";
		}

		$uri = "";
		if (isset($_SERVER['HTTP_HOST']) && $_SERVER['HTTP_HOST'] != '') {
			$uri = $_SERVER['HTTP_HOST'];
		}

		$request_uri = "";
		if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] != '') {
			$request_uri = $_SERVER['REQUEST_URI'];
		}

		$url = $host . $uri . $request_uri;

		return $url;
	}

	public function reportar_error($mensaje_error)
	{
		$this->load->model("Configuracion_model", "configuracion");

		$ip = "";
		if (isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] != '') {
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}

		$url_completa = $this->obtener_url_completa();

		if (isset($_SESSION['userdata']['email'])) {
			$usuario_email = $_SESSION['userdata']['email'];
		} else {
			$usuario_email = "Usuario no logueado";
		}

		$usuario_id = "";
		if (isset($_SESSION['userdata']['user_id'])) {
			$usuario_id = $_SESSION['userdata']['user_id'];
		}

		$data = array(
			'usuario_id' => $usuario_id,
			'usuario_email' => $usuario_email,
			'detalle' => $mensaje_error,
			'url' => $url_completa,
			'ip' => $ip
		);
		$this->configuracion->guardar_log_errores($data);

		$html_error = "<h4>Ah ocurrido un error de acceso<h4>";
		$html_error .= "<span>Detalle: " . $mensaje_error . "</span>";
		$html_error .= "<br><span>URL: " . $url_completa . "</span>";
		$html_error .= "<br><span>IP: " . $ip . "</span>";
		$html_error .= "<br><span>Email usuario: " . $usuario_email . "</span>";

		$this->enviar_email("soporte@alephmanager.com", $html_error, "Alerta de error autogenerada en " . $url_completa);
	}

	public function show_last_query()
	{
		echo $this->db->last_query();
		die();
	}

	public function is_valid_date($string)
	{
		$matches = array();
		$pattern = '/^([0-9]{1,2})\\/([0-9]{1,2})\\/([0-9]{2})$/';
		if (!preg_match($pattern, $string, $matches)) return false;
		if (!checkdate($matches[2], $matches[1], $matches[3])) return false;
		return true;
	}

	public function return_date_from_yyyymmdd($date_string)
	{
		if (is_numeric($date_string) && strlen($date_string) == 8) {
			$year = substr($date_string, 0, 4);
			$month = substr($date_string, 4, 2);
			$day = substr($date_string, 6, 2);

			if (checkdate($month, $day, $year)) {
				return $year . "-" . $month . "-" . $day;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public function delete_table($table_name)
	{
		$this->generic->delete_table($table_name);
		return true;
	}

	public function reset_table($table_name)
	{
		$this->generic->reset_table($table_name);
		return true;
	}

	public function guardar_registro($table, $data)
	{
		$this->generic->save_on_table($table, $data);
	}

	public function ip_allowed()
	{
		$whitelist = $this->get_variable("whitelist_ip");

		if ($whitelist && $whitelist == 1) {
			$ips = array(
				'::1',
				''
			);

			if (in_array($_SERVER['REMOTE_ADDR'], $ips)) {
				return true;
			} else {
				header("HTTP/1.1 403 Forbidden");
				exit;
			}
		} else {
			return true;
		}
	}

	public function get_columnas_excel()
	{
		$columna = 'A';
		$columnas = array();

		$columnas[] = $columna;
		for ($i = 0; $i <= 52; $i++) {
			$columna++;
			$columnas[] = $columna;
		}

		return $columnas;
	}

	public function get_position_excel_by_letra($letra)
	{
		$letra = strtoupper($letra);
		$valor = 'A';
		$posicion = 0;

		while ($letra != $valor) {
			$posicion++;

			if ($posicion > 52) {
				return false;
			}
			$valor++;
		}

		return $posicion;
	}

	public function get_columna_excel_by_numero($numero)
	{
		$columna = 'A';

		$cont = 1;
		while ($cont < $numero) {
			$columna++;
			$cont++;
		}

		return $columna;
	}

	public function catalogo_asignado_it($catalogo_id)
	{
		if ($this->generic->get_row_from_table("analisis_riesgo", array("catalogo_id" => $catalogo_id))) {
			return true;
		} else {
			return false;
		}
	}

	public function validar_nombre_campo($string)
	{
		$length = strlen($string);

		for ($i = 0; $i < $length; $i++) {

			$caracter_ascii = ord($string[$i]);

			if (($caracter_ascii >= 48 && $caracter_ascii <= 57) || ($caracter_ascii >= 65 && $caracter_ascii <= 90) || ($caracter_ascii >= 97 && $caracter_ascii <= 122) || $caracter_ascii == 32 || $caracter_ascii == 95) {
				continue;
			} else {
				return false;
			}
		}

		return true;
	}

	public function generar_path_exportacion($modulo, $nombre_archivo)
	{
		//Como puede generar muchos reportes al mismo tiempo agrego caracteres especiales
		$time = time();

		$modulos_validos = array(
			'arbol_dependencias',
			'base_eventos',
			'base_incidentes',
			'bias',
			'canales',
			'clasificacion',
			'compliance',
			'dependencias',
			'modelo_negocio',
			'service_now',
			'servicios_decentralizados',
			'ro',
			'rit',
			'sti'
		);

		$carpeta_modulo = ""; //Por defecto va a la carpeta root de export
		if (in_array($modulo, $modulos_validos)) {
			//Si la carpeta no existe la creo.
			$path_carpeta = EXPORT_FOLDER . '/reportes/' . $modulo;
			if (!file_exists($path_carpeta)) {
				mkdir($path_carpeta, 0775, true);
			}
			$carpeta_modulo = "reportes/" . $modulo . "/";
		}

		// Reemplazar espacios y caracteres no deseados por guiones bajos
		$nombre_archivo = preg_replace('/[^\w\-\.]/', '_', $nombre_archivo);

		// Eliminar múltiples guiones bajos seguidos
		$nombre_archivo = preg_replace('/_+/', '_', $nombre_archivo);

		// Eliminar guiones bajos al principio y al final
		$nombre_archivo = trim($nombre_archivo, '_');

		$extension = pathinfo($nombre_archivo)['extension'];
		$nombre_archivo = pathinfo($nombre_archivo)['filename'];

		$nombre_archivo = substr($nombre_archivo, 0, 120); //Obtengo como limite 120 caracteres para el nombre del archivo

		$file_name = $nombre_archivo . '_' . $time . '.' . $extension;
		$path = array(
			'path' => EXPORT_FOLDER . $carpeta_modulo . $file_name,
			'url' => EXPORT_URL . $carpeta_modulo . $file_name,
			'nombre_archivo' => $file_name
		);

		return $path;
	}

	public function limpiar_renglon_importacion($fila)
	{
		if ($fila && !empty($fila)) {
			foreach ($fila as $key => $columna) {
				$fila[$key] = $this->filtrar_caracteres_importacion($columna);
			}

			return $fila;
		} else {
			return false;
		}
	}

	public function filtrar_caracteres_importacion($original_string)
	{
		$string = trim($original_string);
		$string = preg_replace('/[\x00-\x1F\x7F]/u', '', $string);
		$string = str_replace("&#8203;", "", $string);
		$string = str_replace("\xE2\x80\x8C", "", $string);
		$string = str_replace("\xE2\x80\x8B", "", $string);
		$string = trim($string);
		$string = ltrim($string, '"');
		$string = rtrim($string, '"');

		$new_string = "";
		$chars = str_split($string);
		foreach ($chars as $char) {
			$CharNo = ord($char);
			if ($CharNo > 31 && $CharNo < 256) {
				// if ($CharNo > 31 && $CharNo < 127) {
				$new_string .= $char;
			}
		}

		return $new_string;
	}

	public function check_ip_whitelist($ip)
	{
		if ($ip) {
			if ($ip == '::1') {
				return true;
			}

			$existe_ip = $this->generic->get_row_from_table("whitelist", array("ip" => trim($ip)));

			if ($existe_ip) {
				return true;
			} else {
				$all_whitelist = $this->generic->get_from_table("whitelist");

				if (!empty($all_whitelist)) {
					$ip_en_rango = false;
					foreach ($all_whitelist as $ip_whitelist) {
						$ip_con_rango = strpos($ip_whitelist->ip, "/");

						if ($ip_con_rango !== FALSE) {
							$ip_separada = explode('.', $ip_whitelist->ip);

							if (isset($ip_separada[0]) && isset($ip_separada[1]) && isset($ip_separada[2])) {
								$ip_inicio = $ip_separada[0] . "." . $ip_separada[1] . "." . $ip_separada[2] . ".";
								if (isset($ip_separada[3])) {
									$rango = explode('/', $ip_separada[3]);

									if (isset($rango[0]) && isset($rango[1])) {
										$rango_inicio = (int)$rango[0];
										$rango_hasta = (int)$rango[1];

										while ($rango_inicio <= $rango_hasta) {
											$ip_completa = $ip_inicio . $rango_inicio;
											if ($ip == $ip_completa) {
												$ip_en_rango = true;
												$rango_inicio = $rango_hasta;
											}
											$rango_inicio++;
										}
									}
								}
							}
						}
					}

					return $ip_en_rango;
				} else {
					return false;
				}
			}
		} else {
			return false;
		}
	}

	public function copy_dir($src, $dst)
	{
		//Open the source directory
		$dir = opendir($src);

		//Make the destination directory if not exist
		@mkdir($dst);

		// Loop through the files in source directory
		while ($file = readdir($dir)) {

			if (($file != '.') && ($file != '..')) {
				// if ( is_dir($src . '/' . $file) ){
				//     custom_copy($src . '/' . $file, $dst . '/' . $file);
				// }else{
				copy($src . '/' . $file, $dst . '/' . $file);
				// }
			}
		}

		closedir($dir);
	}

	public function json_object_to_array($json_object)
	{
		$array = array();

		if ($json_object && $json_object != '') {
			$json_decoded = json_decode($json_object);

			foreach ($json_decoded as $key => $value) {
				if (is_object($value)) {
					foreach ($value as $key_2 => $value_2) {
						$array[$key][$key_2] = $value_2;
					}
				} else {
					$array[$key] = $value;
				}
			}
		}

		return $array;
	}

	public function get_datos_basicos_usuario($user_id)
	{
		$datos = $this->usuarios->get_datos_basicos($user_id);
		if ($datos) {
			return $datos;
		} else {
			return false;
		}
	}

	public function buscar_relacion_usuario($usuario, $tabla_usuarios = 'usuarios')
	{
		$this->verify_logged_in();

		if ($usuario && $usuario != '') {
			$where = "CONCAT(nombre, ' ', apellido) = '" . $usuario . "'";
			$existe_usuario = $this->generic->get_row_from_table($tabla_usuarios, $where);

			//Filtro que no este eliminado y que sea un usuario habilitado
			if ($existe_usuario && $existe_usuario->eliminado == 0 && $existe_usuario->enabled == 1) {
				return $existe_usuario->id;
				die();
			}

			$nombres = explode(' ', $usuario);
			$cant_nombre = count($nombres);

			$where_user = array(
				"enabled" => 1,
				"eliminado" => 0
			);

			if ($cant_nombre < 2) {
				//Si es interno busco por el username
				if ($tabla_usuarios == 'usuarios') {
					$where_user["username"] = $nombres[0];
				} else {
					//Si es externo busco por el email
					$where_user["email"] = $nombres[0];
				}

				$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);

				if ($usuario) {
					return $usuario->id;
				} else {
					return 0;
				}
			} else {
				switch ($cant_nombre) {
					case '2':
						$where_user["nombre"] = $nombres[0];
						$where_user["apellido"] = $nombres[1];

						$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);
						if ($usuario) {
							return $usuario->id;
							break;
							die();
						}

						$where_user["nombre"] = $nombres[1];
						$where_user["apellido"] = $nombres[0];

						$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);
						if ($usuario) {
							return $usuario->id;
							break;
							die();
						}
						break;
					case '3':
						$where_user["nombre"] = $nombres[0] . " " . $nombres[1];
						$where_user["apellido"] = $nombres[2];

						$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);
						if ($usuario) {
							return $usuario->id;
							break;
							die();
						}

						$where_user["nombre"] = $nombres[0];
						$where_user["apellido"] = $nombres[1] . " " . $nombres[2];

						$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);
						if ($usuario) {
							return $usuario->id;
							break;
							die();
						}

						$where_user["nombre"] = $nombres[2];
						$where_user["apellido"] = $nombres[0] . " " . $nombres[1];

						$usuario = $this->generic->get_row_from_table($tabla_usuarios, $where_user);
						if ($usuario) {
							return $usuario->id;
							break;
							die();
						}
						break;
					default:
						return 0;
						break;
				}
			}
		} else {
			return 0;
		}
	}

	public function get_usuarios()
	{
		$campos_select = "id, nombre, apellido, email, username, habilita_api, rol_id, area_id, ultimo_login";
		$usuarios = $this->generic->get_from_table("usuarios", array('eliminado' => 0, 'enabled' => 1), "nombre asc, apellido asc", false, $campos_select);
		return $usuarios;
	}

	public function get_usuarios_externos()
	{
		$campos_select = "id, nombre, apellido, email, ultimo_login";
		$usuarios = $this->generic->get_from_table("usuarios_externos", array('eliminado' => 0, 'enabled' => 1), "nombre asc, apellido asc", false, $campos_select);
		return $usuarios;
	}

	public function get_usuario_by_username($username)
	{
		return $this->generic->get_row_from_table("usuarios", array("username" => $username));
	}

	public function get_usuario_by_email($email)
	{
		return $this->generic->get_row_from_table("usuarios", array("email" => $email));
	}

	public function eliminar_caracteres_invalidos($string)
	{
		//Reemplazo los acentos
		$string = $this->reemplazar_acentos($string);

		$length = strlen($string);

		for ($i = 0; $i < $length; $i++) {

			$caracter_ascii = ord($string[$i]);

			if (($caracter_ascii >= 48 && $caracter_ascii <= 57) || ($caracter_ascii >= 65 && $caracter_ascii <= 90) || ($caracter_ascii >= 97 && $caracter_ascii <= 122) || $caracter_ascii == 32 || $caracter_ascii == 95) {
				continue;
			} else {
				$string[$i] = ' ';
			}
		}

		return $string;
	}

	public function eliminar_caracteres_archivos($string)
	{
		$string = preg_replace("/[^a-z0-9\_\-\.]/i", ' ', $string);
		return $string;
	}

	public function reemplazar_acentos($string)
	{
		$unwanted_array = array(
			'Š' => 'S',
			'š' => 's',
			'Ž' => 'Z',
			'ž' => 'z',
			'À' => 'A',
			'Á' => 'A',
			'Â' => 'A',
			'Ã' => 'A',
			'Ä' => 'A',
			'Å' => 'A',
			'Æ' => 'A',
			'Ç' => 'C',
			'È' => 'E',
			'É' => 'E',
			'Ê' => 'E',
			'Ë' => 'E',
			'Ì' => 'I',
			'Í' => 'I',
			'Î' => 'I',
			'Ï' => 'I',
			'Ñ' => 'N',
			'Ò' => 'O',
			'Ó' => 'O',
			'Ô' => 'O',
			'Õ' => 'O',
			'Ö' => 'O',
			'Ø' => 'O',
			'Ù' => 'U',
			'Ú' => 'U',
			'Û' => 'U',
			'Ü' => 'U',
			'Ý' => 'Y',
			'Þ' => 'B',
			'ß' => 'Ss',
			'à' => 'a',
			'á' => 'a',
			'â' => 'a',
			'ã' => 'a',
			'ä' => 'a',
			'å' => 'a',
			'æ' => 'a',
			'ç' => 'c',
			'è' => 'e',
			'é' => 'e',
			'ê' => 'e',
			'ë' => 'e',
			'ì' => 'i',
			'í' => 'i',
			'î' => 'i',
			'ï' => 'i',
			'ð' => 'o',
			'ñ' => 'n',
			'ò' => 'o',
			'ó' => 'o',
			'ô' => 'o',
			'õ' => 'o',
			'ö' => 'o',
			'ø' => 'o',
			'ù' => 'u',
			'ú' => 'u',
			'û' => 'u',
			'ý' => 'y',
			'þ' => 'b',
			'ÿ' => 'y',
			'Ğ' => 'G',
			'İ' => 'I',
			'Ş' => 'S',
			'ğ' => 'g',
			'ı' => 'i',
			'ş' => 's',
			'ü' => 'u',
			'ă' => 'a',
			'Ă' => 'A',
			'ș' => 's',
			'Ș' => 'S',
			'ț' => 't',
			'Ț' => 'T'
		);


		$string = strtr($string, $unwanted_array);

		return $string;
	}

	public function generar_nombre_campo_db($string)
	{
		$string = $this->filtrar_caracteres_importacion($string);
		$string = $this->reemplazar_acentos($string);
		$string = str_replace(' ', '_', $string);
		$string = strtolower($string);
		$string = substr($string, 0, 60);

		return $string;
	}

	public function validar_fecha($fecha, $separador, $formato)
	{
		$valores = explode($separador, $fecha);
		if (count($valores) == 3) {
			switch ($formato) {
				case 'YYYY-MM-DD':
					$resultado = checkdate($valores[1], $valores[2], $valores[0]);
					break;
				case 'DD-MM-YYYY':
					$resultado = checkdate($valores[1], $valores[0], $valores[2]);
					break;
				case 'YYYY/MM/DD':
					$resultado = checkdate($valores[1], $valores[2], $valores[0]);
					break;
				case 'DD/MM/YYYY':
					$resultado = checkdate($valores[1], $valores[0], $valores[2]);
					break;
				default:
					$resultado = false;
					break;
			}
			return $resultado;
		}
		return false;
	}

	public function formatear_fecha_mysql($fecha)
	{
		if ($fecha != '') {
			$nueva_fecha = "";
			if ($fecha != '0000-00-00 00:00:00') {
				$nueva_fecha = date('Y-m-d', strtotime($fecha));
			}
			return $nueva_fecha;
		} else {
			return false;
		}
	}

	protected function validar_fecha_importacion($fecha)
	{
		$formatos = ['d-m-Y', 'd/m/Y', 'm-d-Y', 'm/d/Y', 'Y-m-d', 'Y/m/d'];

		//Agrego ceros iniciales para tomar formatos
		$fecha = preg_replace('/\b(\d)\b/', '0$1', $fecha);
		foreach ($formatos as $formato) {
			$d = DateTime::createFromFormat($formato, $fecha);
			//Valido fecha
			if ($d && $d->format($formato) === $fecha) {
				return true;
			}
		}

		return false;
	}

	public function screenshot()
	{
		$this->verify_logged_in();
		$image = $_POST["image"];
		$image = explode(";", $image)[1];
		$image = explode(",", $image)[1];
		$image = str_replace(" ", "+", $image);
		$image = base64_decode($image);
		$file_name = $this->generar_path_exportacion('ro', 'Mapa calor residuales.jpg');
		$archivo = $file_name['path'];
		$url_archivo = $file_name['url'];
		file_put_contents($archivo, $image);
		echo $url_archivo;
	}

	public function validar_hoja_recursivo_arriba($nodo_id, $tipo_elemento, $nodo_padre, $tipo_padre, $version_id)
	{
		$this->verify_logged_in();

		//Por defecto es true
		$resultado = true;

		if ($nodo_id == $nodo_padre && $tipo_elemento == $tipo_padre) {
			$resultado = false;
		} else {
			$busqueda_parent = array(
				'nodo_id' => $nodo_padre,
				'tipo_elemento' => $tipo_padre,
				'version_id' => $version_id
			);

			$datos_parent = $this->generic->get_row_from_table("arbol_dependencias", $busqueda_parent);
			if ($datos_parent) {
				if ($this->validar_hoja_recursivo_arriba($nodo_id, $tipo_elemento, $datos_parent->nodo_id_padre, $datos_parent->tipo_elemento_padre, $version_id)) {
					$resultado = true;
				} else {
					$resultado = false;
				}
			} else {
				$resultado = true;
			}
		}

		return $resultado;
	}

	public function validar_hoja_recursivo_abajo($nodo_lado_b, $tipo_lado_b, $nodo_lado_a, $tipo_lado_a, $version_id)
	{
		$this->verify_logged_in();

		//Esta funcion evita que el nodo del lado B de los que asigno como dependencia no contenga el nodo del lado A para eso busco si existe en el arbol y traigo los hijos
		//Por defecto es true
		$resultado = true;

		//Si lo estoy asociando a si mismo o llega el mismo parent devuelvo false
		if ($nodo_lado_b == $nodo_lado_a && $tipo_lado_b == $tipo_lado_a) {
			return false;
		} else {
			$datos_busqueda = array(
				"nodo_id_padre" => $nodo_lado_b,
				"tipo_elemento_padre" => $tipo_lado_b,
				"version_id" => $version_id
			);

			//Busco si tiene hijos el nodo actual
			$hijos_registro = $this->generic->get_from_table("arbol_dependencias", $datos_busqueda);



			if (!empty($hijos_registro)) {
				foreach ($hijos_registro as $nodo_hijo) {
					if ($this->validar_hoja_recursivo_abajo($nodo_hijo->nodo_id, $nodo_hijo->tipo_elemento, $nodo_lado_a, $tipo_lado_a, $version_id)) {
						$resultado = true;
					} else {
						return false;
					}
				}
			}
		}

		return $resultado;
	}

	protected function agregar_periodicidad_fecha($periodicidad, $fecha)
	{
		$fecha_nueva = $fecha;
		switch ($periodicidad) {
			case 'Diario':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 day'));
				break;
			case 'diariamente':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 day'));
				break;
			case 'Diario (revisión semanal)':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 week'));
				break;
			case 'Semanal':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 week'));
				break;
			case 'semanalmente':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 week'));
				break;
			case 'Quincenal':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +15 days'));
				break;
			case 'Mensual':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 month'));
				break;
			case 'mensualmente':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 month'));
				break;
			case 'Bimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +2 month'));
				break;
			case 'bimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +2 month'));
				break;
			case 'Trimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +3 month'));
				break;
			case 'trimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +3 month'));
				break;
			case 'Semestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +6 month'));
				break;
			case 'Anual':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 year'));
				break;
			case 'anual':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 year'));
				break;
			default:
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' +1 day')); //Agrego un default sino entra en un loop en compliance al guardar revisión
				break;
		}
		return $fecha_nueva;
	}

	protected function quitar_periodicidad_fecha($periodicidad, $fecha)
	{
		if ($fecha) {
			$fecha_nueva = $fecha;
		} else {
			$fecha_nueva = null;
		}

		switch ($periodicidad) {
			case 'Diario':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -1 day'));
				break;
			case 'Diario (revisión semanal)':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -1 week'));
				break;
			case 'Semanal':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -1 week'));
				break;
			case 'Quincenal':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -15 days'));
				break;
			case 'Mensual':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -1 month'));
				break;
			case 'Bimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -2 month'));
				break;
			case 'Trimestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -3 month'));
				break;
			case 'Semestral':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -6 month'));
				break;
			case 'Anual':
				$fecha_nueva = date('Y-m-d', strtotime($fecha . ' -1 year'));
				break;
			default:
				break;
		}
		return $fecha_nueva;
	}

	protected function procesar_campos_form($datos_formulario, $es_edicion = false, $campos_procesar, $datos_originales = false)
	{
		$resultado = array();
		$campos_guardar = array();
		$log = "";

		foreach ($campos_procesar as $campo) {
			$nombre_campo = $campo->nombre_campo;

			switch ($campo->tipo) {
				case 'DATE':
					if ((bool)strtotime($datos_formulario[$nombre_campo]) && $datos_formulario[$nombre_campo] != '' && $datos_formulario[$nombre_campo] != '0000-00-00') {
						if ($datos_formulario[$nombre_campo] != '') {
							$valor = date('Y-m-d', strtotime($datos_formulario[$nombre_campo]));
						} else {
							$valor = NULL;
						}
					} else {
						$valor = NULL;
					}
					break;
				case 'CHECKBOX':
					if (isset($datos_formulario[$nombre_campo])) {
						$valor = 1;
					} else {
						$valor = 0;
					}
					break;
				case 'USUARIO':
					if (isset($datos_formulario[$nombre_campo])) {
						$valor = json_encode($datos_formulario[$nombre_campo]);
					} else {
						$valor = NULL;
					}
					break;
				default:
					if (isset($datos_formulario[$nombre_campo])) {
						$valor = $datos_formulario[$nombre_campo];
					} else {
						$valor = NULL;
					}
					break;
			}

			if ($datos_originales && is_object($datos_originales) && isset($datos_originales->$nombre_campo) && $datos_originales->$nombre_campo != $valor) {
				$log .= ' el campo ' . $campo->nombre_mostrar . ' cambió de "' . $datos_originales->$nombre_campo . '" a "' . $valor . '",';
			} else {
				//Los campos que son null no los proceso para guardar
				if ($valor === NULL) {
					continue;
				}
			}

			$campos_guardar[$nombre_campo] = $valor;
		}

		if ($log != '') {
			$log = rtrim($log, ',');
		}

		$resultado['datos_guardar'] = $campos_guardar;
		$resultado['log'] = $log;

		return $resultado;
	}

	protected function actualizar_fecha_revision_componente_sd($id_componente, $actualiza_fec_inicio_periodicidad = false)
	{
		$componente_alerta = $this->generic->get_row_from_table('analisis_sd_servicios_x_componente', array('id' => $id_componente));

		//Si no tiene fecha de revision la genero
		if (!$componente_alerta->fecha_revision || $componente_alerta->fecha_revision == '0000-00-00') {
			$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $componente_alerta->inicio_control);

			//Si tiene revisiones voy a buscar el periodo que le siga para inicializar la fecha de revision
			$ultima_revision = $this->generic->get_row_from_table("analisis_sd_servicios_x_componente_revisiones", array("componente_id" => $componente_alerta->id), "fecha_revision desc");
			if ($ultima_revision) {
				while ($fecha_prox_revision <= $ultima_revision->fecha_revision) {
					$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $fecha_prox_revision);
				}
			}
		} else {
			$ultima_revision = $this->generic->get_row_from_table("analisis_sd_servicios_x_componente_revisiones", array("componente_id" => $componente_alerta->id), "fecha_revision desc");
			//Si nunca tuvo revisiones vuelvo a generarla
			if (!$ultima_revision) {
				$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $componente_alerta->inicio_control);
			} else {
				//Pregunto si es actualizacion de fecha de inicio o periodicidad
				if ($actualiza_fec_inicio_periodicidad) {
					//Si tiene una revision ya no es la fecha de inicio de control la que tiene que tomar, sino que calculo a partir de la fecha de la última revisión
					$periodos_revision = explode('hasta', $ultima_revision->periodo_revision);
					$periodo_hasta = trim($periodos_revision[1], ' ');
					$fecha_calcular = $periodo_hasta;

					$fecha_formatear = DateTime::createFromFormat('d/m/Y', $fecha_calcular);
					$fecha_calcular = $fecha_formatear->format('Y-m-d');

					//Si esta actualizando calculo desde fecha de inicio y esa fecha tiene que ser mayor a la actual y a la de revision
					$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $fecha_calcular);
					while ($fecha_prox_revision < date('Y-m-d') && $fecha_prox_revision < $ultima_revision->fecha_revision) {
						$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $fecha_prox_revision);
					}
				} else {
					//Si es una actualizacion estandar de aviso tomo la ultima fecha de revision y le sumo la periodicidad
					$fecha_prox_revision = $componente_alerta->fecha_revision;
					do {
						$fecha_prox_revision = $this->agregar_periodicidad_fecha($componente_alerta->periodicidad, $fecha_prox_revision);
					} while ($fecha_prox_revision < date('Y-m-d'));
				}
			}
		}

		$this->generic->update('analisis_sd_servicios_x_componente', array('id' => $id_componente), array("fecha_revision" => $fecha_prox_revision));

		return $fecha_prox_revision;
	}

	protected function actualizar_fecha_revision_seccion_compliance($id_seccion_analisis, $modificacion = false)
	{
		$datos_seccion_analisis = $this->generic->get_row_from_table("compliance_analisis_normas_seccion", array("id" => $id_seccion_analisis));

		//Si no tiene fecha de revision la genero
		if (!$datos_seccion_analisis->fecha_revision || $datos_seccion_analisis->fecha_revision == '0000-00-00' || $datos_seccion_analisis->fecha_revision = '') {
			$fecha_prox_revision = $this->agregar_periodicidad_fecha($datos_seccion_analisis->periodicidad, $datos_seccion_analisis->inicio_control);

			//Si tiene revisiones voy a buscar el periodo que le siga para inicializar la fecha de revision
			$ultima_revision = $this->generic->get_row_from_table("analisis_sd_servicios_x_componente_revisiones", array("componente_id" => $datos_seccion_analisis->id), "fecha_revision desc");
			if ($ultima_revision) {
				while ($fecha_prox_revision <= $ultima_revision->fecha_revision) {
					$fecha_prox_revision = $this->agregar_periodicidad_fecha($datos_seccion_analisis->periodicidad, $fecha_prox_revision);
				}
			}
		} else {
			$fecha_prox_revision = $datos_seccion_analisis->fecha_revision;
			do {
				$fecha_prox_revision = $this->agregar_periodicidad_fecha($datos_seccion_analisis->periodicidad, $fecha_prox_revision);
			} while ($fecha_prox_revision < date('Y-m-d'));
		}

		$this->generic->update('compliance_analisis_normas_seccion', array('id' => $id_seccion_analisis), array("fecha_revision" => $fecha_prox_revision));

		return $fecha_prox_revision;
	}

	protected function calcular_fecha_revision_periodo_desde($id_componente)
	{
		$componente_alerta = $this->generic->get_row_from_table('analisis_sd_servicios_x_componente', array('id' => $id_componente));

		$fecha_revision_anterior = null;

		$fecha_calculada = $this->quitar_periodicidad_fecha($componente_alerta->periodicidad, $componente_alerta->fecha_revision);
		if ($fecha_calculada) {
			$fecha_revision_anterior = $fecha_calculada;
		}

		return $fecha_revision_anterior;
	}

	protected function calcular_fecha_revision_periodo_desde_seccion_compliance($id_seccion)
	{
		$seccion_alerta = $this->generic->get_row_from_table('compliance_analisis_normas_seccion', array('id' => $id_seccion));

		$fecha_revision_anterior = null;

		$fecha_calculada = $this->quitar_periodicidad_fecha($seccion_alerta->periodicidad, $seccion_alerta->fecha_revision);
		if ($fecha_calculada) {
			$fecha_revision_anterior = $fecha_calculada;
		}

		return $fecha_revision_anterior;
	}

	protected function obtener_dependencias_modelo($id_nivel, $id_registro_actual, $cantidad_niveles)
	{
		$resultado = array();

		if (!defined('NIVELES_MODELO')) {
			return $resultado;
		}

		$datos_nodo_actual = $this->generic->get_row_from_table("arbol_procesos", array("nodo_id" => $id_registro_actual, "tipo_elemento" => $id_nivel), "disabled ASC, fecha_baja DESC"); //Obtengo datos de la dependencia
		$datos_nivel_actual = NIVELES_MODELO[$id_nivel];

		if (!$datos_nivel_actual['dependencia_id']) {
			return $resultado;
		}

		if ($datos_nodo_actual) {
			$datos_nodo_padre = $this->generic->get_row_from_table("arbol_procesos", array("nodo_id" => $datos_nodo_actual->nodo_id_padre, "tipo_elemento" => $datos_nodo_actual->tipo_elemento_padre), "disabled ASC, fecha_baja DESC");
		} else {
			$datos_nodo_padre = false;
		}

		$cantidad_procesados = 0;

		while ($datos_nivel_actual['dependencia_id'] && $cantidad_procesados < $cantidad_niveles) {
			//completar los array de resultado
			$datos_nivel_actual = NIVELES_MODELO[$datos_nivel_actual['dependencia_id']];

			if ($datos_nivel_actual) {
				if ($datos_nodo_padre) {
					$datos_superior = $this->generic->get_row_from_table($datos_nivel_actual['tabla'], array("id" => $datos_nodo_padre->nodo_id));

					if (!empty($datos_nivel_actual['campos'])) {
						foreach ($datos_nivel_actual['campos'] as $campo) {
							$nombre_campo = $campo['campo'];
							$resultado[$datos_nivel_actual['id']][$nombre_campo] = $datos_superior->$nombre_campo;

							//Agrego ID del item
							$resultado[$datos_nivel_actual['id']]['id'] = $datos_superior->id;
						}
					}
				} else {
					if (!empty($datos_nivel_actual['campos'])) {
						foreach ($datos_nivel_actual['campos'] as $campo) {
							$nombre_campo = $campo['campo'];
							$resultado[$datos_nivel_actual['id']][$nombre_campo] = "";

							//Agrego ID del item
							$resultado[$datos_nivel_actual['id']]['id'] = "";
						}
					}
				}
			}

			if (!isset($datos_nivel_actual['dependencia_id']) || $datos_nivel_actual['dependencia_id'] == '') {
				$resultado = array_reverse($resultado, true);
				return $resultado;
			}

			if ($datos_nodo_padre) {
				$datos_nodo_padre = $this->generic->get_row_from_table("arbol_procesos", array("nodo_id" => $datos_nodo_padre->nodo_id_padre, "tipo_elemento" => $datos_nodo_padre->tipo_elemento_padre), "disabled ASC, fecha_baja DESC");
			} else {
				$datos_nodo_padre = false;
			}

			$cantidad_procesados++;
		}
		$resultado = array_reverse($resultado, true);
		return $resultado;
	}

	protected function obtener_dependencias_izquierda_derecha($id_nivel)
	{
		$dependencias = array();

		$nivel_actual = $this->generic->get_row_from_table("modelo_niveles", array("id" => $id_nivel));
		if (!$nivel_actual) {
			return false;
		}

		if (!$nivel_actual->dependencia_id && $nivel_actual->dependencia_id) {
			return $dependencias;
		}

		$nivel_dependencia = $this->generic->get_row_from_table("modelo_niveles", array("id" => $nivel_actual->dependencia_id));
		if (!$nivel_dependencia) {
			return $dependencias;
		}

		while ($nivel_dependencia) {
			$dependencias[$nivel_dependencia->id] = $nivel_dependencia;
			$nivel_dependencia = $this->generic->get_row_from_table("modelo_niveles", array("id" => $nivel_dependencia->dependencia_id));
		}

		return array_reverse($dependencias, true);
	}

	protected function get_nombre_entidad()
	{
		if (defined("ENTIDAD")) {
			return ENTIDAD;
		} else {
			return false;
		}
	}

	protected function get_campos_niveles_modelo_estadisticas()
	{
		$campos_modelo = array();
		$niveles_modelo = $this->generic->get_from_table("modelo_niveles");

		if (!empty($niveles_modelo)) {
			foreach ($niveles_modelo as $nivel) {
				$campos_modelo[$nivel->id] = array();

				$campos_nivel = $this->generic->get_from_table("registros_oyp_posicion", array("nivel_id" => $nivel->id, "visibilidad_estadisticas" => 1), 'posicion asc');
				if (!empty($campos_nivel)) {
					foreach ($campos_nivel as $campo) {
						$campos_modelo[$nivel->id][] = $campo->campo;
					}
				} else {
					$campos_modelo[$nivel->id][] = "identificador";
					$campos_modelo[$nivel->id][] = "nombre";
				}
			}
		}

		return $campos_modelo;
	}

	protected function get_campos_niveles_modelo_analisis()
	{
		$campos_modelo = array();
		$niveles_modelo = $this->generic->get_from_table("modelo_niveles");

		if (!empty($niveles_modelo)) {
			foreach ($niveles_modelo as $nivel) {
				$campos_modelo[$nivel->id] = array();

				$campos_nivel = $this->generic->get_from_table("registros_oyp_posicion", array("nivel_id" => $nivel->id, "visibilidad_analisis" => 1), 'posicion asc');
				if (!empty($campos_nivel)) {
					foreach ($campos_nivel as $campo) {
						$campos_modelo[$nivel->id][] = $campo->campo;
					}
				} else {
					if (strpos($_SERVER['HTTP_HOST'], "naranjax") === false) {
						$campos_modelo[$nivel->id][] = "identificador";
					} else {
						$campos_modelo[$nivel->id][] = "identificador";
						$campos_modelo[$nivel->id][] = "nombre";
					}
				}
			}
		}

		return $campos_modelo;
	}

	protected function eliminar_campo_tabla_aleph($tabla, $nombre_campo)
	{
		if (!$this->generic->check_field_exist($nombre_campo, $tabla)) {
			return true;
		}

		$query_update = "ALTER TABLE " . $tabla . " DROP COLUMN " . $nombre_campo;
		$this->generic->run_query($query_update);

		return true;
	}

	protected function crear_campo_tabla_aleph($tabla, $tipo_campo, $nombre_campo)
	{
		//Si el campo ya existe devuelvo true
		if ($this->generic->check_field_exist($nombre_campo, $tabla)) {
			return true;
		}

		switch (ucfirst($tipo_campo)) {
			case 'VARCHAR':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " VARCHAR(255)";
				break;
			case 'TEXT':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TEXT";
				break;
			case 'DATE':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " DATE";
				break;
			case 'CHECKBOX':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TINYINT(1) NOT NULL DEFAULT 0";
				break;
			case 'MESANIO':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " VARCHAR(30)";
				break;
			case 'ARCHIVO':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " VARCHAR(255)";
				break;
			case 'USUARIO':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TEXT";
				break;
			case 'NUMERICO':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " BIGINT(20)";
				break;
			case 'PORCENTAJE':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " FLOAT";
				break;
			case 'GERENCIA':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TEXT";
				break;
			case 'SELECT':
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TEXT DEFAULT NULL";
				break;
			default:
				$query_update = "ALTER TABLE " . $tabla . " ADD COLUMN " . $nombre_campo . " TEXT DEFAULT NULL";
				break;
		}

		if ($query_update != '') {
			$this->generic->run_query($query_update);
		}

		return true;
	}

	protected function cambiar_tipo_campo_tabla_aleph($tabla, $tipo_campo, $nombre_campo)
	{
		switch ($tipo_campo) {
			case 'VARCHAR':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " VARCHAR(255)";
				break;
			case 'TEXT':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " TEXT";
				break;
			case 'DATE':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " DATE";
				break;
			case 'CHECKBOX':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " TINYINT(1) NOT NULL DEFAULT 0";
				break;
			case 'MESANIO':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " VARCHAR(30)";
				break;
			case 'ARCHIVO':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " VARCHAR(255)";
				break;
			case 'USUARIO':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " VARCHAR(50)";
				break;
			case 'NUMERICO':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " BIGINT(20)";
				break;
			case 'PORCENTAJE':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " BIGINT(20)";
				break;
			case 'GERENCIA':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " TEXT";
				break;
			case 'SELECT':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " TEXT DEFAULT NULL";
				break;
			case 'ARCHIVO':
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " VARCHAR(255)";
				break;
			default:
				$query_update = "ALTER TABLE " . $tabla . " CHANGE COLUMN " . $nombre_campo . " " . $nombre_campo . " TEXT DEFAULT NULL";
				break;
		}

		if ($query_update != '') {
			$this->generic->run_query($query_update);
		}

		return true;
	}

	protected function crear_tabla_opcionales_categoria($id_categoria)
	{
		//Verifico si existe la tabla
		$tabla_opcionales = "opcionales_categoria_" . $id_categoria;
		$existe_tabla = $this->generic->check_table($tabla_opcionales);
		if (!$existe_tabla) {
			$table_data = array(
				'id' => 'INT(11) NOT NULL AUTO_INCREMENT',
				'activo_tecnologico_id' => 'INT(11) NOT NULL',
				'cmdb_id' => 'INT(11) NOT NULL',
				'CONSTRAINT' => 'analisis_sd_responsables_pk PRIMARY KEY (id)'
			);

			$this->generic->create_table($tabla_opcionales, $table_data);

			//Al crear la tabla agrego los indices
			$query_update = "ALTER TABLE " . $tabla_opcionales . " ADD INDEX(activo_tecnologico_id)";
			$this->generic->run_query($query_update);
			$query_update = "ALTER TABLE " . $tabla_opcionales . " ADD INDEX(cmdb_id)";
			$this->generic->run_query($query_update);
		}

		return true;
	}

	protected function add_campo_custom($datos_formulario, $url, $tabla_campos, $tabla_destino, $modulo, $categoria_id = false)
	{
		if (isset($datos_formulario['tipo']) && $datos_formulario['tipo'] == 'ARCHIVO') {
			$longitud = '255';
		} else {
			if (isset($datos_formulario['longitud'])) {
				$longitud = $datos_formulario['longitud'];
			}
		}

		//Si trae cat_id recupero los datos de la categoria
		if ($categoria_id) {
			$datos_categoria = $this->generic->get_row_from_table("categorias", array("id" => $categoria_id));
			if (!$datos_categoria) {
				echo "No se pudo recuperar datos de la categoría";
				return false;
			}
		}

		if ($datos_formulario['accion'] == 'add') {
			$nombre_campo = $this->generar_nombre_campo_db($datos_formulario['nombre_campo']);

			$where_existe_campo = array(
				'nombre_campo' => $nombre_campo
			);

			if ($categoria_id) {
				$where_existe_campo['categoria_id'] = $categoria_id;
			}

			$existe_campo = $this->generic->get_row_from_table($tabla_campos, $where_existe_campo);
			if (!$existe_campo) {
				$data_campo = array(
					'nombre_campo' => $nombre_campo,
					'nombre_mostrar' => $datos_formulario['nombre_mostrar'],
					'tipo' => $datos_formulario['tipo'],
					'requerido' => $datos_formulario['requerido'],
					'posicion' => $datos_formulario['posicion']
				);

				if (isset($longitud)) {
					$data_campo['longitud'] = $longitud;
				}

				if (isset($datos_formulario['visible'])) {
					$data_campo['visible'] = $datos_formulario['visible'];
				}

				if ($categoria_id) {
					$data_campo['categoria_id'] = $categoria_id;
				}

				if ($datos_formulario['tipo'] == 'SELECT') {
					if (!isset($datos_formulario['opciones']) || empty($datos_formulario['opciones'])) {
						if ($url) {
							$this->session->set_flashdata('error_message', 'Debe agregar opciones para el tipo de campo selector de opciones');
							redirect($url);
							exit();
						} else {
							echo 'Debe agregar opciones para el tipo de campo selector de opciones';
							return false;
						}
					}

					$opciones = array();
					foreach ($datos_formulario['opciones'] as $opcion) {
						$opciones[] = $opcion;
					}

					$data_campo['opciones'] = json_encode($opciones);
				}

				//Valido que no exista el campo en la base de datos antes de crearlo
				if (!$this->generic->check_field_exist($nombre_campo, $tabla_destino)) {
					$this->crear_campo_tabla_aleph($tabla_destino, $datos_formulario['tipo'], $nombre_campo);

					//Guardo el campo
					$this->generic->save_on_table($tabla_campos, $data_campo);
					if (isset($datos_categoria)) {
						$this->guardar_log('Creó el campo "' . $datos_formulario['nombre_mostrar'] . '" para la categoría ' . $datos_categoria->nombre);
					} else {
						$this->guardar_log('Creó el campo "' . $datos_formulario['nombre_mostrar'] . '" en el módulo ' . $modulo);
					}
				}

				$this->session->set_flashdata('success_message', 'Campo "' . $datos_formulario['nombre_mostrar'] . '" guardado.');
			} else {
				if ($url) {
					$this->session->set_flashdata('error_message', 'Ya existe un campo con el nombre "' . $datos_formulario['nombre_mostrar'] . '".');
				} else {
					if ($categoria_id) {
						echo 'Ya existe un campo con el nombre "' . $datos_formulario['nombre_mostrar'] . '" para la categoría "' . $datos_categoria->nombre . '".';
					} else {
						echo 'Ya existe un campo con el nombre "' . $datos_formulario['nombre_mostrar'] . '".';
					}
					return false;
				}
			}
		} else {
			$datos_campo = $this->generic->get_row_from_table($tabla_campos, array("id" => $datos_formulario['id']));
			if ($datos_campo) {
				$nombre_original = $datos_campo->nombre_campo;

				$data_update = array();

				if (isset($datos_categoria)) {
					$mensaje_log = 'Actualizo los siguientes datos para el campo "' . $datos_campo->nombre_campo . '" en la categoría ' . $datos_categoria->nombre . ':';
				} else {
					$mensaje_log = 'Actualizo los siguientes datos para el campo "' . $datos_campo->nombre_campo . '" en el módulo de ' . $modulo . ':';
				}

				if ($datos_formulario['tipo'] == 'SELECT') {
					//Cambio las opciones
					if (!isset($datos_formulario['opciones']) || empty($datos_formulario['opciones'])) {
						if ($url) {
							$this->session->set_flashdata('error_message', 'Debe agregar opciones para el tipo de campo selector de opciones');
							redirect($url);
							exit();
						} else {
							echo 'Debe agregar opciones para el tipo de campo selector de opciones';
							return false;
						}
					}

					$opciones = array();
					foreach ($datos_formulario['opciones'] as $opcion) {
						$opciones[] = $opcion;
					}

					$data_update['opciones'] = json_encode($opciones);
				}

				if ($datos_formulario['tipo'] != $datos_campo->tipo) {
					$data_update['tipo'] = $datos_formulario['tipo'];
					$mensaje_log .= '<br>Tipo de campo de "' . $datos_campo->tipo . '" a "' . $datos_formulario['tipo'] . '"';

					if ($this->generic->check_field_exist($nombre_original, $tabla_destino)) {
						$this->cambiar_tipo_campo_tabla_aleph($tabla_destino, $datos_formulario['tipo'], $nombre_original);
					}
				}

				if (isset($datos_formulario['rol_id_editor'])) {
					if ($datos_formulario['rol_id_editor'] != $datos_campo->rol_id_editor) {
						$data_update['rol_id_editor'] = $datos_formulario['rol_id_editor'];

						$datos_rol_anterior = $this->generic->get_row_from_table("roles", array("id" => $datos_campo->rol_id_editor));
						$datos_rol_nuevo = $this->generic->get_row_from_table("roles", array("id" => $datos_formulario['rol_id_editor']));

						if ($datos_rol_anterior) {
							$rol_anterior = $datos_rol_anterior->nombre;
						} else {
							$rol_anterior = 'Todos';
						}

						if ($datos_rol_nuevo) {
							$rol_nuevo = $datos_rol_nuevo->nombre;
						} else {
							$rol_nuevo = 'Todos';
						}

						$mensaje_log .= '<br>Rol de editor de campo de "' . $rol_anterior . '" a "' . $rol_nuevo . '"';
					}
				}

				if ($datos_campo->nombre_mostrar != $datos_formulario['nombre_mostrar']) {
					$data_update['nombre_mostrar'] = $datos_formulario['nombre_mostrar'];
					$mensaje_log .= '<br>Nombre a mostrar de "' . $datos_campo->nombre_mostrar . '" a "' . $datos_formulario['nombre_mostrar'] . '"';
				}

				if (isset($datos_campo->longitud) && isset($longitud)) {
					if ($datos_campo->longitud != $longitud) {
						if ($datos_formulario['tipo'] == 'TEXT') {
							if ($this->generic->check_field_exist($nombre_original, $tabla_destino)) {
								$query_update = "ALTER TABLE " . $tabla_destino . " CHANGE COLUMN " . $nombre_original . " " . $nombre_original . " TEXT DEFAULT NULL";
								$this->generic->run_query($query_update);
							}
						}

						$data_update['longitud'] = $longitud;
						$mensaje_log .= '<br>Longitud de "' . $datos_campo->longitud . '" a "' . $datos_formulario['longitud'] . '"';
					}
				}

				if (isset($datos_campo->visible) && isset($datos_formulario['visible'])) {
					if ($datos_campo->visible != $datos_formulario['visible']) {
						$data_update['visible'] = $datos_formulario['visible'];

						if ($datos_formulario['visible']) {
							$mensaje_log .= '<br>Invisible a visible';
						} else {
							$mensaje_log .= '<br>Visible a invisible';
						}
					}
				}

				if ($datos_campo->requerido != $datos_formulario['requerido']) {
					$data_update['requerido'] = $datos_formulario['requerido'];

					if ($datos_formulario['requerido']) {
						$mensaje_log .= '<br>No requerido a requerido';
					} else {
						$mensaje_log .= '<br>Requerido a no requerido';
					}
				}

				if ($datos_campo->posicion != $datos_formulario['posicion']) {
					$data_update['posicion'] = $datos_formulario['posicion'];
					$mensaje_log .= '<br>Posición de "' . $datos_campo->posicion . '" a "' . $datos_formulario['posicion'] . '"';
				}

				if (!empty($data_update)) {
					$this->generic->update($tabla_campos, array("id" => $datos_formulario['id']), $data_update);
					$this->session->set_flashdata('success_message', 'Campo actualizado');
					if ($mensaje_log != '') {
						$this->guardar_log($mensaje_log);
					}
				} else {
					$this->session->set_flashdata('success_message', 'Campo sin actualizaciones');
				}
			} else {
				if ($url) {
					$this->session->set_flashdata('error_message', 'No se han podido recuperar datos del campo');
				} else {
					echo 'No se han podido recuperar datos del campo';
					return false;
				}
			}
		}

		if ($url) {
			redirect($url);
		} else {
			return true;
		}
	}

	protected function delete_campo_custom($tabla_campos, $tabla_destino, $modulo)
	{
		$datos_campo = $this->generic->get_row_from_table($tabla_campos, array("id" => $_POST['id']));
		if (!$datos_campo) {
			return false;
		}

		if ($this->generic->check_field_exist($datos_campo->nombre_campo, $tabla_destino)) {
			$this->generic->delete_where($tabla_campos, array("id" => $_POST['id']));

			$query_update = "ALTER TABLE " . $tabla_destino . " DROP COLUMN " . $datos_campo->nombre_campo;
			$this->generic->run_query($query_update);

			$this->guardar_log('Eliminó el campo "' . $datos_campo->nombre_campo . '" en el módulo ' . $modulo);
		} else {
			return false;
		}

		return true;
	}

	protected function procesar_campos_importacion($campos_importar, $fila_datos, $col_nro = 0)
	{
		$resultado = array();

		$valores_importacion = array();
		$errores = array();
		$errores_menores = array();
		foreach ($campos_importar as $campo) {
			$nombre_campo = $campo->nombre_campo;

			$valor = NULL;
			switch ($campo->tipo) {
				case 'DATE':
					if (isset($fila_datos[$col_nro]) && (bool)strtotime($fila_datos[$col_nro])) {
						$valor = date('Y-m-d', strtotime($fila_datos[$col_nro]));
					}
					break;
				case 'CHECKBOX':
					if (isset($fila_datos[$col_nro]) && $fila_datos[$col_nro] != '') {
						$valor = 1;
					} else {
						$valor = 0;
					}
					break;
				case 'SELECT':
					$opciones = $this->json_object_to_array($campo->opciones);

					if (isset($fila_datos[$col_nro]) && $fila_datos[$col_nro] != '') {
						foreach ($opciones as $value) {
							if ($fila_datos[$col_nro] == $value) {
								$valor = $fila_datos[$col_nro];
							}
						}
					}
					break;
				case 'AREA':
					if (isset($fila_datos[$col_nro]) && $fila_datos[$col_nro] != '') {
						$datos_area = $this->generic->get_row_from_table('areas', array("nombre" => $fila_datos[$col_nro]));
						if ($datos_area) {
							$valor = $fila_datos[$col_nro];
						} else {
							$errores_menores[] = 'El área "' . $fila_datos[$col_nro] . '" no existe en el sistema.';
						}
					}
					break;
				default:
					if (isset($fila_datos[$col_nro])) {
						$valor = $fila_datos[$col_nro];
					}
					break;
			}

			if (!$valor && $campo->requerido) {
				$errores[] = 'El valor "' . $fila_datos[$col_nro] . '" para el campo "' . $campo->nombre_mostrar . '" es inválido, no se puede procesar la linea.';
			}

			$valores_importacion[$nombre_campo] = $valor;
			$col_nro++;
		}

		$resultado['valores_importacion'] = $valores_importacion;
		$resultado['log_errores_menores'] = $errores_menores;
		$resultado['log_errores_graves'] = $errores;

		return $resultado;
	}

	protected function mostrar_tiempo_ejecucion()
	{
		$tiempo_ejecucion = $this->db->query_times;

		//Obtengo el ultimo elemento del array
		$tiempo_ejecucion = $tiempo_ejecucion[(count($tiempo_ejecucion) - 1)];

		echo "Tiempo ejecución: " . $tiempo_ejecucion;
		die();
	}

	protected function remover_saltos_linea_csv($file)
	{
		$file = preg_replace("/[\r\n|\n|\r]+/", PHP_EOL, $file);

		return $file;
	}

	protected function generate_password($password_string)
	{
		$key = 'U&F23bms4';
		$hash = hash_hmac('sha256', $password_string, $key);

		return $hash;
	}

	protected function procesar_token_usuario_login($email_usuario)
	{
		$time = time();
		$key = $time . '4l3ph';
		$hash = hash_hmac('sha256', $email_usuario, $key);

		return $hash;
	}

	protected function desencriptar_token_usuario_login($email_usuario, $token)
	{
		$cont = 0;

		$time = time();
		$key = $time . '4l3ph';
		$token_login = hash_hmac('sha256', $email_usuario, $key);

		if ($token_login == $token) {
			return true;
		} else {
			$codigo_ok = false;
		}

		while (!$codigo_ok && $cont < 30) {
			$time = $time + 1;
			$key = $time . '4l3ph';
			$token_login = hash_hmac('sha256', $email_usuario, $key);
			$cont++;

			if ($token_login == $token) {
				return true;
			}
		}

		return $codigo_ok;
	}

	protected function completar_campo($string, $caracter, $cantidad, $sentido)
	{
		if (is_numeric($string) && $string < 0) { //Trato especial a los numeros negativos
			$numero_absoluto = abs($string);
			$result = $this->mb_str_pad($numero_absoluto, ($cantidad - 1), $caracter, STR_PAD_LEFT);
			//Agrego el signo negativo adelante
			$result = "-" . $result;
			if ($cantidad > 0 && strlen($result) > $cantidad) {
				//Obtengo los primeros caracteres hacia la derecha
				$result = substr($result, -$cantidad);
			}
		} else {
			if ($cantidad > 0 && strlen($string) > $cantidad) {
				//Obtengo los primeros caracteres hacia la derecha
				$string = substr($string, -$cantidad);
			}

			if ($sentido == 'izquierda') {
				$result = $this->mb_str_pad($string, $cantidad, $caracter, STR_PAD_LEFT);
			} else {
				$result = $this->mb_str_pad($string, $cantidad, $caracter, STR_PAD_RIGHT);
			}
		}

		return $result;
	}

	protected function mb_str_pad($str, $pad_len, $pad_str = ' ', $dir = STR_PAD_RIGHT, $encoding = NULL)
	{
		$encoding = $encoding === NULL ? mb_internal_encoding() : $encoding;
		$padBefore = $dir === STR_PAD_BOTH || $dir === STR_PAD_LEFT;
		$padAfter = $dir === STR_PAD_BOTH || $dir === STR_PAD_RIGHT;
		$pad_len -= mb_strlen($str, $encoding);
		$targetLen = $padBefore && $padAfter ? $pad_len / 2 : $pad_len;
		$strToRepeatLen = mb_strlen($pad_str, $encoding);
		$repeatTimes = ceil($targetLen / $strToRepeatLen);
		$repeatedString = str_repeat($pad_str, max(0, $repeatTimes)); // safe if used with valid unicode sequences (any charset)
		$before = $padBefore ? mb_substr($repeatedString, 0, (int)floor($targetLen), $encoding) : '';
		$after = $padAfter ? mb_substr($repeatedString, 0, (int)ceil($targetLen), $encoding) : '';
		return $before . $str . $after;
	}

	protected function get_array_areas()
	{
		$areas_all = array();
		$areas = $this->generic->get_from_table('areas', false, 'nombre asc');
		foreach ($areas as $area) {
			$areas_all[$area->id] = $area->nombre;
		}

		return $areas_all;
	}

	function validar_formato_fecha($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}

	protected function get_server_protocol()
	{
		if (
			isset($_SERVER['HTTPS']) &&
			($_SERVER['HTTPS'] == 'on' || $_SERVER['HTTPS'] == 1) ||
			isset($_SERVER['HTTP_X_FORWARDED_PROTO']) &&
			$_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https'
		) {
			$protocol = 'https://';
		} else {
			$protocol = 'http://';
		}

		return $protocol;
	}

	/**************************** INTERFACES PHP EXCEL **************************/
	protected function instanciar_objeto_php_excel()
	{
		$objeto_php_excel = null;

		if (PHP_VERSION >= '7.4') {
			include_once ASSETS . "excel/Classes/PHPSpreadsheet/vendor/autoload.php";
			//Cargo la nueva libreria
			$objeto_php_excel =  new Spreadsheet();
		} else {
			include_once ASSETS . "excel/Classes/PHPExcel.php";
			//Cargo libreria vieja 
			$objeto_php_excel = new PHPExcel();
		}

		if (!$objeto_php_excel) {
			$this->mostrar_error('Error al cargar libreria php excel');
		}

		return $objeto_php_excel;
	}

	protected function excel_set_hoja_activa($objeto_php_excel, $indice_hoja)
	{
		if (PHP_VERSION >= '7.4') {
			//Cargo la nueva libreria
			$objeto_php_excel->setActiveSheetIndex($indice_hoja);
		} else {
			//Cargo libreria vieja 
			$objeto_php_excel->setActiveSheetIndex($indice_hoja);
		}
		return true;
	}

	protected function excel_set_nombre_hoja($objeto_php_excel, $nombre_hoja)
	{
		$objeto_php_excel->getActiveSheet()->setTitle($nombre_hoja);
	}

	protected function excel_mergear_celdas($objeto_php_excel, $posiciones_celdas)
	{
		$objeto_php_excel->getActiveSheet()->mergeCells($posiciones_celdas);
	}

	protected function excel_escribir_celda($objeto_php_excel, $posicion, $contenido, $color = false)
	{
		$objeto_php_excel->getActiveSheet()->SetCellValue($posicion, $contenido);

		if ($color) {
			$this->pintar_celda($posicion, $color, $objeto_php_excel);
		}
	}

	protected function excel_escribir_celda_texto($objeto_php_excel, $posicion, $contenido, $color = false)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$objeto_php_excel->getActiveSheet()->setCellValueExplicit($posicion, $contenido, PHPExcel_Cell_DataType::TYPE_STRING);
		}

		if ($color) {
			$this->pintar_celda($posicion, $color, $objeto_php_excel);
		}
	}

	protected function excel_pintar_celda_negrita($objeto_php_excel, $posicion)
	{
		$objeto_php_excel->getActiveSheet()->getStyle($posicion)->getFont()->setBold(true);
	}

	protected function excel_centrar_texto_celdas($objeto_php_excel, $posicion)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$center_text = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
				)
			);
			$objeto_php_excel->getActiveSheet()->getStyle($posicion)->applyFromArray($center_text);
		}
	}

	protected function excel_aplicar_bordes_celdas($objeto_php_excel, $posicion)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$estilo_bordes = array(
				'borders' => array(
					'allborders' => array(
						'style' => PHPExcel_Style_Border::BORDER_DOUBLE,
						'color' => array('rgb' => 'FFFFFF')
					)
				)
			);
			$objeto_php_excel->getActiveSheet()->getStyle($posicion)->applyFromArray($estilo_bordes);
		}
	}

	protected function excel_aplicar_fondo_celdas($objeto_php_excel, $posicion, $color)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$estilo_fondo = array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'rgb' => $color
				)
			);

			$objeto_php_excel->getActiveSheet()->getStyle($posicion)->getFill()->applyFromArray($estilo_fondo);
		}
	}

	protected function excel_aplicar_formato_numero_celdas($objeto_php_excel, $posicion)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$objeto_php_excel->getActiveSheet()->getStyle($posicion)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_TEXT);
		}
	}

	protected function excel_aplicar_formato_texto_alternativo_celdas($objeto_php_excel, $posicion)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$objeto_php_excel->getActiveSheet()->getCell($posicion)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING2);
		}
	}

	protected function pintar_celda($celda, $color, $objeto_php_excel)
	{
		if ($color == '' || $color == '#') {
			return false;
		}

		$color = ltrim($color, '#');

		if (PHP_VERSION >= '7.4') {
			// $objeto_php_excel->getActiveSheet()->getStyle($celda)->getFill()->applyFromArray(array(
			//     'type' => PHPExcel_Style_Fill::FILL_SOLID,
			//     'startcolor' => array(
			//         'rgb' => $color
			//     )
			// ));
		} else {
			$objeto_php_excel->getActiveSheet()->getStyle($celda)->getFill()->applyFromArray(array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'startcolor' => array(
					'rgb' => $color
				)
			));
		}
	}

	protected function generar_excel($objeto_php_excel, $path_archivo, $descarga_directa = false)
	{
		$url_archivo = BASE_URL . '/' . $path_archivo;
		$nombre_archivo = basename($path_archivo);

		if (PHP_VERSION >= '7.4') {
			//Cargo la nueva libreria
			$objWriter = new Xlsx($objeto_php_excel);
			$objWriter->save($path_archivo);
			if ($descarga_directa) {
				header('Content-Type: application/vnd.ms-excel');
			}
		} else {
			//Cargo libreria vieja
			$objWriter = new PHPExcel_Writer_Excel2007($objeto_php_excel);
			$objWriter->save($path_archivo);
			if ($descarga_directa) {
				header('Content-Type: application/octet-stream');
			}
		}

		$servers_debug = array(
			'aleph.localhost',
			'qa.alephmanager.com',
			'dev.alephmanager.com'
		);

		//Si es ambiente de prueba no envio emails por generacion de archivos
		// if(isset($_SERVER['SERVER_NAME']) && !in_array($_SERVER['SERVER_NAME'],$servers_debug)){
		//     $titulo_email = 'Notificación de generación de archivo';
		//     $contenido_email = 'Esta es una notificación para avisarle de que su archivo "'.$nombre_archivo.'" se encuentra disponible para la descarga.';
		//     $contenido_email .= '<p>Para descargarlo haga <a href="'.$url_archivo.'" download>clic aquí</a>.</p>';
		//     $this->enviar_email($this->get_user_email(),$contenido_email,$titulo_email);
		// }

		if ($descarga_directa) {
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="' . basename($path_archivo) . '"');
			header('Expires: 0');
			header('Cache-Control: must-revalidate');
			header('Pragma: public');
			header('Content-Length: ' . filesize($path_archivo));
			readfile($path_archivo);
		}

		return true;
	}

	protected function excel_cargar_archivo($target_file)
	{
		if (PHP_VERSION >= '7.4') {
			include_once ASSETS . "excel/Classes/PHPSpreadsheet/vendor/autoload.php";
			//Cargo la nueva libreria
			$objeto_php_excel = \PhpOffice\PhpSpreadsheet\IOFactory::load($target_file);
		} else {
			include_once ASSETS . "excel/Classes/PHPExcel.php";
			//Cargo libreria vieja
			$objeto_php_excel = PHPExcel_IOFactory::load($target_file);
		}

		return $objeto_php_excel;
	}

	protected function centrar_celda($objeto_php_excel, $celdas)
	{
		if (PHP_VERSION >= '7.4') {
		} else {
			$estilo_centrado = array(
				'alignment' => array(
					'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
					'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER
				)
			);

			$objeto_php_excel->getActiveSheet()->getStyle($celdas)->applyFromArray($estilo_centrado);
		}
	}

	protected function excel_titulo_columna_reporte($objeto_php_excel, $celdas)
	{
		$this->centrar_celda($objeto_php_excel, $celdas);
		$this->pintar_celda($celdas, "D3D3D3", $objeto_php_excel);
	}

	protected function excel_habilitar_saltos_lineas($objeto_php_excel, $celdas)
	{
		$objeto_php_excel->getActiveSheet()->getStyle($celdas)->getAlignment()->setWrapText(true);
	}
	/**********************************************************************/

	//Agrego funcion para reemplazar caracteres en un string
	public function correjir_errores_exportacion($string)
	{
		$characters = array("<br>" => "\n");
		$string_replace = $string;
		$characters_key = array_keys($characters);

		foreach ($characters_key as $character) {
			$string_replace = str_replace($character, $characters[$character], $string_replace);
		}

		return $string_replace;
	}

	public function correjir_errores_titulos($string)
	{
		$characters = array('"' => ' ', "'" => " ");
		$string_replace = $string;
		$characters_key = array_keys($characters);

		foreach ($characters_key as $character) {
			$string_replace = str_replace($character, $characters[$character], $string_replace);
		}

		return $string_replace;
	}

	function obtener_mayor_valor($valores)
	{
		// Verifica si el array no está vacío
		if (empty($valores)) {
			return 0;
		}

		$valores_numericos = array_filter($valores, 'is_numeric');

		// Verifica si hay elementos numéricos en el array
		if (empty($valores_numericos)) {
			return 0;
		}

		$mayor = reset($valores_numericos); // Inicializa con el primer valor numérico

		foreach ($valores_numericos as $valor) {
			if ($valor > $mayor) {
				$mayor = $valor;
			}
		}

		return $mayor;
	}

	protected function obtener_datos_archivo($archivo, $target_file, $return_url = false)
	{
		if (move_uploaded_file($archivo["tmp_name"], $target_file)) {
			$extension = pathinfo($target_file, PATHINFO_EXTENSION);

			if ($extension == 'csv') {
				$file = file($target_file);
				$file = $this->remover_saltos_linea_csv($file);
			} else {
				$excelObject = $this->excel_cargar_archivo($target_file);
				$file = $excelObject->getActiveSheet()->toArray(null);
			}

			return $file;
		} else {
			if ($return_url) {
				$this->session->set_flashdata('error_message', "No se pudo subir el archivo, vuelva a intentar más tarde, si este error persiste contáctese con un administrador.");
				redirect($return_url);
			} else {
				echo "No se pudo subir el archivo, vuelva a intentar más tarde, si este error persiste contáctese con un administrador.";
				die();
			}
		}
	}

	protected function convertir_string_enlace($string, $forzar)
	{
		if ($forzar) {
			if (strpos($string, 'http') === false) {
				$url = 'https://' . $string;
			} else {
				$url = $string;
			}

			$enlace = '<a href="' . $url . '" target="_blank" title="' . $string . '">' . $string . '</a><br>';
		} else {
			$url = '~(?:(https?)://([^\s<]+)|(www\.[^\s<]+?\.[^\s<]+))(?<![\.,:])~i';
			$enlace = preg_replace($url, '<a href="$0" target="_blank" title="$0">$0</a><br>', $string);
		}

		return $enlace;
	}

	//Obtiene todas las categorias
	protected function obtener_categorias($traer_deshabilitadas = false)
	{
		if ($traer_deshabilitadas) {
			$where = false;
		} else {
			$where = array(
				'disabled' => 0
			);
		}
		$categorias = $this->generic->get_from_table('categorias', $where, 'nombre asc');
		return $categorias;
	}

	protected function obtener_subcategorias_by_categoria_id($categoria_id)
	{
		$subcategorias = $this->generic->get_from_table('categorias', array('categoria_padre_id' => $categoria_id, 'disabled' => 0), 'nombre asc');
		return $subcategorias;
	}

	protected function obtener_categorias_padres($traer_deshabilitadas = false)
	{
		$where = array(
			'categoria_padre_id' => NULL
		);

		if (!$traer_deshabilitadas) {
			$where['disabled'] = 0;
		}

		$categorias = $this->generic->get_from_table('categorias', $where, 'nombre asc');
		return $categorias;
	}

	protected function obtener_subcategorias_all($traer_deshabilitadas = false)
	{
		$where = array(
			'categoria_padre_id != ' => NULL,
			'categoria_padre_id != ' => 0
		);

		if (!$traer_deshabilitadas) {
			$where['disabled'] = 0;
		}

		$subcategorias = $this->generic->get_from_table('categorias', $where, 'nombre asc');
		return $subcategorias;
	}

	protected function obtener_columnas_intervalos($fecha_inicial = false, $fecha_final = false)
	{
		$intervalos = array();
		setlocale(LC_TIME, 'es_ES.UTF-8');

		// Crear objetos DateTime a partir de las fechas
		$fecha_inicio_obj = DateTime::createFromFormat('Y-m-d', $fecha_inicial);
		$fecha_fin_obj = DateTime::createFromFormat('Y-m-d', $fecha_final);

		if ($fecha_inicio_obj && $fecha_fin_obj) {
			// Asegurarse de que la fecha de inicio sea menor o igual a la fecha de fin
			if ($fecha_inicio_obj > $fecha_fin_obj) {
				echo "La fecha de inicio debe ser anterior o igual a la fecha de fin.";
			} else {
				$tipos_intervalo = array(
					'semanal' => '+1 week',
					'bisemanal' => '+2 weeks',
					'mensual' => '+1 month',
					'bimensual' => '+2 months',
					'trimestral' => '+3 months',
					'semestral' => '+6 months',
					'anual' => '+1 year'
				);

				foreach ($tipos_intervalo as $tipo => $modificacion) {
					$fecha_calculo = clone $fecha_inicio_obj;
					$intervalos[$tipo] = array();

					while ($fecha_calculo <= $fecha_fin_obj) {
						$inicio_intervalo = clone $fecha_calculo;
						$fin_intervalo = clone $fecha_calculo;
						$fin_intervalo->modify($modificacion)->modify('-1 day');

						if ($fin_intervalo > $fecha_fin_obj) {
							$fin_intervalo = clone $fecha_fin_obj;
						}

						if ($tipo == 'anual') {
							$intervalos[$tipo][] = $inicio_intervalo->format('Y');
						} elseif (in_array($tipo, array('mensual', 'bimensual', 'trimestral', 'semestral'))) {
							$intervalos[$tipo][] = ucfirst(strftime('%B', $inicio_intervalo->format('U')));
						} else {
							$intervalos[$tipo][] = array(
								'inicio' => $inicio_intervalo->format('Y-m-d'),
								'fin' => $fin_intervalo->format('Y-m-d')
							);
						}

						$fecha_calculo->modify($modificacion);
					}
				}
			}
		}

		return $intervalos;
	}

	protected function crear_campos_default_secciones_compliance($norma_id)
	{
		$campos_secciones = array();
		$campos_secciones[0]['nombre_campo'] = 'nombre';
		$campos_secciones[0]['nombre_mostrar'] = 'Sección';
		$campos_secciones[0]['tipo_campo'] = 1;
		$campos_secciones[0]['es_default'] = 1;
		$campos_secciones[0]['mostrar_formulario'] = 1;
		$campos_secciones[0]['requerido'] = 1;
		$campos_secciones[0]['posicion'] = 1;

		$campos_secciones[1]['nombre_campo'] = 'punto';
		$campos_secciones[1]['nombre_mostrar'] = 'Punto';
		$campos_secciones[1]['tipo_campo'] = 1;
		$campos_secciones[1]['es_default'] = 1;
		$campos_secciones[1]['mostrar_formulario'] = 1;
		$campos_secciones[1]['requerido'] = 1;
		$campos_secciones[1]['posicion'] = 2;

		$campos_secciones[2]['nombre_campo'] = 'descripcion';
		$campos_secciones[2]['nombre_mostrar'] = 'Descripción';
		$campos_secciones[2]['tipo_campo'] = 1;
		$campos_secciones[2]['es_default'] = 1;
		$campos_secciones[2]['mostrar_formulario'] = 1;
		$campos_secciones[2]['requerido'] = 0;
		$campos_secciones[2]['posicion'] = 3;

		$campos_secciones[3]['nombre_campo'] = 'fecha_cumplimiento';
		$campos_secciones[3]['nombre_mostrar'] = 'Fecha de cumplimiento';
		$campos_secciones[3]['tipo_campo'] = 6;
		$campos_secciones[3]['es_default'] = 1;
		$campos_secciones[3]['mostrar_formulario'] = 1;
		$campos_secciones[3]['requerido'] = 1;
		$campos_secciones[3]['posicion'] = 4;

		$campos_secciones[4]['nombre_campo'] = 'cantidad_dias_notificar_vencimiento';
		$campos_secciones[4]['nombre_mostrar'] = 'Días notificar antes de fecha de cumplimiento';
		$campos_secciones[4]['tipo_campo'] = 1;
		$campos_secciones[4]['es_default'] = 1;
		$campos_secciones[4]['mostrar_formulario'] = 1;
		$campos_secciones[4]['requerido'] = 1;
		$campos_secciones[4]['posicion'] = 5;

		$periodicidades = array('Diario', 'Diario (revisión semanal)', 'Semanal', 'Quincenal', 'Mensual', 'Bimestral', 'Trimestral', 'Semestral', 'Anual', 'Sin periodicidad');
		$campos_secciones[5]['nombre_campo'] = 'frecuencia_revision';
		$campos_secciones[5]['nombre_mostrar'] = 'Frecuencia revisión';
		$campos_secciones[5]['tipo_campo'] = 2;
		$campos_secciones[5]['es_default'] = 1;
		$campos_secciones[5]['mostrar_formulario'] = 1;
		$campos_secciones[5]['requerido'] = 1;
		$campos_secciones[5]['posicion'] = 6;
		$campos_secciones[5]['valores_campo'] = json_encode($periodicidades);

		if (!empty($campos_secciones)) {
			foreach ($campos_secciones as $campo_default) {
				$data = array(
					'nombre_campo' => $campo_default['nombre_campo'],
					'nombre_mostrar' => $campo_default['nombre_mostrar'],
					'tipo_campo' => $campo_default['tipo_campo'],
					'es_default' => $campo_default['es_default'],
					'mostrar_formulario' => $campo_default['mostrar_formulario'],
					'requerido' => $campo_default['requerido'],
					'posicion' => $campo_default['posicion'],
					'norma_id' => $norma_id
				);

				if (isset($campo_default['valores_campo'])) {
					$data['valores_campo'] = $campo_default['valores_campo'];
				}

				//Verifico que no exista el campo para la norma
				$existe = $this->generic->get_row_from_table("compliance_secciones_campos", array("nombre_campo" => $campo_default['nombre_campo'], "norma_id" => $norma_id));
				if (!$existe) {
					$this->generic->save_on_table("compliance_secciones_campos", $data);
				} else {
					//Si ya existe lo seteo como default y pongo el requerido default
					$data_update = array(
						'es_default' => 1,
						'requerido' => $campo_default['requerido'],
						'tipo_campo' => $campo_default['tipo_campo']
					);

					if (isset($campo_default['valores_campo'])) {
						$data_update['valores_campo'] = $campo_default['valores_campo'];
					}

					$this->generic->update("compliance_secciones_campos", array('id' => $existe->id), $data_update);
				}
			}
		}

		return true;
	}

	public function calcular_resultado_con_operador($valor_1, $valor_2, $operador, $resultado = 0)
	{
		//Siempre devuelvo el valor default
		switch ($operador) {
			case '-':
				$resultado = $valor_1 - $valor_2;
				break;
			case '+':
				$resultado = $valor_1 + $valor_2;
				break;
			case '*':
				$resultado = $valor_1 * $valor_2;
				break;
			case '*(1-':
				$resultado = $valor_1 * (1 - $valor_2);
				break;
			case '/':
				if ($valor_1 > 0 && $valor_2 > 0) {
					$resultado = $valor_1 / $valor_2;
				} else {
					$resultado = 0;
				}
				break;
			default:
				$resultado = $valor_1 - $valor_2;
				break;
		}

		return $resultado;
	}

	//Dado un valor de residual comparo contra los valores y operadores configurados
	public function get_resultado_comparador_residuales($riesgos_residuales, $residual_calculado, $resultado)
	{
		if (!empty($riesgos_residuales)) {
			$tiene_resultado = false;
			foreach ($riesgos_residuales as $residual) {
				switch ($residual->operador) {
					case '<':
						if ($residual_calculado < $residual->valor) {
							$resultado = $residual->id;
							$tiene_resultado = true;
						}
						break;
					case '<=':
						if ($residual_calculado <= $residual->valor) {
							$resultado = $residual->id;
							$tiene_resultado = true;
						}
						break;
					case '=':
						if ($residual_calculado == $residual->valor) {
							$resultado = $residual->id;
							$tiene_resultado = true;
						}
						break;
					case '>=':
						if ($residual_calculado >= $residual->valor) {
							$resultado = $residual->id;
							$tiene_resultado = true;
						}
						break;
					case '>':
						if ($residual_calculado > $residual->valor) {
							$resultado = $residual->id;
							$tiene_resultado = true;
						}
						break;
					default:
						break;
				}

				//Corto ejecucion foreach
				if ($tiene_resultado) {
					break;
				}
			}
		}

		return $resultado;
	}

	protected function validar_password($password)
	{
		$patron = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@#$%^&+=*.,;_-])(?=.*[a-zA-Z0-9]).{8,}$/';

		if (preg_match($patron, $password)) {
			// La contraseña cumple con el patrón
			return true;
		} else {
			// La contraseña no cumple con el patrón
			return false;
		}
	}

	protected function crear_campos_default_catalogo_sti($catalogo_id)
	{
		$posicion_array_campo = 0;
		$sti_ocultar_punto_acceso_unificado = $this->get_variable("sti_ocultar_punto_acceso_unificado");
		$sti_ocultar_informacion_adicional_proveedor = $this->get_variable("sti_ocultar_informacion_adicional_proveedor");

		//Agrego los campos base
		$campos_base = array();

		if ($sti_ocultar_punto_acceso_unificado) {
			$pau_habilitado = 0;
		} else {
			$pau_habilitado = 1;
		}

		if ($sti_ocultar_informacion_adicional_proveedor) {
			$informacion_adicional_visible = 0;
		} else {
			$informacion_adicional_visible = 1;
		}

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'responsable_pau';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Responsable del PAU';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $pau_habilitado;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 1;
		$campos_base[$posicion_array_campo]['orden_listado'] = 1;
		$campos_base[$posicion_array_campo]['tipo'] = 'texto';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'PAU (Punto de Acceso Unificado)';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'domicilio_pau';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Domicilio PAU';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $pau_habilitado;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 2;
		$campos_base[$posicion_array_campo]['orden_listado'] = 2;
		$campos_base[$posicion_array_campo]['tipo'] = 'texto';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'PAU (Punto de Acceso Unificado)';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'fecha_alta_pau';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Fecha Alta del PAU';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $pau_habilitado;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 3;
		$campos_base[$posicion_array_campo]['orden_listado'] = 3;
		$campos_base[$posicion_array_campo]['tipo'] = 'fecha';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'PAU (Punto de Acceso Unificado)';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'fecha_baja_pau';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Fecha Baja del PAU';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $pau_habilitado;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 4;
		$campos_base[$posicion_array_campo]['orden_listado'] = 4;
		$campos_base[$posicion_array_campo]['tipo'] = 'fecha';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'PAU (Punto de Acceso Unificado)';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'referente';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Referente del proveedor';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $informacion_adicional_visible;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 5;
		$campos_base[$posicion_array_campo]['orden_listado'] = 5;
		$campos_base[$posicion_array_campo]['tipo'] = 'texto';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'Información adicional sobre el proveedor';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'email';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Email del proveedor';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $informacion_adicional_visible;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 6;
		$campos_base[$posicion_array_campo]['orden_listado'] = 6;
		$campos_base[$posicion_array_campo]['tipo'] = 'texto';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'Información adicional sobre el proveedor';
		$posicion_array_campo++;

		$campos_base[$posicion_array_campo]['nombre_campo'] = 'domicilio';
		$campos_base[$posicion_array_campo]['nombre_mostrar'] = 'Domicilio del proveedor';
		$campos_base[$posicion_array_campo]['campo_base'] = 0;
		$campos_base[$posicion_array_campo]['habilitado'] = $informacion_adicional_visible;
		$campos_base[$posicion_array_campo]['visible_listado'] = 0;
		$campos_base[$posicion_array_campo]['orden_abm'] = 7;
		$campos_base[$posicion_array_campo]['orden_listado'] = 7;
		$campos_base[$posicion_array_campo]['tipo'] = 'texto';
		$campos_base[$posicion_array_campo]['nombre_seccion'] = 'Información adicional sobre el proveedor';
		$posicion_array_campo++;

		foreach ($campos_base as $campo) {
			$where_campo_catalogo = array(
				'catalogo_id' => $catalogo_id,
				'nombre_campo' => $campo['nombre_campo']
			);

			$existe = $this->generic->get_row_from_table("catalogos_sti_campos_encuadramiento", $where_campo_catalogo);
			if (!$existe) {
				$campo['catalogo_id'] = $catalogo_id;
				$this->generic->save_on_table("catalogos_sti_campos_encuadramiento", $campo);
			}
		}
	}

	function generate_secure_password($length = 16)
	{
		//Mix de caracteres permitidos
		$upperCase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$lowerCase = 'abcdefghijklmnopqrstuvwxyz';
		$numbers = '0123456789';
		$specialCharacters = '!@#$%*()-_=+.';

		$password = [
			$upperCase[random_int(0, strlen($upperCase) - 1)],
			$lowerCase[random_int(0, strlen($lowerCase) - 1)],
			$numbers[random_int(0, strlen($numbers) - 1)],
			$specialCharacters[random_int(0, strlen($specialCharacters) - 1)]
		];

		$allCharacters = $upperCase . $lowerCase . $numbers . $specialCharacters;

		for ($i = count($password); $i < $length; $i++) {
			$password[] = $allCharacters[random_int(0, strlen($allCharacters) - 1)];
		}

		shuffle($password);

		return implode('', $password);
	}

	/* Funcion para verificar si la fila a importar se encuentra vacía */
	protected function es_fila_vacia($fila)
	{
		$esta_vacia = true;
		if ($fila && !empty($fila)) {
			foreach ($fila as $columna) {
				if (trim($columna) != '') {
					$esta_vacia = false;
				}
			}
		}
		return $esta_vacia;
	}

	protected function cargar_session_externo($datos_usuario_externo)
	{
		//Creó la sesión de usuario externo
		$_SESSION['userdata']['user_id'] = $datos_usuario_externo->id;
		$_SESSION['userdata']['nombre'] = $datos_usuario_externo->nombre;
		$_SESSION['userdata']['email'] = $datos_usuario_externo->email;
		$_SESSION['userdata']['rol'] = "Externo";
		$_SESSION['userdata']['lang'] = 1;

		//Agrego token para validacion de formularios
		$_SESSION['form_token'] = md5(uniqid(mt_rand(), true));

		return true;
	}

	protected function redirect_after_login()
	{
		if (isset($_SESSION['callback_url']) && $_SESSION['callback_url'] != '') {
			$last_url = $_SESSION['callback_url'];
			unset($_SESSION['callback_url']);
			redirect($last_url);
		} else {
			redirect(base_url('dashboard'));
		}
	}
}
