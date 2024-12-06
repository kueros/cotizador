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
use App\Http\Controllers\AlertaController;
use App\Http\Controllers\AlertaDetalleController;
use App\Http\Controllers\AlertaTipoController;
use App\Http\Controllers\AlertaTipoTratamientoController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use App\Models\Variable;


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
	$copa_background_home_custom = Variable::where('nombre', '=', 'copa_background_home_custom')->first();
	$background_home_custom_path = Variable::where('nombre', '=', 'background_home_custom_path')->first();
	if (!is_null($copa_background_home_custom)) {
		$copa_background_home_custom = $copa_background_home_custom->valor;
		$background_home_custom_path = is_null($background_home_custom_path) ? "" : $background_home_custom_path->valor;
	}
	return view('dashboard', compact('copa_background_home_custom', 'background_home_custom_path'));
})->middleware(['auth', 'verified'])->name('dashboard');



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
	Route::get('/profile', 										[ProfileController::class, 'edit'])->name('profile.edit');
	Route::patch('/profile', 									[ProfileController::class, 'update'])->name('profile.update');
	Route::delete('/profile', 									[ProfileController::class, 'destroy'])->name('profile.destroy');
});

//->middleware('custom.csrf')
Route::middleware('auth')->group(function () {
	Route::get('/users', 										[UserController::class, 'index'])->name('users.index'); #agregar estado y mensaje para mostrar modalcita con resultado de la acción realizada.
	Route::get('/show/{id}', 									[UserController::class, 'show'])->name('users.show');
	Route::get('/users/create', 								[UserController::class, 'create'])->name('users.create');
	Route::post('/users', 										[UserController::class, 'store'])->name('users.store');
	Route::get('/users/{user}/edit', 							[UserController::class, 'edit'])->name('users.edit');
	Route::put('/users/{user}', 								[UserController::class, 'usersUpdate'])->name('users.update');
	Route::delete('/users/{user}', 								[UserController::class, 'destroy'])->name('users.destroy');
	Route::post('/users/options', 								[UserController::class, 'options'])->name('users.options');
	Route::get('/users/guardar_opciones', 						[UserController::class, 'mostrar_opciones'])->name('users.mostrar_opciones');
	Route::post('/users/guardar_opciones', 		 				[UserController::class, 'guardar_opciones'])->name('users.guardar_opciones');
	Route::patch('/users/{id}/blanquear_password', 				[UserController::class, 'blanquear_password'])->name('users.blanquear_password');
	Route::patch('/users/{id}/deshabilitar', 					[UserController::class, 'deshabilitar_usuario'])->name('users.deshabilitar_usuario');
	Route::patch('/users/{id}/deshabilitar_usuario_temporal', 	[UserController::class, 'deshabilitar_usuario_temporal'])->name('users.deshabilitar_usuario_temporal');
	Route::get('/unlock-account/{userId}', 						[UserController::class, 'unlockAccount'])->name('account.unlock');
	Route::post('/users/ajax_delete/{id}', 						[UserController::class, 'ajax_delete'])->name('users.ajax_delete');
	Route::get('/users/ajax_edit/{id}', 						[UserController::class, 'ajax_edit'])->name('users.ajax_edit');
});

Route::middleware('auth')->group(function () {
	Route::get('/permisos', 									[PermisoController::class, 'index'])->name('permisos.index');
	Route::get('/permisosOrden', 								[PermisoController::class, 'permisosOrden'])->name('permisos.permisosOrden');
	Route::get('/show/{id}', 									[PermisoController::class, 'show'])->name('permisos.show');
	Route::get('/permisos/create', 								[PermisoController::class, 'create'])->name('permisos.create');
	Route::post('/permisos', 									[PermisoController::class, 'store'])->name('permisos.store');
	Route::get('/permisos/{permiso}/edit', 						[PermisoController::class, 'edit'])->name('permisos.edit');
	Route::patch('/permisos/{permiso}', 						[PermisoController::class, 'update'])->name('permisos.update');
	Route::delete('/permisos/{permiso}', 						[PermisoController::class, 'destroy'])->name('permisos.destroy');
	Route::post('/permisos/options', 							[PermisoController::class, 'options'])->name('permisos.options');
	Route::get('/permisos/fields', 								[PermisoController::class, 'fields'])->name('permisos.fields');
	Route::post('/permisos/update-order', 						[PermisoController::class, 'updateOrder'])->name('permisos.updateOrder');

	#Route::get('/permisos', [PermisoController::class, 'index'])->name('permisos.index');
	Route::post('/permisos/reordenar', 							[PermisoController::class, 'reordenar'])->name('permisos.reordenar');
});

