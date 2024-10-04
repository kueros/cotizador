<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\MyController;
use App\Models\Permiso;

class PermisoControllerSuperadmin extends Controller
{

    public function __construct(MyController $myController)
    {
        $this->myController = $myController;
    }


    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        $permisos = Permiso::paginate();
        return view('permiso.index', compact('permisos'))
            ->with('i', ($request->input('page', 1) - 1) * $permisos->perPage());
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $permisos = new Permiso();
        return view('permiso.create', compact('permisos'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, MyController $myController): RedirectResponse
    {
        // Validar los datos del usuario
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:permisos,nombre',
            'registra_log' => 'required|integer',
            'orden' => 'required|integer|unique:permisos,orden',
        ]);
        Permiso::create($validatedData);
        $clientIP = \Request::ip();
        $userAgent = \Request::userAgent();
        $username = Auth::user()->username;
        $action = "permisos.store";
        $message = $username . " creó el permiso " . $_POST['nombre'];
        #$myController->loguear($clientIP, $userAgent, $username, $action, $message);
        $subject = "Creación de permiso";
        $body = "Permiso " . $_POST['nombre'] . " creado correctamente por " . Auth::user()->username;
        $to = Auth::user()->email;
        // Llamar a enviar_email de MyController
        $myController->enviar_email($to, $body, $subject);
        Log::info('Correo enviado exitosamente a ' . $to);
        return Redirect::route('permisos.index')
            ->with('success', 'Permiso created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show($id): View
    {
        $permiso = Permiso::find($id);
        return view('permiso.show', compact('permiso'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id): View
    {
        $permiso = Permiso::find($id);
        return view('permiso.edit', compact('permiso'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Permiso $permiso, MyController $myController): RedirectResponse
    {
        // Validar los datos del usuario
        $validatedData = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
        ]);
        $permiso->update($validatedData);
        $clientIP = \Request::ip();
        $userAgent = \Request::userAgent();
        $username = Auth::user()->username;
        $action = "permisos.update";
        $message = $username . " actualizó el permiso " . $_POST['nombre'];
        #$myController->loguear($clientIP, $userAgent, $username, $action, $message);
        $subject = "Actualización de permiso";
        $body = "Permiso " . $_POST['nombre'] . " actualizado correctamente por " . Auth::user()->username;
        $to = Auth::user()->email;
        // Llamar a enviar_email de MyController
        $myController->enviar_email($to, $body, $subject);
        Log::info('Correo enviado exitosamente a ' . $to);
        return Redirect::route('permisos.index')
            ->with('success', 'Permiso updated successfully');
    }

    public function destroy($id, MyController $myController): RedirectResponse
    {
        $permiso = Permiso::find($id);
        // Almacena el nombre de permiso antes de eliminarlo
        $nombre = $permiso->nombre;
        // Elimina el permiso
        $permiso->delete();
        $message = Auth::user()->username . " borró el permiso " . $nombre;
        Log::info($message);
        $subject = "Borrado de permiso";
        $body = "Permiso " . $nombre . " borrado correctamente por " . Auth::user()->username;
        $to = "omarliberatto@yafoconsultora.com";
        // Llamar a enviar_email de MyController
        $myController->enviar_email($to, $body, $subject);
        Log::info('Correo enviado exitosamente a ' . $to);
        return Redirect::route('permisos.index')
            ->with('success', 'Permiso deleted successfully');
    }
    public function updateOrder(Request $request)
    {
        $orden = $request->input('orden');

        foreach ($orden as $permisoData) {
            Permiso::where('id', $permisoData['id'])
                ->update(['orden' => $permisoData['orden']]);
        }

        return response()->json(['success' => true]);
    }
}
