<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderShipped;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class OrderShipmentController extends Controller
{
	/**
	 * Enviar el pedido dado.
	 */
	public function store(Request $request): RedirectResponse
	{
		#$user = User::findOrFail($request->user_id);
		$user = User::all();
		// Enviar el pedido...

		Mail::to("omarliberatto1967@gmail.com")->send(new OrderShipped($user));

		return redirect('/users');
	}
}


/* 
use Illuminate\Support\Facades\Mail;

Mail::raw('Prueba de envío de correo desde Laravel', function ($message) {
    $message->to('destinatario@ejemplo.com')
            ->subject('Correo de Prueba');
});
*/