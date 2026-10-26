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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id()->identity(); // Esto crea un campo 'id' como clave primaria y autoincremental
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Relación con la tabla 'users'
            $table->string('title');
            $table->text('description');
            $table->enum('priority', ['baja', 'media', 'alta'])->default('media'); // Prioridad del ticket
            $table->enum('status', ['abierta', 'en_progreso', 'cerrada'])->default('abierta'); // Estado del ticket
            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
