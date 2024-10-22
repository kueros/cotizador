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
        Schema::create('permisos', function (Blueprint $table) {
            $table->integer('id')->primary();
            $table->string('nombre')->nullable();
            $table->string('descripcion')->nullable();
            $table->integer('orden')->nullable();  // Cambiado a integer
            $table->integer('seccion_id')->nullable();
            $table->timestamps();
        });

		// Verificar si el permiso "Permiso deAdministrador" ya existe, si no, crearlo
		DB::table('permisos')->insert([
			[
				'id' => 1,
				'nombre' => 'list_usr',
				'descripcion' => 'Listar Usuarios',
				'orden' => 1,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 2,
				'nombre' => 'add_usr',
				'descripcion' => 'Agregar Usuarios',
				'orden' => 2,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 3,
				'nombre' => 'edit_usr',
				'descripcion' => 'Editar Usuarios',
				'orden' => 3,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 4,
				'nombre' => 'del_usr',
				'descripcion' => 'Eliminar Usuarios',
				'orden' => 4,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 5,
				'nombre' => 'enable_usr',
				'descripcion' => 'Habilitar/deshabilitar Usuarios',
				'orden' => 5,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 6,
				'nombre' => 'clean_pass',
				'descripcion' => 'Blanquear password',
				'orden' => 6,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 7,
				'nombre' => 'setup_usr',
				'descripcion' => 'Cambiar configuraciones de usuarios',
				'orden' => 7,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 8,
				'nombre' => 'manage_perm',
				'descripcion' => 'Asignar Permisos',
				'orden' => 8,  // Proporciona un valor por defecto para orden
				'seccion_id' => 2,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 9,
				'nombre' => 'list_roles',
				'descripcion' => 'Listar Roles',
				'orden' => 9,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 10,
				'nombre' => 'add_rol',  // Condición para buscar el permiso
				'descripcion' => 'Agregar Rol',
				'orden' => 10,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 11,
				'nombre' => 'edit_rol',  // Condición para buscar el permiso
				'descripcion' => 'Editar Rol',
				'orden' => 11,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 12,
				'nombre' => 'del_rol',
				'descripcion' => 'Eliminar Rol',
				'orden' => 12,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 13,
				'nombre' => 'setup_soft',
				'descripcion' => 'Acceder a las configuraciones del software',
				'orden' => 13,  // Proporciona un valor por defecto para orden
				'seccion_id' => 4,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 14,
				'nombre' => 'save_setup_soft',
				'descripcion' => 'Guardar las configuraciones del software',
				'orden' => 14,  // Proporciona un valor por defecto para orden
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
