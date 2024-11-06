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
        Schema::create('users_cadeaux', function (Blueprint $table) {
            $table->foreignId('users_id')->constrained('users')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('cadeau_id')->constrained('cadeaux')->onDelete('no action')->onUpdate('no action');
            $table->string('role', 50)->nullable();
            $table->primary(['users_id', 'cadeau_id']);
            $table->timestamps();
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users_cadeaux');
    }
};
