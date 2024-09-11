<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notificacion extends Model
{
    use HasFactory;

	protected $table = 'notificaciones';

	protected $fillable = [
		'user_id',
		'mensaje',
		'estado',
		'user_emisor_id',
		'asunto'
	];
}