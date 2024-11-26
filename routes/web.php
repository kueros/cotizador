<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use App\Http\Controllers\Permiso_x_RolController;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\OrderShipmentController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\PermisoController;
use App\Http\Controllers\TipoTransaccionController;
use App\Http\Controllers\TipoTransaccionCampoAdicionalController;
use App\Http\Controllers\FuncionController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;


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
/* 
Route::get('/dashboard/{imagenHome}', function () {
	return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');
*/

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
	Route::get('/users', 								[UserController::class, 'index'])->name('users.index'); #agregar estado y mensaje para mostrar modalcita con resultado de la acción realizada.
	Route::get('/show/{id}', 							[UserController::class, 'show'])->name('users.show');
	Route::get('/users/create', 						[UserController::class, 'create'])->name('users.create');
	Route::post('/users', 								[UserController::class, 'store'])->name('users.store');
	Route::get('/users/{user}/edit', 					[UserController::class, 'edit'])->name('users.edit');
	Route::put('/users/{user}', 						[UserController::class, 'usersUpdate'])->name('users.update');
	#Route::patch('/users/{user}/update', [UserController::class, 'update'])->name('users.update');
	Route::delete('/users/{user}', 						[UserController::class, 'destroy'])->name('users.destroy');
	Route::post('/users/options', 						[UserController::class, 'options'])->name('users.options');
	Route::get('/users/guardar_opciones', 				[UserController::class, 'mostrar_opciones'])->name('users.mostrar_opciones');
	Route::post('/users/guardar_opciones', 		 		[UserController::class, 'guardar_opciones'])->name('users.guardar_opciones');
	Route::patch('/users/{id}/blanquear_password', 		[UserController::class, 'blanquear_password'])->name('users.blanquear_password');
	Route::patch('/users/{id}/deshabilitar', 			[UserController::class, 'deshabilitar_usuario'])->name('users.deshabilitar_usuario');
	Route::patch('/users/{id}/deshabilitar_usuario_temporal', 	[UserController::class, 'deshabilitar_usuario_temporal'])->name('users.deshabilitar_usuario_temporal');
	Route::get('/unlock-account/{userId}', 				[UserController::class, 'unlockAccount'])->name('account.unlock');
	Route::post('/users/ajax_delete/{id}', 				[UserController::class, 'ajax_delete'])->name('users.ajax_delete');
	Route::get('/users/ajax_edit/{id}', 				[UserController::class, 'ajax_edit'])->name('users.ajax_edit');
});


// Ruta para mostrar un mensaje después del registro para que el usuario verifique su correo
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');

// Ruta para verificar el correo cuando el usuario hace clic en el enlace
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/home'); // Cambia esta ruta según tu aplicación
})->middleware(['auth', 'signed'])->name('verification.verify');

// Ruta para reenviar el correo de verificación
Route::post('/email/verification-notification', function (Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('message', 'Verification link sent!');
})->middleware(['auth', 'throttle:6,1'])->name('verification.send');


Route::middleware('auth')->group(function () {
	Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
	Route::get('/permisosOrden', [PermisoController::class, 'permisosOrden'])->name('permisos.permisosOrden');
	Route::get('/show/{id}', [PermisoController::class, 'show'])->name('permisos.show');
	Route::get('/permisos/create', [PermisoController::class, 'create'])->name('permisos.create');
	Route::post('/permisos', [PermisoController::class, 'store'])->name('permisos.store');
	Route::get('/permisos/{permiso}/edit', [PermisoController::class, 'edit'])->name('permisos.edit');
	Route::patch('/permisos/{permiso}', [PermisoController::class, 'update'])->name('permisos.update');
	Route::delete('/permisos/{permiso}', [PermisoController::class, 'destroy'])->name('permisos.destroy');
	Route::post('/permisos/options', [PermisoController::class, 'options'])->name('permisos.options');
	Route::get('/permisos/fields', [PermisoController::class, 'fields'])->name('permisos.fields');
    Route::post('/permisos/update-order', [PermisoController::class, 'updateOrder'])->name('permisos.updateOrder');

	#Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
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
	Route::put('/roles/{rol}', [RolController::class, 'update'])->name('roles.update');
	Route::delete('/roles/{rol}', [RolController::class, 'destroy'])->name('roles.destroy');
	Route::post('/roles/options', [RolController::class, 'options'])->name('roles.options');
	Route::get('/roles/fields', [RolController::class, 'fields'])->name('roles.fields');
	Route::get('/roles/ajax_listado', [RolController::class, 'ajax_listado'])->name('roles.ajax_listado');
	Route::get('/roles/ajax_edit/{id}', [RolController::class, 'ajax_edit'])->name('roles.ajax_edit');
	Route::post('/roles/ajax_delete/{id}', [RolController::class, 'ajax_delete'])->name('roles.ajax_delete');
});


