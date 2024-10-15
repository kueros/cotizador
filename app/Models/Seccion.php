<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Seccion extends Model
{
	use HasFactory;
	protected $table = 'seccion'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'nombre',
	];

	public function permisos()
	{
		return $this->hasMany(Permiso::class);
	}
}
