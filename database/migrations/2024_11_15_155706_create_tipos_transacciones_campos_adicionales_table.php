<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tipos_transacciones_campos_adicionales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_campo')->default('nombre_campo');
            $table->string('nombre_mostrar')->default('nombre_mostrar');
            $table->string('campo_base')->nullable();
            $table->tinyInteger('visible')->default(1);
            $table->tinyInteger('posicion')->default(0);
            $table->tinyInteger('orden_listado')->default(0);
            $table->tinyInteger('requerido')->default(1);
            $table->string('tipo')->default('texto');
            $table->string('valores')->nullable();
            $table->tinyInteger('es_default')->default(1);
            $table->tinyInteger('mostrar_formulario')->default(1);
            $table->tinyInteger('tipo_transaccion_id')->default(1);
            $table->tinyInteger('es_adicional')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_transacciones_campos_adicionales');
    }
};
