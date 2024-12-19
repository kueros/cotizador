<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $guarded = []; // Permitir todos los campos para asignación masiva
    protected $table = 'transacciones';
    protected $primaryKey = 'id';
}