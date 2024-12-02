<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlertaTipoTratamiento extends Model
{
    protected $table = 'alertas_tipos_tratamientos';

    protected $fillable = [
        'alertas_id',
        'nombre',
    ];
}
