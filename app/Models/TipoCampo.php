<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoCampo extends Model
{
    protected $table = 'tipos_campos';

    protected $fillable = [
        'nombre',
    ];

    protected $primaryKey = 'tipos_campos_id';
}

