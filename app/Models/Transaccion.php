<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = 'transacciones';
    protected $primaryKey = 'id';
    protected $fillable = [
        'nombre',
        'tipo_trasaccion_id',
    ];
}
