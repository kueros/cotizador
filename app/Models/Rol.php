<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso;

class Rol extends Model
{

	protected $table = 'roles';

	protected $fillable = [
		'nombre',
	];

    protected $primaryKey = 'rol_id';

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
