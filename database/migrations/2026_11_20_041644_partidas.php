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
        Schema::create('partidas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('jugador_1_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('jugador_2_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('turno_actual_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('ganador_id')->nullable()->constrained('users')->nullOnDelete();
            $table->enum('status', ['no_iniciado', 'en_juego', 'finalizado'])->default('no_iniciado');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partidas');
    }
};
