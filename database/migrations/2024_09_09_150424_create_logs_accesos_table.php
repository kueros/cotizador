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
        Schema::create('logs_accesos', function (Blueprint $table) {
			$table->integer('id')->primary()->autoIncrement();
			$table->string('email')->index();
			$table->string('ip_address', 45)->nullable();
			$table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('logs_accesos');
    }
};
