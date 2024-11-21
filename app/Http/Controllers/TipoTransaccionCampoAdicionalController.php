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
use App\Models\TipoTransaccion;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

class TipoTransaccionCampoAdicionalController extends Controller
{
	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function index($id, Request $request, MyController $myController): View
	#public function index(Request $request, MyController $myController): View
	{
		/* 		$permiso_listar_roles = $myController->tiene_permiso('list_roles');
		if (!$permiso_listar_roles) {
			abort(403, '.');
			return false;
		}
 */

		$tipo_transaccion_nombre = TipoTransaccion::where('id', $id)->first()['nombre'];
		$tipos_campos = TipoCampo::all();
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->paginate();
		return view('tipos_transacciones_campos_adicionales.index', compact('campos_adicionales', 'tipos_campos', 'id', 'tipo_transaccion_nombre'))
			->with('i', ($request->input('page', 1) - 1) * $campos_adicionales->perPage());
	}

	/*******************************************************************************************************************************
	 *******************************************************************************************************************************/
	public function ajax_listado(Request $request)
	{
		$tipo_transaccion_id = $request->input('tipo_transaccion_id');
   
		$campos_adicionales = TipoTransaccionCampoAdicional::leftJoin('tipos_campos', 'tipos_transacciones_campos_adicionales.tipo', '=', 'tipos_campos.id')
			->select('tipos_campos.nombre as tipo_nombre', 'tipos_transacciones_campos_adicionales.*')
			->where('tipo_transaccion_id', $tipo_transaccion_id)
			->get();
		$data = array();
		foreach ($campos_adicionales as $r) {

			$definirCamposUrl = route('tipos_transacciones_campos_adicionales.edit', $r->id);
			$accion = '<a class="btn btn-sm btn-primary" href="' . $definirCamposUrl . '" title="Editar Campos"><i class="bi bi-pencil"></i></a>';

			$accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_campos_adicionales(' . "'" . $r->id . "'" . ')"><i class="bi bi-trash"></i></a>';
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
		#print_r($id);
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
	public function ajax_guardar_columna(MyController $myController)
	{
		$nombre_campo = str_replace(' ','_',strtolower($_POST['nombre_campo']));
		$nombre_campo = substr($nombre_campo, 0, 60);
		#$tipo_trasaccion_id = $_POST['tipo_trasaccion_id'];
		$existe = TipoTransaccionCampoAdicional::where('nombre_campo', $nombre_campo)->first();
		#$existe = $this->generic->get_row_from_table("compliance_secciones_campos",array("norma_id" => $norma_id, "nombre_mostrar" => $_POST['nombre']));
		if($existe){
			echo "El campo ya existe";
			return false;
		}
#print_r ($_POST);
		switch($_POST['tipo']){
			case '4'://Verifico que tenga valores
				if(!isset($_POST['valores']) || empty($_POST['valores'])){
					echo "Se deben agregar valores para el selector";
					return false;
				}
				break;
			default:
				break;
		}
		$requerido = $_POST['requerido'] ?? 0;

		//Creo el campo si no existe
		$data_campo = array(
			'nombre_campo' => $nombre_campo,
			'nombre_mostrar' => $_POST['nombre_mostrar'],
			'tipo' => $_POST['tipo'],
			'requerido' => $requerido,
			'orden_listado' => $_POST['posicion'],
			'tipo_trasaccion_id' => $_POST['tipo_transaccion_id'],
		);
#print_r($data_campo);
		$inserted_id = TipoTransaccionCampoAdicional::create($data_campo);

 		if(!empty($_POST['valores']) && $_POST['tipo'] == 4){
			#$valores = implode(',', $_POST['valores']);
			$valores = json_encode($_POST['valores']);
			$inserted_id->valores = $valores;
			$inserted_id->save();
		}
		$inserted_id->tipo_transaccion_id = $_POST['tipo_transaccion_id'];
		$inserted_id->save();
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$message = $username . " creó el campo adicional para tipo de transacción " . $nombre_campo;
		$myController->loguear($clientIP, $userAgent, $username, $message);

		$tabla = 'transacciones';
		$columna = $nombre_campo;

		if($inserted_id){

			if(!Schema::hasColumn($tabla, $columna)){
				Schema::table($tabla, function(Blueprint $table) use ($columna){
					$table->string($columna)->nullable()->after('nombre');
				});
			}

			#echo "true";
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
		dd($request->valores);

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
		#dd($request->valores);
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

	public function update(Request $request)
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
			'tipo' => 'required|integer',
			'requerido' => 'required|integer',
			'orden_listado' => 'required|integer',
		]);		// Obtener el modelo
		#dd($validatedData);
		$tipoTransaccionId = $request->tipo_transaccion_id;
		#dd($tipoTransaccionId);
		$tipo_transaccion_campo_adicional = TipoTransaccionCampoAdicional::findOrFail($request->id);
		// Actualizar el modelo con los datos validados
		$tipo_transaccion_campo_adicional->update($validatedData);

		#return redirect()->route('tipos_transacciones_campos_adicionales')->with('success', 'Campo adicional de tipo de transacción actualizado correctamente.');

		return redirect()->route('tipos_transacciones_campos_adicionales', ['id' => $tipoTransaccionId])
		->with('success', 'Campo adicional de tipo de transacción actualizado correctamente.');
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
		#dd($tipos_transacciones_campos_adicionales);
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
