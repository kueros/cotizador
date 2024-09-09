<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAdministracion extends Model
{
    use HasFactory;
	protected $table = 'logs_administracion'; // Asegúrate de que el nombre de la tabla es correcto
	protected $fillable = [
		'username',
		'detalle',
		'ip_address',
		'user_agent'
	];
}
