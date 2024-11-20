<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use App\Models\TipoTransaccionCampoAdicional;
use App\Models\TipoCampo;
use App\Models\Transaccion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TipoTransaccionCampoAdicionalController extends Controller
{
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function index(Request $request, MyController $myController): View
	{
		/* 		$permiso_listar_roles = $myController->tiene_permiso('list_roles');
		if (!$permiso_listar_roles) {
			abort(403, '.');
			return false;
		}
 */



		$tipos_campos = TipoCampo::all();
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->paginate();
		return view('tipos_transacciones_campos_adicionales.index', compact('campos_adicionales', 'tipos_campos'))
			->with('i', ($request->input('page', 1) - 1) * $campos_adicionales->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		#dd($request);
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
		->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
		->get();

		$data = array();
		foreach ($campos_adicionales as $r) {

			$definirCamposUrl = route('tipos_transacciones_campos_adicionales.edit', $r->id);
			$accion = '<a class="btn btn-sm btn-primary" href="' . $definirCamposUrl . '" title="Definir Campos">Definir Campos</a>';

			#$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-trash"></i></a>';
			$data[] = array(
				$r->nombre_campo,
				$r->nombre_mostrar,
				$r->orden_listado,
				$r->requerido,
				$r->tipo_nombre,
				$r->valores,
				$accion
			);
		}
		$output = array(
			"recordsTotal" => $campos_adicionales->count(),
			"recordsFiltered" => $campos_adicionales->count(),
			"data" => $data
		);

		return response()->json($output);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_edit($id)
	{
		#dd($id);
		#$data = TipoTransaccionCampoAdicional::where('id',$id)->first();
		$data = TipoTransaccionCampoAdicional::find($id);

		if (!$data) {
			return response()->json(['error' => 'Registro no encontrado'], 404);
		}

		return response()->json($data);
	}
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_delete($id, MyController $myController)
	{
		/*$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
         		if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		} */
		$campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		$nombre = $campos_adicionales->nombre;
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " borró el tipo de transacción " . $nombre;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$campos_adicionales->delete();
		return response()->json(["status" => true]);
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_guardar_columna()
	{
		$nombre_campo = str_replace(' ','_',strtolower($_POST['nombre_campo']));
		$nombre_campo = substr($nombre_campo, 0, 60);
		#$tipo_trasaccion_id = $_POST['tipo_trasaccion_id'];
		$existe = Transaccion::where('nombre', $nombre_campo)->first();
		#$existe = $this->generic->get_row_from_table("compliance_secciones_campos",array("norma_id" => $norma_id, "nombre_mostrar" => $_POST['nombre']));
		if($existe){
			echo "El campo ya existe";
			return false;
		}
#print_r($_POST);
		switch($_POST['tipo']){
			case '2'://Verifico que tenga valores
				if(!isset($_POST['valores']) || empty($_POST['valores'])){
					echo "Se deben agregar valores para el selector";
					return false;
				}
				break;
			default:
				break;
		}

		if(isset($_POST['requerido'])){
			$requerido = 1;
		}else{
			$requerido = 0;
		}

		//Creo el campo si no existe
		$data_campo = array(
			'nombre_campo' => $nombre_campo,
			'nombre_mostrar' => $_POST['nombre_mostrar'],
			'tipo' => $_POST['tipo'],
			'requerido' => $requerido,
			'posicion' => $_POST['posicion']#,
			#'tipo_trasaccion_id' => $tipo_trasaccion_id
		);

		if(!empty($_POST['valores']) && $_POST['tipo'] == 4){
			$data_campo['valores_campo'] = json_encode($_POST['valores']);
		}

/* 			if($_POST['tipo_campo'] == 5){
			$data_campo['nivel_id'] = $_POST['nivel_modelo_id'];
		}
*/
		$inserted_id = TipoTransaccionCampoAdicional::create($data_campo);
		#$inserted_id = $this->generic->save_on_table("compliance_secciones_campos",$data_campo);
		#$datos_transaccion = TipoTransaccionCampoAdicional::find($tipo_trasaccion_id);
		#$this->compliance->get_by_id($norma_id);

		$tabla = 'transacciones';
		$columna = $nombre_campo;

		if($inserted_id){

			if(!Schema::hasColumn($tabla, $columna)){
				Schema::table($tabla, function(Blueprint $table) use ($columna){
					$table->string($columna)->nullable()->after('nombre');
				});
			}

/* 			if(!$this->generic->check_field_exist($nombre_campo, "compliance_secciones")){
				$query_update = "ALTER TABLE compliance_secciones ADD COLUMN ".$nombre_campo." TEXT";
				$this->generic->run_query($query_update);
			}
 */
/* 			$this->guardar_log('Creó el campo con el nombre "'.$_POST['nombre'].'" para las secciones de la Norma Compliance "'.$datos_norma->nombre.'"');
			$this->session->set_flashdata('success_message', 'Columna creada.');
 */
			#echo "true";
			return true;
		}else{
			return false;
		}




		return true;
		#$this->asignar_permiso();

		//Verifico que la columna no exista
		#$nombre_sin_caracteres = $this->eliminar_caracteres_invalidos($_POST['nombre']);
		$nombre_campo = str_replace(' ','_',strtolower($nombre_sin_caracteres));
		$nombre_campo = substr($nombre_campo, 0, 60);
		$tipo_trasaccion_id = $_POST['tipo_trasaccion_id'];

		$existe = $this->generic->get_row_from_table("transacciones",array("tipo_trasaccion_id" => $tipo_trasaccion_id, "nombre_mostrar" => $_POST['nombre']));
		if($existe){
			echo "El campo ya existe";
			return false;
		}

		switch($_POST['tipo_campo']){
			case '2'://Verifico que tenga valores
				if(!isset($_POST['valores']) || empty($_POST['valores'])){
					echo "Se deben agregar valores para el selector";
					return false;
				}
				break;
			case '5':
				if(!isset($_POST['nivel_modelo_id']) || $_POST['nivel_modelo_id'] == ''){
					echo "Se debe seleccionar un nivel del modelo de negocio";
					return false;
				}
				break;
			default:
				break;
		}

		if(isset($_POST['requerido'])){
			$requerido = 1;
		}else{
			$requerido = 0;
		}

		//Creo el campo si no existe
		$data_campo = array(
			'nombre_campo' => $nombre_campo,
			'nombre_mostrar' => $_POST['nombre_mostrar'],
			'tipo_campo' => $_POST['tipo'],
			'requerido' => $requerido,
			'posicion' => $_POST['posicion'],
			'tipo_trasaccion_id' => $tipo_trasaccion_id
		);

		if(!empty($_POST['valores']) && $_POST['tipo_campo'] == 2){
			$data_campo['valores_campo'] = json_encode($_POST['valores']);
		}

		if($_POST['tipo_campo'] == 5){
			$data_campo['nivel_id'] = $_POST['nivel_modelo_id'];
		}

		$inserted_id = $this->generic->save_on_table("compliance_secciones_campos",$data_campo);

		$datos_norma = $this->compliance->get_by_id($norma_id);

		if($inserted_id){
			if(!$this->generic->check_field_exist($nombre_campo, "compliance_secciones")){
				$query_update = "ALTER TABLE compliance_secciones ADD COLUMN ".$nombre_campo." TEXT";
				$this->generic->run_query($query_update);
			}

			$this->guardar_log('Creó el campo con el nombre "'.$_POST['nombre'].'" para las secciones de la Norma Compliance "'.$datos_norma->nombre.'"');
			$this->session->set_flashdata('success_message', 'Columna creada.');

			echo "true";
			return true;
		}else{
			return false;
		}
	}


	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function create(MyController $myController): View
	{
		/* 		$permiso_agregar_roles = $myController->tiene_permiso('add_rol');
		if (!$permiso_agregar_roles) {
			abort(403, '.');
			return false;
		} */
		$campos_adicionales = new CampoAdicionalTipoTransaccion();
		return view('rol.create', compact('roles'));
	}

	/*******************************************************************************************************************************
	 * 
	 ********************************************************************************************************************************/

	public function store(Request $request, MyController $myController): RedirectResponse
	{
		#dd($request->valores);
		/*     $permiso_agregar_roles = $myController->tiene_permiso('add_rol');
    if (!$permiso_agregar_roles) {
        abort(403, '.');
        return false;
    }
 */
		#dd($request->valores);

		// Validar los datos del usuario
		$validatedData = $request->validate([
			'nombre_campo' => [
				'required',
				'string',
				'max:255',
				'min:3',
				'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
				Rule::unique('tipos_transacciones_campos_adicionales'),
			],
			'nombre_mostrar' => [
				'string',
				'max:255',
				'min:3',
				'regex:/^[\pL\s]+$/u', // Permitir solo letras y espacios
			],
			'visible' => [
				'integer',
			],
			'requerido' => [
				'integer',
			],
			'tipo' => [
				'integer',
			],
			[
				'nombre_campo.regex' => 'El nombre solo puede contener letras y espacios.',
				'nombre_campo.unique' => 'Este nombre de tipo de transacción ya está en uso.',
			]
		]);

		/*         $tipoTransacciónExistente = CampoAdicionalTipoTransaccion::where('nombre', $request->input('nombre'))->first();
        if ($tipoTransacciónExistente) {
            return redirect()->back()->withErrors(['nombre' => 'Este nombre de tipo de transacción ya está en uso.'])->withInput();
        }
		 */

		$campo = TipoTransaccionCampoAdicional::create($validatedData);
		$valores = implode(',', $request->valores);
		$campo->valores = $valores;
		$campo->save();
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el campo adicional para tipo de transacción " . $request->input('nombre');
		$myController->loguear($clientIP, $userAgent, $username, $message);

		return Redirect::route('tipos_transacciones_campos_adicionales')->with('success', 'Campo adicional para tipo de transacción creado exitosamente.');
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/

	public function update(Request $request, $id)
	{
		#dd($request);
		// Validar los datos
		$request->merge([
			'visible' => $request->has('visible') ? 1 : 0,
			'requerido' => $request->has('requerido') ? 1 : 0,
		]);
		$validatedData = $request->validate([
			'nombre_campo' => 'required|string|max:255',
			'nombre_mostrar' => 'required|string|max:255',
			'requerido' => 'required|integer',
			'tipo' => 'required|integer',
			'valor_default' => 'max:255',
		]);		// Obtener el modelo
		#dd($validatedData);
		$tipo_transaccion_campo_adicional = TipoTransaccionCampoAdicional::findOrFail($id);
		// Actualizar el modelo con los datos validados
		$tipo_transaccion_campo_adicional->update($validatedData);

		return redirect()->route('tipos_transacciones_campos_adicionales')->with('success', 'Campo adicional de tipo de transacción actualizado correctamente.');
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/

	 public function edit($id, MyController $myController): View
	{
		#dd($id);
		/* 		$permiso_editar_roles = $myController->tiene_permiso('edit_rol');
		if (!$permiso_editar_roles) {
			abort(403, '.');
			return false;
		}
 */
/* 		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		#dd($roles);
		return view('tipos_transacciones_campos_adicionales.edit', compact('tipos_transacciones_campos_adicionales'));
 */
		$tipos_campos = TipoCampo::all();
		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::
			where('tipos_transacciones_campos_adicionales.id', $id)
			->leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->first();
		return view('tipos_transacciones_campos_adicionales.edit', compact('tipos_transacciones_campos_adicionales', 'tipos_campos'));
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function destroy($id, MyController $myController): RedirectResponse
	{
		/* 		$permiso_eliminar_roles = $myController->tiene_permiso('del_rol');
		if (!$permiso_eliminar_roles) {
			abort(403, '.');
			return false;
		}
 */
		$tipos_transacciones_campos_adicionales = TipoTransaccionCampoAdicional::find($id);
		// Almacena el nombre de rol antes de eliminarlo
		$nombre = $tipos_transacciones_campos_adicionales->nombre_campo;
		// Elimina el rol
		$tipos_transacciones_campos_adicionales->delete();
		$message = Auth::user()->username . " borró el campo adicional de tipo de transacción " . $nombre;
		Log::info($message);
		return Redirect::route('tipos_transacciones_campos_adicionales')
			->with('success', 'Campo adicional de tipo de transacción exitosamente borrado');
	}


}
