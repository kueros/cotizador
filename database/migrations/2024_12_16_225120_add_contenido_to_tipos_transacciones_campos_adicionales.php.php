<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('tipos_transacciones_campos_adicionales', function (Blueprint $table) {
            $table->string('contenido')->after('tipo_transaccion_id')->nullable(); // Agrega la columna 'contenido'
        });
    }
    
    public function down()
    {
        Schema::table('tipos_transacciones_campos_adicionales', function (Blueprint $table) {
            $table->dropColumn('contenido'); // Elimina la columna 'contenido'
        });
    }};
