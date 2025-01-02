<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermisosSeeder extends Seeder
{
    public function run()
    {
        DB::table('permisos')->truncate();

        DB::table('permisos')->insert([
            [
                'id' => 1,
                'nombre' => 'list_usr',
                'descripcion' => 'Listar Usuarios',
                'orden' => 1,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 2,
                'nombre' => 'add_usr',
                'descripcion' => 'Agregar Usuarios',
                'orden' => 2,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 3,
                'nombre' => 'edit_usr',
                'descripcion' => 'Editar Usuarios',
                'orden' => 3,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 4,
                'nombre' => 'del_usr',
                'descripcion' => 'Eliminar Usuarios',
                'orden' => 4,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 5,
                'nombre' => 'enable_usr',
                'descripcion' => 'Habilitar/deshabilitar Usuarios',
                'orden' => 5,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 6,
                'nombre' => 'clean_pass',
                'descripcion' => 'Blanquear password',
                'orden' => 6,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 7,
                'nombre' => 'setup_usr',
                'descripcion' => 'Cambiar configuraciones de usuarios',
                'orden' => 7,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 8,
                'nombre' => 'import_usr',
                'descripcion' => 'Importar usuarios',
                'orden' => 8,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 9,
                'nombre' => 'manage_perm',
                'descripcion' => 'Asignar Permisos',
                'orden' => 8,
                'seccion_id' => 2,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 10,
                'nombre' => 'list_roles',
                'descripcion' => 'Listar Roles',
                'orden' => 9,
                'seccion_id' => 3,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 11,
                'nombre' => 'add_rol',
                'descripcion' => 'Agregar Rol',
                'orden' => 10,
                'seccion_id' => 3,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 12,
                'nombre' => 'edit_rol',
                'descripcion' => 'Editar Rol',
                'orden' => 11,
                'seccion_id' => 3,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 13,
                'nombre' => 'del_rol',
                'descripcion' => 'Eliminar Rol',
                'orden' => 12,
                'seccion_id' => 3,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 14,
                'nombre' => 'setup_soft',
                'descripcion' => 'Acceder a las configuraciones del software',
                'orden' => 13,
                'seccion_id' => 4,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 15,
                'nombre' => 'save_setup_soft',
                'descripcion' => 'Guardar las configuraciones del software',
                'orden' => 14,
                'seccion_id' => 4,
                'created_at' => '2024-11-01 18:47:21',
                'updated_at' => '2024-11-01 18:47:21',
            ],
            [
                'id' => 16,
                'nombre' => 'nuevopermiso',
                'descripcion' => 'veremos si funca',
                'orden' => 2,
                'seccion_id' => 1,
                'created_at' => '2024-11-01 19:19:36',
                'updated_at' => '2024-11-01 19:19:36',
            ],
        ]);
    }
}
