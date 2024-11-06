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
        Schema::create('scheduled_transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exp')->constrained('users');
            $table->foreignId('destinataire')->constrained('users');
            $table->decimal('montant', 10, 2);
            $table->enum('frequence', ['daily', 'weekly', 'monthly']);
            $table->date('date_debut');
            $table->date('date_fin')->nullable();
            $table->time('heure_execution');
            $table->boolean('is_active')->default(true);
            $table->dateTime('last_execution')->nullable();
            $table->dateTime('next_execution')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scheduled_transfers');
    }
};
