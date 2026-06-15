<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('configuracion.users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('correo')->nullable();
            $table->string('password')->nullable();
            $table->string('cognito_sub')->nullable()->unique();
            $table->foreignId('role_user')->nullable()->constrained('configuracion.roles');
            $table->string('status', 10)->default('ACTIVO');
            $table->timestamps();
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('configuracion.users');
    }
};
