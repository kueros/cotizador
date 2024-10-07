<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{

	protected $table = 'roles'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'nombre',
	];
    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'permisos_x_rol', 'rol_id', 'permiso_id')
            ->withTimestamps()
            ->withPivot('habilitado');
    }

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

}