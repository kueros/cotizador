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
		Schema::create('permisos_x_rol', function (Blueprint $table) {
			$table->integer('permisos_x_rol_id')->primary()->autoIncrement();
			$table->integer('rol_id')->nullable();
			$table->integer('permiso_id')->nullable();
			$table->tinyInteger('habilitado')->default(1);
			$table->timestamps();
		});

		$rol = DB::table('roles')->first();
		$rol_id = $rol->rol_id;
		$permisos = DB::table('permisos')->get('id');
		foreach ($permisos as $permiso) {
			DB::table('permisos_x_rol')->insert([
				[
					'rol_id' => $rol_id,
					'permiso_id' => $permiso->id,
					'habilitado' => 1,
					'created_at' => now(),
					'updated_at' => now()
				]
			]);
		}
	}

	/**
	 * Reverse the migrations.
	 */
	public function down(): void
	{
		Schema::dropIfExists('permisos_x_rol');
	}
};
