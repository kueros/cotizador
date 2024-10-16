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
        Schema::create('permisos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nombre')->nullable();
            $table->integer('orden')->nullable();  // Cambiado a integer
            $table->integer('seccion_id')->nullable();
            $table->timestamps();
        });

		// Verificar si el permiso "Permiso deAdministrador" ya existe, si no, crearlo
		DB::table('permisos')->insert([
			[
				'id' => 1,
				'nombre' => 'Acceder al listado de usuarios',
				'orden' => 1,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 2,
				'nombre' => 'Agregar usuario',
				'orden' => 2,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 3,
				'nombre' => 'Editar usuario',
				'orden' => 3,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 4,
				'nombre' => 'Eliminar usuario',
				'orden' => 4,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 5,
				'nombre' => 'Administrar campos adicionales para tabla de usuarios',
				'orden' => 5,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 6,
				'nombre' => 'Habilitar / Deshabilitar usuario',
				'orden' => 6,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 7,
				'nombre' => 'Blanquear contraseña de usuario',
				'orden' => 7,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 8,
				'nombre' => 'Cambiar configuraciones de usuarios',
				'orden' => 8,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 9,
				'nombre' => 'Asignar Permisos',  // Condición para buscar el permiso
				'orden' => 9,  // Proporciona un valor por defecto para orden
				'seccion_id' => 2,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 10,
				'nombre' => 'Acceder al Listado de Roles',  // Condición para buscar el permiso
				'orden' => 10,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 11,
				'nombre' => 'Agregar Rol',  // Condición para buscar el permiso
				'orden' => 11,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 12,
				'nombre' => 'Editar Rol',  // Condición para buscar el permiso
				'orden' => 12,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 13,
				'nombre' => 'Eliminar Rol',  // Condición para buscar el permiso
				'orden' => 13,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 14,
				'nombre' => 'Acceder a las configuraciones del software',  // Condición para buscar el permiso
				'orden' => 14,  // Proporciona un valor por defecto para orden
				'seccion_id' => 4,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 15,
				'nombre' => 'Guardar las configuraciones del software',  // Condición para buscar el permiso
				'orden' => 15,  // Proporciona un valor por defecto para orden
				'seccion_id' => 4,  // Proporciona un valor por defecto para seccion_id
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
        Schema::dropIfExists('permisos');
    }
};
