<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAcceso extends Model
{
    use HasFactory;
	protected $table = 'logs_accesos'; // Asegúrate de que el nombre de la tabla es correcto
	protected $fillable = [
		'email',
		'username',
		'ip_address',
		'user_agent'
	];
}
