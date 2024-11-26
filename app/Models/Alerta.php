<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alerta extends Model
{
    protected $table = 'alertas';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipos_alertas_id',
        'detalles_alertas_id',
    ];

    protected $primaryKey = 'id';
}
