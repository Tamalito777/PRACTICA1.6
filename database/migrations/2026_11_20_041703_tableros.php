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
        Schema::create('tableros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partida_id')->unique()->constrained('partidas')->cascadeOnDelete();
            $table->enum('casilla_1', ['X', 'O'])->nullable();
            $table->enum('casilla_2', ['X', 'O'])->nullable();
            $table->enum('casilla_3', ['X', 'O'])->nullable();
            $table->enum('casilla_4', ['X', 'O'])->nullable();
            $table->enum('casilla_5', ['X', 'O'])->nullable();
            $table->enum('casilla_6', ['X', 'O'])->nullable();
            $table->enum('casilla_7', ['X', 'O'])->nullable();
            $table->enum('casilla_8', ['X', 'O'])->nullable();
            $table->enum('casilla_9', ['X', 'O'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tableros');
    }
};
