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
        Schema::create('users', function (Blueprint $table) {
            $table->id()->identity(); // Esto crea un campo 'id' como clave primaria y autoincremental
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('client'); // Agrega el campo 'role' con valor por defecto 'user'
            $table->rememberToken(); // esto agrega un campo para recordar la sesión del usuario
            $table->timestamps(); // esto agrega la fecha de creación y actualización automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
