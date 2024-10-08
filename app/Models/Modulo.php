<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Modulo extends Model
{
	use HasFactory;
	protected $table = 'modulos'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'nombre',
	];

	public function permisos()
	{
		return $this->hasMany(Permiso::class);
	}
}