Route::middleware('auth')->group(function () {
	Route::get('/permisos_x_rol', 								[Permiso_x_RolController::class, 'index'])->name('permisos_x_rol.index');
	Route::post('/permisos_x_rol/update', 						[Permiso_x_RolController::class, 'updatePermisos'])->name('permisos_x_rol.update');
});

Route::middleware('auth')->group(function () {
	Route::get('/roles', 										[RolController::class, 'index'])->name('roles.index');
	Route::get('/show/{id}', 									[RolController::class, 'show'])->name('roles.show');
	Route::get('/roles/create', 								[RolController::class, 'create'])->name('roles.create');
	Route::post('/roles', 										[RolController::class, 'store'])->name('roles.store');
	Route::get('/roles/{rol}/edit', 							[RolController::class, 'edit'])->name('roles.edit');
	Route::put('/roles/{rol}', 									[RolController::class, 'update'])->name('roles.update');
	Route::delete('/roles/{rol}', 								[RolController::class, 'destroy'])->name('roles.destroy');
	Route::post('/roles/options', 								[RolController::class, 'options'])->name('roles.options');
	Route::get('/roles/fields', 								[RolController::class, 'fields'])->name('roles.fields');
	Route::get('/roles/ajax_listado', 							[RolController::class, 'ajax_listado'])->name('roles.ajax_listado');
	Route::get('/roles/ajax_edit/{id}', 						[RolController::class, 'ajax_edit'])->name('roles.ajax_edit');
	Route::post('/roles/ajax_delete/{id}', 						[RolController::class, 'ajax_delete'])->name('roles.ajax_delete');
});


Route::middleware('auth')->group(function () {
	Route::get('/monitoreo', 									[MonitoreoController::class, 'index'])->name('monitoreo.index');
	Route::get('/monitoreo/log_accesos', 						[MonitoreoController::class, 'log_accesos'])->name('monitoreo.log_accesos');
	Route::get('/monitoreo/log_administracion', 				[MonitoreoController::class, 'log_administracion'])->name('monitoreo.log_administracion');
	Route::get('/monitoreo/log_notificaciones', 				[MonitoreoController::class, 'log_notificaciones'])->name('monitoreo.log_notificaciones');
	Route::get('/monitoreo/log_emails', 						[MonitoreoController::class, 'log_emails'])->name('monitoreo.log_emails');
	Route::get('/monitoreo/ajax_log_acceso', 					[MonitoreoController::class, 'ajax_log_acceso'])->name('monitoreo.ajax_log_acceso');
	Route::get('/monitoreo/ajax_log_administracion', 			[MonitoreoController::class, 'ajax_log_administracion'])->name('monitoreo.ajax_log_administracion');
	Route::get('/monitoreo/ajax_log_emails', 					[MonitoreoController::class, 'ajax_log_emails'])->name('monitoreo.ajax_log_emails');
});

