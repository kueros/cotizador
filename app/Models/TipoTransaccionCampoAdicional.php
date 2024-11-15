<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TipoTransaccionCampoAdicional extends Model
{
    protected $table = 'tipos_transacciones_campos_adicionales';

    protected $fillable = [
        'nombre_campo',
        'nombre_mostrar',
        'campo_base',
        'visible',
        'orden_abm',
        'orden_listado',
        'requerido',
        'tipo',
        'valor_default',
        'es_default',
        'mostrar_formulario',
        'requerido',
        'tipo_transaccion_id',
        'es_adicional',

    ];

    protected $primaryKey = 'TipoTransaccionCampoAdicionalController_id';
}