Route::middleware('auth')->group(function () {
	Route::get('/monitoreo', [MonitoreoController::class, 'index'])->name('monitoreo.index');
	Route::get('/monitoreo/log_accesos', [MonitoreoController::class, 'log_accesos'])->name('monitoreo.log_accesos');
	Route::get('/monitoreo/log_administracion', [MonitoreoController::class, 'log_administracion'])->name('monitoreo.log_administracion');
	Route::get('/monitoreo/log_notificaciones', [MonitoreoController::class, 'log_notificaciones'])->name('monitoreo.log_notificaciones');
	Route::get('/monitoreo/log_emails', [MonitoreoController::class, 'log_emails'])->name('monitoreo.log_emails');
	Route::get('/monitoreo/ajax_log_acceso', [MonitoreoController::class, 'ajax_log_acceso'])->name('monitoreo.ajax_log_acceso');
	Route::get('/monitoreo/ajax_log_administracion', [MonitoreoController::class, 'ajax_log_administracion'])->name('monitoreo.ajax_log_administracion');
	Route::get('/monitoreo/ajax_log_emails', [MonitoreoController::class, 'ajax_log_emails'])->name('monitoreo.ajax_log_emails');
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
		Route::post('/guardar_imagen_home', [ConfiguracionController::class, 'guardarImagenHome'])->name('configuracion.guardar_imagen_home');
		Route::post('/guardar_imagen_login', [ConfiguracionController::class, 'guardarImagenLogin'])->name('configuracion.guardar_imagen_login');

	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/send/mail', [OrderShipmentController::class, 'store'])->name('enviarmail');
		Route::get('/obtenerusername', [MyController::class, 'get_username'])->name('obtenerusername1');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/tipos_transacciones', [TipoTransaccionController::class, 'index'])->name('tipos_transacciones.index');
		#Route::get('/show/{id}', [TipoTransaccionController::class, 'show'])->name('tipos_transacciones.show');
		Route::get('/tipos_transacciones/create', [TipoTransaccionController::class, 'create'])->name('tipos_transacciones.create');
		Route::post('/tipos_transacciones', [TipoTransaccionController::class, 'store'])->name('tipos_transacciones.store');
		Route::get('/tipos_transacciones/{id}/edit', [TipoTransaccionController::class, 'edit'])->name('tipos_transacciones.edit');
		Route::patch('/tipos_transacciones/{id}', [TipoTransaccionController::class, 'update'])->name('tipos_transacciones.update');
		Route::delete('/tipos_transacciones/{id}', [TipoTransaccionController::class, 'destroy'])->name('tipos_transacciones.destroy');
		Route::post('/tipos_transacciones/options', [TipoTransaccionController::class, 'options'])->name('tipos_transacciones.options');
		Route::get('/tipos_transacciones/fields', [TipoTransaccionController::class, 'fields'])->name('tipos_transacciones.fields');
		Route::get('/tipos_transacciones/ajax_listado', [TipoTransaccionController::class, 'ajax_listado'])->name('tipos_transacciones.ajax_listado');
		Route::get('/tipos_transacciones/ajax_edit/{id}', [TipoTransaccionController::class, 'ajax_edit'])->name('tipos_transacciones.ajax_edit');
		Route::post('/tipos_transacciones/ajax_delete/{id}', [TipoTransaccionController::class, 'ajax_delete'])->name('tipos_transacciones.ajax_delete');
	}
);
Route::middleware('auth')->group(
	function () {
		Route::get('/tipos_transacciones_campos_adicionales/ajax_listado', [TipoTransaccionCampoAdicionalController::class, 'ajax_listado'])->name('tipos_transacciones_campos_adicionales.ajax_listado');
		Route::get('/tipos_transacciones_campos_adicionales/edit/{id}', [TipoTransaccionCampoAdicionalController::class, 'edit'])->name('tipos_transacciones_campos_adicionales.edit');
		Route::get('/tipos_transacciones_campos_adicionales/{id}', [TipoTransaccionCampoAdicionalController::class, 'index'])->name('tipos_transacciones_campos_adicionales');
#		Route::get('/tipos_transacciones_campos_adicionales', [TipoTransaccionCampoAdicionalController::class, 'index'])->name('tipos_transacciones_campos_adicionales');
		#Route::get('/show/{id}', [TipoTransaccionController::class, 'show'])->name('tipos_transacciones.campos_adicionales.show');
		Route::get('/tipos_transacciones_campos_adicionales/create', [TipoTransaccionCampoAdicionalController::class, 'create'])->name('tipos_transacciones_campos_adicionales.create');
		Route::post('/tipos_transacciones_campos_adicionales', [TipoTransaccionCampoAdicionalController::class, 'store'])->name('tipos_transacciones_campos_adicionales.store');
		Route::put('/tipos_transacciones_campos_adicionales/{id}', [TipoTransaccionCampoAdicionalController::class, 'update'])->name('tipos_transacciones_campos_adicionales.update');
		Route::delete('/tipos_transacciones_campos_adicionales/{id}', [TipoTransaccionCampoAdicionalController::class, 'destroy'])->name('tipos_transacciones_campos_adicionales.destroy');
		#Route::post('/tipos_transacciones_campos_adicionales/options', [TipoTransaccionCampoAdicionalController::class, 'options'])->name('tipos_transacciones_campos_adicionales.options');
		#Route::get('/tipos_transacciones_campos_adicionales/fields', [TipoTransaccionCampoAdicionalController::class, 'fields'])->name('tipos_transacciones_campos_adicionales.fields');
		Route::get('/tipos_transacciones_campos_adicionales/ajax_edit/{id}', [TipoTransaccionCampoAdicionalController::class, 'ajax_edit'])->name('tipos_transacciones_campos_adicionales.ajax_edit');		
		Route::post('/tipos_transacciones_campos_adicionales/ajax_delete/{id}', [TipoTransaccionCampoAdicionalController::class, 'ajax_delete'])->name('tipos_transacciones_campos_adicionales.ajax_delete');
		Route::post('/tipos_transacciones_campos_adicionales/ajax_guardar_columna/', [TipoTransaccionCampoAdicionalController::class, 'ajax_guardar_columna'])->name('tipos_transacciones_campos_adicionales.ajax_guardar_columna');
	}
);

