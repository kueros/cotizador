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
        Schema::create('logs_administracion', function (Blueprint $table) {
            $table->id();
			$table->string('username')->index();
			$table->string('detalle', 255);
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }
/*
	1.	id: Identificador único del log.
	2.	username (string, 255): Nombre del usuario que realizó la acción.
	3.	detalle (string, 255): Descripción detallada del log, incluyendo más información si es necesario.
	4.	ip_address (string, 45): Dirección IP desde donde se realizó la acción.
	5.	user_agent (text): Información del agente de usuario para identificar el dispositivo o navegador utilizado.
	7.	created_at (timestamp): Fecha y hora en que se creó el log.
	8.	updated_at (timestamp): Fecha y hora en que se actualizó el log (opcional, generalmente manejado automáticamente por Laravel).

*/

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_administracion');
    }
};

