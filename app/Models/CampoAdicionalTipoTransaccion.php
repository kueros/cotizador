<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampoAdicionalTipoTransaccion extends Model
{
    protected $table = 'campos_adicionales_tipos_transacciones';

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

    protected $primaryKey = 'campos_adicionales_tipos_transacciones_id';
}