Route::middleware('auth')->group(
	function () {
		Route::post('/configuracion/remitente', 				[ConfiguracionController::class, 'guardar_remitente'])->name('configuracion.guardar_remitente');
		Route::get('/configuracion/mail', 						[ConfiguracionController::class, 'enviar_mail'])->name('configuracion.enviar_mail');
		Route::get('/configuracion', 							[ConfiguracionController::class, 'index'])->name('configuracion.index');
		Route::post('/configuracion/guardar_estado', 			[ConfiguracionController::class, 'guardar_estado'])->name('configuracion.guardar_estado');
		Route::post('/configuracion/add_parametro_email', 		[ConfiguracionController::class, 'add_parametro_email'])->name('configuracion.add_parametro_email');
		Route::get('/configuracion/variables', 					[ConfiguracionController::class, 'variables'])->name('configuracion.variables');
		Route::post('/configuracion/variables', 				[ConfiguracionController::class, 'store'])->name('configuracion.variables.store');
		Route::get('/configuracion/variables/create', 			[ConfiguracionController::class, 'create'])->name('configuracion.variables.create');
		Route::get('/configuracion/variables/{variable}/edit', 	[ConfiguracionController::class, 'edit'])->name('configuracion.variables.edit');
		Route::patch('/configuracion/variables/{variable}', 	[ConfiguracionController::class, 'update'])->name('configuracion.variables.update');
		Route::delete('/configuracion/variables/{variable}', 	[ConfiguracionController::class, 'destroy'])->name('configuracion.variables.destroy');
		Route::post('/configuracion/ajax_delete_parametro_email', [ConfiguracionController::class, 'ajax_delete_parametro_email'])->name('configuracion.ajax_delete_parametro_email');
		Route::post('/guardar_imagen_home', 					[ConfiguracionController::class, 'guardarImagenHome'])->name('configuracion.guardar_imagen_home');
		Route::post('/guardar_imagen_login', 					[ConfiguracionController::class, 'guardarImagenLogin'])->name('configuracion.guardar_imagen_login');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/send/mail', 								[OrderShipmentController::class, 'store'])->name('enviarmail');
		Route::get('/obtenerusername', 							[MyController::class, 'get_username'])->name('obtenerusername1');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::put('/ttUpdate/{id}', 							[TipoTransaccionController::class, 'ttUpdate'])->name('ttUpdate');
		Route::get('/tipos_transacciones/ajax_listado', 		[TipoTransaccionController::class, 'ajax_listado'])->name('tipos_transacciones.ajax_listado');
		Route::get('/tipos_transacciones', 						[TipoTransaccionController::class, 'index'])->name('tipos_transacciones.index');
		Route::get('/tipos_transacciones/create', 				[TipoTransaccionController::class, 'create'])->name('tipos_transacciones.create');
		Route::post('/tipos_transacciones', 					[TipoTransaccionController::class, 'store'])->name('tipos_transacciones.store');
		Route::get('/tipos_transacciones/{id}/edit',			[TipoTransaccionController::class, 'edit'])->name('tipos_transacciones.edit');
		Route::delete('/tipos_transacciones/{id}', 				[TipoTransaccionController::class, 'destroy'])->name('tipos_transacciones.destroy');
		Route::post('/tipos_transacciones/options', 			[TipoTransaccionController::class, 'options'])->name('tipos_transacciones.options');
		Route::get('/tipos_transacciones/fields', 				[TipoTransaccionController::class, 'fields'])->name('tipos_transacciones.fields');
		Route::get('/tipos_transacciones/ajax_edit/{id}', 		[TipoTransaccionController::class, 'ajax_edit'])->name('tipos_transacciones.ajax_edit');
		Route::post('/tipos_transacciones/ajax_delete/{id}', 	[TipoTransaccionController::class, 'ajax_delete'])->name('tipos_transacciones.ajax_delete');
	}
);
Route::middleware('auth')->group(
	function () {
		Route::get('/tipos_transacciones_campos_adicionales/ajax_listado', 			[TipoTransaccionCampoAdicionalController::class, 'ajax_listado'])->name('tipos_transacciones_campos_adicionales.ajax_listado');
		Route::get('/tipos_transacciones_campos_adicionales/edit/{id}', 			[TipoTransaccionCampoAdicionalController::class, 'edit'])->name('tipos_transacciones_campos_adicionales.edit');
		Route::get('/tipos_transacciones_campos_adicionales/{id}', 					[TipoTransaccionCampoAdicionalController::class, 'index'])->name('tipos_transacciones_campos_adicionales');
		Route::get('/tipos_transacciones_campos_adicionales/create', 				[TipoTransaccionCampoAdicionalController::class, 'create'])->name('tipos_transacciones_campos_adicionales.create');
		Route::post('/tipos_transacciones_campos_adicionales', 						[TipoTransaccionCampoAdicionalController::class, 'store'])->name('tipos_transacciones_campos_adicionales.store');
		Route::put('/tipos_transacciones_campos_adicionales/{id}', 					[TipoTransaccionCampoAdicionalController::class, 'update'])->name('tipos_transacciones_campos_adicionales.update');
		Route::delete('/tipos_transacciones_campos_adicionales/{id}', 				[TipoTransaccionCampoAdicionalController::class, 'destroy'])->name('tipos_transacciones_campos_adicionales.destroy');
		Route::get('/tipos_transacciones_campos_adicionales/ajax_edit/{id}', 		[TipoTransaccionCampoAdicionalController::class, 'ajax_edit'])->name('tipos_transacciones_campos_adicionales.ajax_edit');
		Route::post('/tipos_transacciones_campos_adicionales/ajax_delete/{id}', 	[TipoTransaccionCampoAdicionalController::class, 'ajax_delete'])->name('tipos_transacciones_campos_adicionales.ajax_delete');
		Route::post('/tipos_transacciones_campos_adicionales/ajax_store/', [TipoTransaccionCampoAdicionalController::class, 'ajax_store'])->name('tipos_transacciones_campos_adicionales.ajax_store');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/funciones', 								[FuncionController::class, 'index'])->name('funciones.index');
		Route::post('/funciones/ajax_store', 					[FuncionController::class, 'ajax_store'])->name('funciones.ajax_store');
		Route::put('/funcionesUpdate/{id}', 					[FuncionController::class, 'funcionesUpdate'])->name('funcionesUpdate');
		Route::get('/funciones/ajax_listado', 					[FuncionController::class, 'ajax_listado'])->name('funciones.ajax_listado');
		Route::get('/funciones/ajax_edit/{id}', 				[FuncionController::class, 'ajax_edit'])->name('funciones.ajax_edit');
		Route::post('/funciones/ajax_delete/{id}', 				[FuncionController::class, 'ajax_delete'])->name('funciones.ajax_delete');
		Route::get('/funciones/tipos_transacciones', 			[FuncionController::class, 'obtenerTiposTransacciones']);
		Route::post('/funciones/campos-transacciones', 			[FuncionController::class, 'obtenerCamposTransacciones']);
		Route::post('/funciones/contar-transacciones', 			[FuncionController::class, 'contarTransacciones']);
		Route::post('/funciones/acumular-transacciones', 		[FuncionController::class, 'acumularTransacciones']);
		Route::get('/funciones/listado', 						[FuncionController::class, 'listado']);
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/alertasIndex', 							[AlertaController::class, 'alertasIndex'])->name('alertasIndex');
		Route::put('/alertasUpdate/{id}', 						[AlertaController::class, 'alertasUpdate'])->name('alertasUpdate');
		Route::post('/alertas/ajax_store/', 					[AlertaController::class, 'ajax_store'])->name('alertas.ajax_store');
		Route::get('/alertas/ajax_listado', 					[AlertaController::class, 'ajax_listado'])->name('alertas.ajax_listado');
		Route::get('/alertas/ajax_edit/{id}', 					[AlertaController::class, 'ajax_edit'])->name('alertas.ajax_edit');
		Route::post('/alertas/ajax_delete/{id}', 				[AlertaController::class, 'ajax_delete'])->name('alertas.ajax_delete');
		Route::post('/alertas/store', 							[AlertaController::class, 'store'])->name('alertas.store');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::put('/alertas_detalles/{id}', 					[AlertaDetalleController::class, 'update'])->name('alertas_detalles.update');
		Route::get('/alertas_detalles/ajax_listado', 			[AlertaDetalleController::class, 'ajax_listado'])->name('alertas_detalles.ajax_listado');
		Route::get('/alertas_detalles/{id}', 					[AlertaDetalleController::class, 'index'])->name('alertas_detalles');
		Route::get('/alertas_detalles/ajax_edit/{id}', 			[AlertaDetalleController::class, 'ajax_edit'])->name('alertas_detalles.ajax_edit');
		Route::post('/alertas_detalles/ajax_delete/{id}', 		[AlertaDetalleController::class, 'ajax_delete'])->name('alertas_detalles.ajax_delete');
		Route::post('/alertas_detalles', 						[AlertaDetalleController::class, 'store'])->name('alertas_detalles.store');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/alertas_tipos/ajax_listado', 				[AlertaTipoController::class, 'ajax_listado'])->name('alertas_tipos.ajax_listado');
		Route::get('/alertas_tipos', 							[AlertaTipoController::class, 'index'])->name('alertas_tipos');
		Route::post('/alertas_tipos/ajax_store/', 				[AlertaTipoController::class, 'ajax_store'])->name('alertas_tipos.ajax_store');
		Route::get('/alertas_tipos/ajax_edit/{id}', 			[AlertaTipoController::class, 'ajax_edit'])->name('alertas_tipos.ajax_edit');
		Route::post('/alertas_tipos/ajax_delete/{id}', 			[AlertaTipoController::class, 'ajax_delete'])->name('alertas_tipos.ajax_delete');
		Route::post('/alertas_tipos', 							[AlertaTipoController::class, 'store'])->name('alertas_tipos.store');
		Route::put('/alertas_tipos/{id}',						[AlertaTipoController::class, 'update'])->name('alertas_tipos.update');
	}
);

