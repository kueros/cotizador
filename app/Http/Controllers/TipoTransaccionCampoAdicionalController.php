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
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

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
        $campos_adicionales = TipoTransaccionCampoAdicional::paginate();
        return view('tipos_transacciones_campos_adicionales.index', compact('campos_adicionales', 'tipos_campos'))
            ->with('i', ($request->input('page', 1) - 1) * $campos_adicionales->perPage());
    }

    /*******************************************************************************************************************************
     *******************************************************************************************************************************/
    public function ajax_listado(Request $request)
    {
        #dd($request);
        $campos_adicionales = TipoTransaccionCampoAdicional::all();
        #dd($campos_adicionales);
        $data = array();
        foreach ($campos_adicionales as $r) {
            $accion = '<a class="btn btn-sm btn-primary" href="javascript:void(0)" title="Editar" onclick="edit_rol(' . "'" . $r->rol_id .
                "'" . ')"><i class="bi bi-pencil"></i></a>';

            if ($r->id != 1) {
                $accion .= '<a class="btn btn-sm btn-danger" href="javascript:void(0)" title="Borrar" onclick="delete_rol(' . "'" . $r->rol_id .
                    "'" . ')"><i class="bi bi-trash"></i></a>';
            }

            $data[] = array(
                $r->nombre_campo,
                $r->nombre_mostrar,
                $r->visible,
                $r->orden_listado,
                $r->requerido,
                $r->tipo_nombre,
                $r->valor_default,
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

        $data = TipoTransaccionCampoAdicional::find($id);
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
        $campos_adicionales = CampoAdicionalTipoTransaccion::find($id);
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
        #dd($request);
        /*     $permiso_agregar_roles = $myController->tiene_permiso('add_rol');
    if (!$permiso_agregar_roles) {
        abort(403, '.');
        return false;
    }
 */
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
        ], [
            'nombre_campo.regex' => 'El nombre solo puede contener letras y espacios.',
            'nombre_campo.unique' => 'Este nombre de tipo de transacción ya está en uso.',
        ]);

        /*         $tipoTransacciónExistente = CampoAdicionalTipoTransaccion::where('nombre', $request->input('nombre'))->first();
        if ($tipoTransacciónExistente) {
            return redirect()->back()->withErrors(['nombre' => 'Este nombre de tipo de transacción ya está en uso.'])->withInput();
        }
 */
dd($validatedData);
        TipoTransaccionCampoAdicional::create($validatedData);

        $clientIP = \Request::ip();
        $userAgent = \Request::userAgent();
        $username = Auth::user()->username;
        $message = $username . " creó el campo adicional para tipo de transacción " . $request->input('nombre');
        $myController->loguear($clientIP, $userAgent, $username, $message);

        return Redirect::route('tipos_transacciones_campos_adicionales')->with('success', 'Campo adicional para tipo de transacción creado exitosamente.');
    }
}
