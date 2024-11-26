<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use App\Models\Permiso;

class Funcion extends Model
{
	protected $table = 'funciones';

	protected $fillable = [
		'nombre',
		'formula',
	];

    protected $primaryKey = 'id';

}
