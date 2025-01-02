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
use App\Models\Seccion;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;

class PermisoController extends Controller
{

	public $myController;

	public function __construct(MyController $myController)
	{
		$this->myController = $myController;
	}


	public function index()
	{
		#$permisos = DB::table('permisos')
		#	->orderBy('seccion_id')
		#	->orderBy('orden')
		#	->get();
		$secciones = Seccion::get();
		$permisos = Permiso::orderBy('seccion_id')->get();
		$roles = Rol::get();
		#$permisosRoles = Permiso_x_Rol::get();
		#dd($roles);

		return view('permiso.index', compact('roles', 'permisos', 'secciones'));

		#return view('permiso.index', compact('permisos'));
	}


	public function permisosOrden()
	{
		dd("kdk");
		$permisos = DB::table('permisos')
			->orderBy('seccion_id')
			->orderBy('orden')
			->get();

		return view('permiso.permisosOrden', compact('permisos'));
	}

	public function reordenar(Request $request)
	{
		$nuevoOrden = $request->input('orden');

		foreach ($nuevoOrden as $index => $permisoId) {
			DB::table('permisos')
				->where('id', $permisoId)
				->update(['orden' => $index + 1]);
		}

		return response()->json(['success' => 'Permisos reordenados correctamente.']);
	}

	/**
	 * Show the form for creating a new resource.
	 */
	public function create(): View
	{
		$permisos = new Permiso();
		$permisoUltimoId = Permiso::orderBy('id', 'desc')->first()['id'];
		#dd($permisoUltimoId);
		$secciones = Seccion::get();
		return view('permiso.create', compact('permisos', 'secciones', 'permisoUltimoId'));
	}

	public function store(Request $request, MyController $myController): RedirectResponse
	{
		// Validar los datos del permiso
		$messages = [
			'id' => 'Ese ID ya está utilizado en otro permiso',
			'nombre' => 'Ese nombre de permiso ya está utilizado',
			'descripcion' => 'Esa descripción de permiso ya está utilizada',
			'orden' => 'Ese orden de permiso ya está utilizado',
		];

		$validatedData = $request->validate([
			'id' => 'required|int|unique:permisos,id',
			'nombre' => 'required|string|max:255|unique:permisos,nombre',
			'descripcion' => 'required|string|max:255',
			'orden' => 'required|int',
			'seccion_id' => 'required|int',
		], $messages);

		// Crear el permiso en la base de datos
		$permiso = Permiso::create($validatedData);

		// Lógica para actualizar el archivo PermisosSeeder.php
		$this->updatePermisosSeeder();

		// Enviar correo y otros procesos adicionales
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$action = "permisos.store";
		$message = $username . " creó el permiso " . $_POST['nombre'];
		$subject = "Creación de permiso";
		$body = "Permiso " . $_POST['nombre'] . " creado correctamente por " . Auth::user()->username;
		$to = Auth::user()->email;
		$myController->enviar_email($to, $body, $subject);
		Log::info('Correo enviado exitosamente a ' . $to);

		return Redirect::route('permisos.index')
			->with('success', 'Permiso created successfully.');
	}

	private function updatePermisosSeeder()
	{
		// Obtener todos los permisos actuales
		$permisos = Permiso::all();

		// Generar el contenido del archivo de seed
		$seedContent = "<?php\n\n";
		$seedContent .= "namespace Database\\Seeders;\n\n";
		$seedContent .= "use Illuminate\\Database\\Seeder;\n";
		$seedContent .= "use Illuminate\\Support\\Facades\\DB;\n\n";
		$seedContent .= "class PermisosSeeder extends Seeder\n";
		$seedContent .= "{\n";
		$seedContent .= "    public function run()\n";
		$seedContent .= "    {\n";
		$seedContent .= "        DB::table('permisos')->truncate();\n\n";
		$seedContent .= "        DB::table('permisos')->insert([\n";

		foreach ($permisos as $permiso) {
			$seedContent .= "            [\n";
			$seedContent .= "                'id' => {$permiso->id},\n";
			$seedContent .= "                'nombre' => '{$permiso->nombre}',\n";
			$seedContent .= "                'descripcion' => '{$permiso->descripcion}',\n";
			$seedContent .= "                'orden' => {$permiso->orden},\n";
			$seedContent .= "                'seccion_id' => {$permiso->seccion_id},\n";
			$seedContent .= "                'created_at' => '{$permiso->created_at}',\n";
			$seedContent .= "                'updated_at' => '{$permiso->updated_at}',\n";
			$seedContent .= "            ],\n";
		}

		$seedContent .= "        ]);\n";
		$seedContent .= "    }\n";
		$seedContent .= "}\n";

		// Guardar el contenido en PermisosSeeder.php
		$filePath = database_path('seeders/PermisosSeeder.php');
		file_put_contents($filePath, $seedContent);
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
		#dd($request);
		$messages = [
			'id' => 'Ese ID ya está utilizado en otro permiso',
			'nombre' => 'Ese nombre de permiso ya está utilizado',
			'descripcion' => 'Ese nombre de permiso ya está utilizado',
			'orden' => 'Ese nombre de permiso ya está utilizado',
		];
		// Validar los datos del permiso, ignorando al permiso actual
		$validatedData = $request->validate([
			'id' => 'required|int|unique:permisos,id,' . $permiso->id . ',id',
			'nombre' => 'required|string|max:255|unique:permisos,nombre,' . $permiso->nombre . ',nombre',
			'descripcion' => 'required|string|max:255',
			'orden' => 'required|int',
		], $messages);

		$permiso->update($validatedData);
		$clientIP = \Request::ip();
		$userAgent = \Request::userAgent();
		$username = Auth::user()->username;
		$action = "permisos.update";
		$message = $username . " actualizó el permiso " . $_POST['nombre'];
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
		$message = Auth::user()->username . " eliminó el permiso " . $nombre;
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
}
