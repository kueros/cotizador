<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
	use HasFactory;

	protected $table = 'roles'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'nombre',
	];
}