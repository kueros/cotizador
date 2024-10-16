<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\Permiso_x_RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\OrderShipmentController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\PermisoController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
	#return view('welcome');
	return redirect()->route('login');
});

Route::get('/session/check', function () {
    if (Auth::check()) {
        return response()->json(['session' => 'active'], 200);
    } else {
        return response()->json(['session' => 'expired'], 401);
    }
});


Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});
//->middleware('custom.csrf')
Route::middleware('auth')->group(function () {
	Route::get('/users', [UserController::class, 'index'])->name('users.index'); #agregar estado y mensaje para mostrar modalcita con resultado de la acción realizada.
	Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');
/* 	Route::get('/admin', function () {
		// Solo los usuarios con el rol de 'admin' pueden acceder
	})->middleware('role:Administrador');
 */	Route::get('/users/create', [UserController::class, 'create'])->name('users.create')->middleware('role:Administrador');
	Route::post('/users', [UserController::class, 'store'])->name('users.store');
	Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
	#Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
	Route::patch('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
	Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
	Route::post('/users/options', [UserController::class, 'options'])->name('users.options');
	Route::get('/users/fields', [UserController::class, 'fields'])->name('users.fields');
// Ruta para mostrar el formulario de cambio de contraseña
	Route::get('/users/{id}/password', [UserController::class, 'showPasswordForm'])->name('users.showPasswordForm');
// Ruta para actualizar la contraseña
	Route::patch('/users/{id}/password', [UserController::class, 'updatePassword'])->name('password.update');
// Ruta para blanquear la contraseña
	Route::patch('/users/{id}/blanquear_password', [UserController::class, 'blanquear_password'])->name('users.blanquear_password');
// Ruta para deshabilitar usuarios
	Route::patch('/users/{id}/deshabilitar', [UserController::class, 'deshabilitar_usuario'])->name('users.deshabilitar_usuario');
});

Route::middleware('auth')->group(function () {
	Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
	Route::get('/show/{id}', [PermisoController::class, 'show'])->name('permisos.show');
	Route::get('/permisos/create', [PermisoController::class, 'create'])->name('permisos.create');
	Route::post('/permisos', [PermisoController::class, 'store'])->name('permisos.store');
	Route::get('/permisos/{permiso}/edit', [PermisoController::class, 'edit'])->name('permisos.edit');
	Route::patch('/permisos/{permiso}', [PermisoController::class, 'update'])->name('permisos.update');
	Route::delete('/permisos/{permiso}', [PermisoController::class, 'destroy'])->name('permisos.destroy');
	Route::post('/permisos/options', [PermisoController::class, 'options'])->name('permisos.options');
	Route::get('/permisos/fields', [PermisoController::class, 'fields'])->name('permisos.fields');
    Route::post('/permisos/update-order', [PermisoController::class, 'updateOrder'])->name('permisos.updateOrder');

	Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
	Route::post('/permisos/reordenar', [PermisoController::class, 'reordenar'])->name('permisos.reordenar');
});

Route::middleware('auth')->group(function () {
	Route::get('/permisos_x_rol', [Permiso_x_RolController::class, 'index'])->name('permisos_x_rol.index');
	Route::post('/permisos_x_rol/update', [Permiso_x_RolController::class, 'updatePermisos'])->name('permisos_x_rol.update');
});

Route::middleware('auth')->group(function () {
	Route::get('/roles', [RolController::class, 'index'])->name('roles.index');
	Route::get('/show/{id}', [RolController::class, 'show'])->name('roles.show');
	Route::get('/roles/create', [RolController::class, 'create'])->name('roles.create');
	Route::post('/roles', [RolController::class, 'store'])->name('roles.store');
	Route::get('/roles/{rol}/edit', [RolController::class, 'edit'])->name('roles.edit');
	Route::patch('/roles/{rol}', [RolController::class, 'update'])->name('roles.update');
	Route::delete('/roles/{rol}', [RolController::class, 'destroy'])->name('roles.destroy');
	Route::post('/roles/options', [RolController::class, 'options'])->name('roles.options');
	Route::get('/roles/fields', [RolController::class, 'fields'])->name('roles.fields');
});


Route::middleware('auth')->group(function () {
	Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
	Route::get('/monitoreo/log_accesos', [MonitoreoController::class, 'log_accesos'])->name('monitoreo.log_accesos');
	Route::get('/monitoreo/log_administracion', [MonitoreoController::class, 'log_administracion'])->name('monitoreo.log_administracion');
	Route::get('/monitoreo/log_notificaciones', [MonitoreoController::class, 'log_notificaciones'])->name('monitoreo.log_notificaciones');
	Route::get('/monitoreo/log_emails', [MonitoreoController::class, 'log_emails'])->name('monitoreo.log_emails');
});

Route::middleware('auth')->group(
	function () {
		Route::post('/configuracion/remitente', [ConfiguracionController::class, 'guardar_remitente'])->name('configuracion.guardar_remitente');
		Route::get('/configuracion/mail', [ConfiguracionController::class, 'enviar_mail'])->name('configuracion.enviar_mail');
		Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');
		Route::post('/configuracion/guardar_estado', [ConfiguracionController::class, 'guardar_estado'])->name('configuracion.guardar_estado');
		Route::post('/configuracion/add_parametro_email', [ConfiguracionController::class, 'add_parametro_email'])->name('configuracion.add_parametro_email');
		Route::get('/configuracion/variables', [ConfiguracionController::class, 'variables'])->name('configuracion.variables');
		Route::post('/configuracion/variables', [ConfiguracionController::class, 'store'])->name('configuracion.variables.store');
		Route::get('/configuracion/variables/create', [ConfiguracionController::class, 'create'])->name('configuracion.variables.create');
		Route::get('/configuracion/variables/{variable}/edit', [ConfiguracionController::class, 'edit'])->name('configuracion.variables.edit');
		Route::patch('/configuracion/variables/{variable}', [ConfiguracionController::class, 'update'])->name('configuracion.variables.update');
		Route::delete('/configuracion/variables/{variable}', [ConfiguracionController::class, 'destroy'])->name('configuracion.variables.destroy');
		Route::post('/configuracion/ajax_delete_parametro_email', [ConfiguracionController::class, 'ajax_delete_parametro_email'])->name('configuracion.ajax_delete_parametro_email');

	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/send/mail', [OrderShipmentController::class, 'store'])->name('enviarmail');
		Route::get('/obtenerusername', [MyController::class, 'get_username'])->name('obtenerusername1');
	}
);


require __DIR__ . '/auth.php';

