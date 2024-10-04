<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogAdministracion extends Model
{
    use HasFactory;
	protected $table = 'logs_administracion'; 
	protected $fillable = [
		'username',
		'detalle',
		'ip_address',
		'user_agent'
	];
}