Route::middleware('auth')->group(function () {
	Route::get('/funciones', [FuncionController::class, 'index'])->name('funciones.index');
#	Route::get('/show/{id}', [FuncionController::class, 'show'])->name('roles.show');
	#Route::get('/funciones/create', [FuncionController::class, 'create'])->name('funciones.create');
	Route::post('/funciones', [FuncionController::class, 'store'])->name('funciones.store');
#	Route::get('/funciones/{rol}/edit', [FuncionController::class, 'edit'])->name('funciones.edit');
	Route::put('/funciones/{id}', [FuncionController::class, 'update'])->name('funciones.update');
#	Route::delete('/funciones/{rol}', [FuncionController::class, 'destroy'])->name('funciones.destroy');
#	Route::post('/funciones/options', [FuncionController::class, 'options'])->name('funciones.options');
#	Route::get('/funciones/fields', [FuncionController::class, 'fields'])->name('funciones.fields');
	Route::get('/funciones/ajax_listado', [FuncionController::class, 'ajax_listado'])->name('funciones.ajax_listado');
	Route::get('/funciones/ajax_edit/{id}', [FuncionController::class, 'ajax_edit'])->name('funciones.ajax_edit');
	Route::post('/funciones/ajax_delete/{id}', [FuncionController::class, 'ajax_delete'])->name('funciones.ajax_delete');
});


require __DIR__ . '/auth.php';

