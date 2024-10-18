<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;



Route::middleware('guest')->group(function () {
	Route::get('register', 							[RegisteredUserController::class, 'create'])->name('register');
	Route::post('register', 						[RegisteredUserController::class, 'store']);
	Route::get('login', 							[AuthenticatedSessionController::class, 'create'])->name('login');
	Route::post('login', 							[AuthenticatedSessionController::class, 'store']);
	Route::get('forgot-password', 					[PasswordResetLinkController::class, 'create'])->name('password.request');
	Route::post('forgot-password', 					[PasswordResetLinkController::class, 'store'])->name('password.email');
	Route::post('update-password', 					[PasswordController::class, 'update'])->name('password.update');
	Route::post('reset-password', 					[NewPasswordController::class, 'store'])->name('password.store');
	#Route::get('reset-password/{token}', 			[NewPasswordController::class, 'create'])->name('password.reset');
	Route::get('password_change', 					[NewPasswordController::class, 'showResetForm'])->name('password.change');
	
	#Route::get('/users/{id}/password', [UserController::class, 'showPasswordForm'])->name('users.showPasswordForm');
	#	Route::patch('/users/{id}/password', [UserController::class, 'updatePassword'])->name('password.update');
	/* 	Route::get('/change-password/{userId}', [UserController::class, 'showPasswordForm'])->name('users.showPasswordForm');
		Route::post('/change-password/{userId}', [UserController::class, 'changePassword'])->name('password.update');
	
		Route::get('/usuarios/cambiar_password', 		[UserController::class, 'showResetForm'])->name('password.change');
		Route::post('/usuarios/cambiar_password', 		[UserController::class, 'resetPassword'])->name('password.reset');
	*/

});
####	
Route::get('reset_pass/{token}/{email}', 			[NewPasswordController::class, 'reset_pass'])->name('reset_pass_form');
Route::post('password_reset', 						[NewPasswordController::class, 'password_reset'])->name('resetear_password');	

###
Route::get('create_pass/{token}/{email}', 			[NewPasswordController::class, 'create_pass'])->name('create_pass_form');
Route::post('/password_create', 					[NewPasswordController::class, 'store'])->name('crear_password');
###




Route::middleware('auth')->group(function () {
	Route::get('verify-email', 						EmailVerificationPromptController::class)->name('verification.notice');
	Route::get('verify-email/{id}/{hash}', 			VerifyEmailController::class)
													->middleware(['signed', 'throttle:6,1'])
													->name('verification.verify');
	Route::post('email/verification-notification', 	[EmailVerificationNotificationController::class, 'store'])
													->middleware('throttle:6,1')
													->name('verification.send');
	Route::get('confirm-password', 					[ConfirmablePasswordController::class, 'show'])->name('password.confirm');
	Route::post('confirm-password', 				[ConfirmablePasswordController::class, 'store']);
	#Route::put('password', 							[PasswordController::class, 'update'])->name('password.update');
	Route::post('logout', 							[AuthenticatedSessionController::class, 'destroy'])->name('logout');
});
