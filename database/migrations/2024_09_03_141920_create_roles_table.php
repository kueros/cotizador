<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
			$table->integer('id')->primary()->autoIncrement();
			$table->string('nombre')->nullable();
			$table->string('guard_name')->nullable();
            $table->timestamps();
        });
        // Insertar un registro de rol "Administrador" al ejecutar la migración
        DB::table('roles')->insert([
            'id' => 1,
            'nombre' => 'Administrador',
            'created_at' => now(),
            'updated_at' => now()
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