Route::middleware('auth')->group(
	function () {
		Route::get('/alertas_tipos_tratamientos/ajax_listado', 				[AlertaTipoTratamientoController::class, 'ajax_listado'])->name('alertas_tipos_tratamientos.ajax_listado');
		Route::get('/alertas_tipos_tratamientos', 							[AlertaTipoTratamientoController::class, 'index'])->name('alertas_tipos_tratamientos');
		Route::post('/alertas_tipos_tratamientos/ajax_store/', 				[AlertaTipoTratamientoController::class, 'ajax_store'])->name('alertas_tipos_tratamientos.ajax_store');
		Route::get('/alertas_tipos_tratamientos/ajax_edit/{id}', 			[AlertaTipoTratamientoController::class, 'ajax_edit'])->name('alertas_tipos_tratamientos.ajax_edit');
		Route::post('/alertas_tipos_tratamientos/ajax_delete/{id}', 		[AlertaTipoTratamientoController::class, 'ajax_delete'])->name('alertas_tipos_tratamientos.ajax_delete');
		Route::post('/alertas_tipos_tratamientos', 							[AlertaTipoTratamientoController::class, 'store'])->name('alertas_tipos_tratamientos.store');
		Route::put('/alertas_tipos_tratamientos/{id}',						[AlertaTipoTratamientoController::class, 'update'])->name('alertas_tipos_tratamientos.update');
	}
);


require __DIR__ . '/auth.php';
