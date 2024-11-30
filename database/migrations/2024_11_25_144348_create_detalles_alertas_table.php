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
        Schema::create('detalles_alertas', function (Blueprint $table) {
            $table->id();
            $table->string('alertas_id');
            $table->string('funciones_id');
            $table->string('fecha_desde');
            $table->string('fecha_hasta');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detalles_alertas');
    }
};
