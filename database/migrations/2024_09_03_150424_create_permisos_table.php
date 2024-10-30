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
				'id' => 101,
				'nombre' => 'list_usr',
				'descripcion' => 'Listar Usuarios',
				'orden' => 1,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 102,
				'nombre' => 'add_usr',
				'descripcion' => 'Agregar Usuarios',
				'orden' => 2,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 103,
				'nombre' => 'edit_usr',
				'descripcion' => 'Editar Usuarios',
				'orden' => 3,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 104,
				'nombre' => 'del_usr',
				'descripcion' => 'Eliminar Usuarios',
				'orden' => 4,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 105,
				'nombre' => 'enable_usr',
				'descripcion' => 'Habilitar/deshabilitar Usuarios',
				'orden' => 5,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 106,
				'nombre' => 'clean_pass',
				'descripcion' => 'Blanquear password',
				'orden' => 6,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 107,
				'nombre' => 'setup_usr',
				'descripcion' => 'Cambiar configuraciones de usuarios',
				'orden' => 7,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 108,
				'nombre' => 'import_usr',
				'descripcion' => 'Importar usuarios',
				'orden' => 8,
				'seccion_id' => 1,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 201,
				'nombre' => 'manage_perm',
				'descripcion' => 'Asignar Permisos',
				'orden' => 8,  // Proporciona un valor por defecto para orden
				'seccion_id' => 2,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 301,
				'nombre' => 'list_roles',
				'descripcion' => 'Listar Roles',
				'orden' => 9,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 302,
				'nombre' => 'add_rol',  // Condición para buscar el permiso
				'descripcion' => 'Agregar Rol',
				'orden' => 10,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 303,
				'nombre' => 'edit_rol',  // Condición para buscar el permiso
				'descripcion' => 'Editar Rol',
				'orden' => 11,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 304,
				'nombre' => 'del_rol',
				'descripcion' => 'Eliminar Rol',
				'orden' => 12,  // Proporciona un valor por defecto para orden
				'seccion_id' => 3,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 401,
				'nombre' => 'setup_soft',
				'descripcion' => 'Acceder a las configuraciones del software',
				'orden' => 13,  // Proporciona un valor por defecto para orden
				'seccion_id' => 4,  // Proporciona un valor por defecto para seccion_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 402,
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
