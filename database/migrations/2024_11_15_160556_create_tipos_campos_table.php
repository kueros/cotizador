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
            $table->timestamps();
        });
        DB::table('tipos_campos')->insert([
			[
				'id' => 1,
				'nombre' => 'Texto',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 2,
				'nombre' => 'Número',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 3,
				'nombre' => 'Fecha',
				'created_at' => now(),
				'updated_at' => now()
			],
			[
				'id' => 4,
				'nombre' => 'Selector',
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
        Schema::dropIfExists('tipo_campos');
    }
};
