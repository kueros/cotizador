<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso_x_rol extends Model
{

	use HasFactory;

	protected $table = 'permisos_x_rol'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'rol_id',
		'permiso_id',
		'habilitado',
	];

}
