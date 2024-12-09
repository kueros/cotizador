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
            $table->string('descripcion');
            $table->string('monto');
            $table->string('usuario_id');
            $table->string('tipo_moneda_id');
            $table->string('tipo_transaccion_id');
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
            $table->dropColumn('tipo_transaccion_id');
        });
    }
};
