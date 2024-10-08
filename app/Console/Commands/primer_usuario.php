<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use App\Models\Rol;
use App\Models\Permiso;
use App\Models\Modulo;

class primer_usuario extends Command
{
	protected $signature = 'primer_usuario {username} {nombre} {apellido} {email} {password}';
	protected $description = 'Crea un nuevo usuario en la tabla yafo_plaft.users con datos validados';

	public function handle()
	{
		$data = [
			'username' => $this->argument('username'),
			'nombre' => $this->argument('nombre'),
			'apellido' => $this->argument('apellido'),
			'email' => $this->argument('email'),
			'password' => $this->argument('password'),
		];

		// Validar los datos
		$validator = Validator::make($data, [
			'username' => 'required|string|max:255',
			'nombre' => 'required|string|max:255',
			'apellido' => 'required|string|max:255',
			'email' => 'required|email|max:255',
			'password' => 'required|string|min:8',
		]);

		// Aplicar hash al password
		$data['password'] = Hash::make($data['password']);

		// Verificar si el rol "Administrador" ya existe, si no, crearlo
		$rol_administrador = Rol::firstOrCreate(
			['nombre' => 'Administrador'],  // Condición para buscar el rol
			[
				'guard_name' => 'web',  // Proporciona un valor por defecto para guard_name
				'created_at' => now(),
				'updated_at' => now()
			]
		);

		// Verificar si los módulos ya existen, si no, crearlos
		DB::table('modulos')->insert([
			[
				'nombre' => 'Usuarios',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Permisos',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Roles',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Configuraciones',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Logs de Accesos',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Logs de Administración',
				'created_at' => now(),
				'updated_at' => now()
			]
		]);

		// Verificar si el permiso "Permiso deAdministrador" ya existe, si no, crearlo
		DB::table('permisos')->insert([
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 1,  // Proporciona un valor por defecto para orden
				'modulo_id' => 1,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 2,  // Proporciona un valor por defecto para orden
				'modulo_id' => 2,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 3,  // Proporciona un valor por defecto para orden
				'modulo_id' => 3,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 4,  // Proporciona un valor por defecto para orden
				'modulo_id' => 4,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 5,  // Proporciona un valor por defecto para orden
				'modulo_id' => 5,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Administración',  // Condición para buscar el permiso
				'orden' => 6,  // Proporciona un valor por defecto para orden
				'modulo_id' => 6,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 7,  // Proporciona un valor por defecto para orden
				'modulo_id' => 1,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 8,  // Proporciona un valor por defecto para orden
				'modulo_id' => 2,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 9,  // Proporciona un valor por defecto para orden
				'modulo_id' => 3,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 10,  // Proporciona un valor por defecto para orden
				'modulo_id' => 4,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 11,  // Proporciona un valor por defecto para orden
				'modulo_id' => 5,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'nombre' => 'Listado',  // Condición para buscar el permiso
				'orden' => 12,  // Proporciona un valor por defecto para orden
				'modulo_id' => 6,  // Proporciona un valor por defecto para modulo_id
				'created_at' => now(),
				'updated_at' => now()
			]
		]);
		
		// Insertar el usuario en la tabla
		DB::table('yafo_plaft.users')->insert([
			'username' => $data['username'],
			'nombre' => $data['nombre'],
			'apellido' => $data['apellido'],
			'email' => $data['email'],
			'password' => $data['password'],
			'rol_id' => $rol_administrador->rol_id,  // Asignar el ID del rol "Administrador"
			'created_at' => now(),
			'updated_at' => now(),
		]);

		// crear el rol_x_usuario
		DB::table('yafo_plaft.roles_x_usuario')->insert([
			'user_id' => 1,
			'rol_id' => $rol_administrador->rol_id,  // Asignar el ID del rol "Administrador"
			'created_at' => now(),
			'updated_at' => now(),
		]);

		$this->info('Usuario creado exitosamente.');
		return 0; // Retornar éxito
	}
}
