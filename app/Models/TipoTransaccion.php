<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTransaccion extends Model
{
    protected $table = 'tipos_transacciones';

    protected $fillable = [
        'nombre',
    ];

    protected $primaryKey = 'tipos_transacciones_id';
}


