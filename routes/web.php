<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\OrderShipmentController;
use App\Http\Controllers\MyController;


Route::get('/', function () {
	#return view('welcome');
	return redirect()->route('login');
});

Route::get('/dashboard', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
	Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware('auth')->group(function () {
	Route::get('/users', [UserController::class, 'index'])->name('users.index'); #agregar estado y mensaje para mostrar modalcita con resultado de la acción realizada.
	Route::get('/show/{id}', [UserController::class, 'show'])->name('users.show');
	Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
	Route::post('/users', [UserController::class, 'store'])->name('users.store');
	Route::get('/users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');
	Route::patch('/users/{user}', [UserController::class, 'update'])->name('users.update');
	Route::delete('/users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
	Route::post('/users/options', [UserController::class, 'options'])->name('users.options');
	Route::get('/users/fields', [UserController::class, 'fields'])->name('users.fields');
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
require __DIR__ . '/auth.php';


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
		Route::post('/configuracion', [ConfiguracionController::class, 'guardar_estado'])->name('configuracion.guardar_estado');
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



