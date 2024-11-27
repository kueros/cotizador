<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaDetalle extends Model
{
    protected $table = 'detalles_alertas';

    protected $fillable = [
        'alertas_id',
        'funciones_id',
        'fecha_desde',
        'fecha_hasta',
    ];

    protected $primaryKey = 'id';
}
