<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permiso extends Model
{
	use HasFactory;

	protected $table = 'permisos';

	protected $fillable = [
		'id',
		'nombre',
		'orden',
		'descripcion',
		'seccion_id',
	];

	public function roles()
	{
		return $this->belongsToMany(Rol::class, 'permisos_x_rol', 'permiso_id', 'rol_id')
			->withTimestamps()
			->withPivot('habilitado');
	}

	public function seccion()
	{
		return $this->belongsTo(Seccion::class);
	}
}
