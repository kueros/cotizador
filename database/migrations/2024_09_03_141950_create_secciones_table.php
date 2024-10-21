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
        Schema::create('secciones', function (Blueprint $table) {
			$table->integer('seccion_id')->primary()->autoIncrement();
			$table->string('nombre')->nullable();
            $table->timestamps();
        }); 

		DB::table('secciones')->insert([
			[
				'nombre' => 'Gestión de Usuarios',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Asignación de Permisos por Rol',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Gestión de Roles',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Configuraciones',
				'created_at' => now(),
				'updated_at' => now()
			]
		]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('secciones');
    }
};
