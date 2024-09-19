<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RolController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MonitoreoController;
use App\Http\Controllers\ConfiguracionController;
use App\Http\Controllers\SendMailController;
use App\Http\Controllers\OrderShipmentController;
use App\Http\Controllers\MyController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;


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


Route::middleware('auth')->group(function () {
	Route::get('/configuracion', [ConfiguracionController::class, 'index'])->name('configuracion.index');

	Route::get('/configuracion/variables', [ConfiguracionController::class, 'variables'])->name('configuracion.variables');

	Route::get('/configuracion/variables/create', [ConfiguracionController::class, 'create'])->name('configuracion.variables.create');
	Route::post('/configuracion/variables', [ConfiguracionController::class, 'store'])->name('configuracion.variables.store');
	Route::get('/configuracion/variables/{variable}/edit', [ConfiguracionController::class, 'edit'])->name('configuracion.variables.edit');
	Route::patch('/configuracion/variables/{variable}', [ConfiguracionController::class, 'update'])->name('configuracion.variables.update');
	Route::delete('/configuracion/variables/{variable}', [ConfiguracionController::class, 'destroy'])->name('configuracion.variables.destroy');
	Route::get('/monitoreo/log_administracion', [MonitoreoController::class, 'log_administracion'])->name('monitoreo.log_administracion');
	Route::get('/monitoreo/log_notificaciones', [MonitoreoController::class, 'log_notificaciones'])->name('monitoreo.log_notificaciones');
	Route::get('/monitoreo/log_emails', [MonitoreoController::class, 'log_emails'])->name('monitoreo.log_emails');
});

Route::post('/configuracion', [ConfiguracionController::class, 'guardar_estado'])->name('configuracion.guardar_estado');
Route::post('/configuracion/remitente', [ConfiguracionController::class, 'guardar_remitente_email'])->name('configuracion.guardar_remitente_email');

Route::get('send/mail', [OrderShipmentController::class, 'store'])->name('enviarmail');
Route::get('/obtenerusername', [MyController::class, 'get_username'])->name('obtenerusername1');





Route::middleware('guest')->group(function () {
	Route::get('register', [RegisteredUserController::class, 'create'])
		->name('register');

	Route::post('register', [RegisteredUserController::class, 'store']);

	Route::get('login', [AuthenticatedSessionController::class, 'create'])
		->name('login');

	Route::post('login', [AuthenticatedSessionController::class, 'store']);

	Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
		->name('password.request');

	Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
		->name('password.email');

	Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
		->name('password.reset');

	Route::post('reset-password', [NewPasswordController::class, 'store'])
		->name('password.store');
});

Route::middleware('auth')->group(function () {
	Route::get('verify-email', EmailVerificationPromptController::class)
		->name('verification.notice');

	Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
		->middleware(['signed', 'throttle:6,1'])
		->name('verification.verify');

	Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
		->middleware('throttle:6,1')
		->name('verification.send');

	Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
		->name('password.confirm');

	Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

	Route::put('password', [PasswordController::class, 'update'])->name('password.update');

	Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
		->name('logout');
});
