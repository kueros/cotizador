<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso;

class Rol extends Model
{

	protected $table = 'roles'; // Asegúrate de que el nombre de la tabla es correcto

	protected $fillable = [
		'nombre',
	];

    protected $primaryKey = 'rol_id'; // Cambia 'rol_id' por el nombre correcto de tu clave primaria

    public function permisos()
    {
        return $this->belongsToMany(Permiso::class, 'permisos_x_rol', 'rol_id', 'permiso_id')
            ->withTimestamps()
            ->withPivot('habilitado');
    }
    public function users()
    {
		return $this->belongsToMany(User::class, 'roles_x_usuario', 'rol_id', 'user_id');
	}
}
