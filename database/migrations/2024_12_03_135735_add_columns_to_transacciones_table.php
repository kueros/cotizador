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
        Schema::table('transacciones', function (Blueprint $table) {
            $table->string('descripcion')->after('nombre');
            $table->string('monto')->after('nombre');
            $table->string('usuario_id')->after('nombre');
            $table->string('tipo_moneda_id')->after('nombre');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transacciones', function (Blueprint $table) {
            $table->dropColumn('descripcion');
            $table->dropColumn('monto');
            $table->dropColumn('usuario_id');
            $table->dropColumn('tipo_moneda_id');
        });
    }
};
