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
		Schema::create('variables', function (Blueprint $table) {
			$table->integer('id')->primary()->autoIncrement();
			$table->string('nombre');
			$table->string('nombre_menu')->nullable();
			$table->string('valor')->default('[]');
			$table->timestamps();
		});

		// Insertar registros de variables por default al ejecutar la migración
		DB::table('variables')->insert([
			[
				'id' => 3,
				'nombre' => 'fecha_minima_edicion_eventos',
				'nombre_menu' => '',
				'valor' => '2019-01-15',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 7,
				'nombre' => 'version',
				'nombre_menu' => '',
				'valor' => '3.7.3',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 8,
				'nombre' => 'fecha_ultima_actualizacion',
				'nombre_menu' => '',
				'valor' => '2024-08-30',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 9,
				'nombre' => 'reset_password_30_dias',
				'nombre_menu' => '',
				'valor' => 0,
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 10,
				'nombre' => 'session_time',
				'nombre_menu' => '',
				'valor' => '900',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 11,
				'nombre' => 'notificaciones_locales',
				'nombre_menu' => 'Utilizar notificacines locales',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 12,
				'nombre' => 'notificaciones_email',
				'nombre_menu' => 'Utilizar notificaciones por email',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 13,
				'nombre' => '_notificaciones_email_aleph',
				'nombre_menu' => 'Utilizar servicio de envío de email de Aleph Manager',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 18,
				'nombre' => 'integracion_azure',
				'nombre_menu' => '',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 19,
				'nombre' => 'integracion_gmail',
				'nombre_menu' => '',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 20,
				'nombre' => 'opav_habilitar_modo_debug',
				'nombre_menu' => 'Habilitar modo debug',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 73,
				'nombre' => 'copa_background_home_custom',
				'nombre_menu' => 'Utilizar imagen home default',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 74,
				'nombre' => 'background_home_custom_path',
				'nombre_menu' => '',
				'valor' => 'slide0028_image054.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 75,
				'nombre' => 'copa_background_login_custom',
				'nombre_menu' => 'Utilizar imagen login default',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 76,
				'nombre' => 'background_login_custom_path',
				'nombre_menu' => '',
				'valor' => 'login-background.jpg',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 97,
				'nombre' => 'opav_habilitar_ia_ciberseguridad',
				'nombre_menu' => 'Habilitar IA en módulo de ciberseguridad',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 98,
				'nombre' => 'opav_open_ai_api_key',
				'nombre_menu' => 'OpenAI API Token',
				'valor' => '0',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 126,
				'nombre' => 'copa_aleph_estilo_logotipo_default',
				'nombre_menu' => 'Utilizar logotipo de Aleph Manager default',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 127,
				'nombre' => 'copa_aleph_estilo_color_barra_menu',
				'nombre_menu' => 'Utilizar color de barra del menú default',
				'valor' => '1',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 130,
				'nombre' => 'aleph_estilo_color_titulos_menu',
				'nombre_menu' => '',
				'valor' => '#FFFFFF',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 131,
				'nombre' => 'aleph_estilo_color_mouseover_menu',
				'nombre_menu' => '',
				'valor' => '#F5F5F5',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 145,
				'nombre' => 'fecha_version',
				'nombre_menu' => '',
				'valor' => '2024-08-30',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 158,
				'nombre' => '_notificaciones_email_from',
				'nombre_menu' => '',
				'valor' => 'omarliberatto@yafoconsultora.com',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 159,
				'nombre' => '_notificaciones_email_from_name',
				'nombre_menu' => '',
				'valor' => 'omar',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 160,
				'nombre' => '_notificaciones_email_config',
				'nombre_menu' => '',
				'valor' => '[]',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 161,
				'nombre' => 'configurar_claves',
				'nombre_menu' => '',
				'valor' => 0,
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
		Schema::dropIfExists('variables');
	}
};
