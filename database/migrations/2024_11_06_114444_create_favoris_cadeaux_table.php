<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favoris', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('favori_id')->constrained('users')->onDelete('cascade');
            $table->string('alias')->nullable(); // Pour donner un surnom au favori
            $table->timestamps();
            
            // Empêcher les doublons
            $table->unique(['user_id', 'favori_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favoris');
    }
};