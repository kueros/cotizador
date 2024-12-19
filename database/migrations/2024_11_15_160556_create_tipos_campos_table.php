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
        Schema::create('tipos_campos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->default('nombre');
            $table->string('tipo')->default('string');
            $table->timestamps();
        });
        DB::table('tipos_campos')->insert([
			[
				'id' => 1,
				'nombre' => 'Texto',
				'tipo' => 'string',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 2,
				'nombre' => 'Número',
				'tipo' => 'integer',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 3,
				'nombre' => 'Fecha',
				'tipo' => 'dateTime',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 4,
				'nombre' => 'Selector',
				'tipo' => 'text',
				'created_at' => now(),
				'updated_at' => now()
			],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipos_campos');
    }
};
