<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transaction', function (Blueprint $table) {
            $table->id();
            $table->decimal('montant', 10, 2);
            $table->foreignId('destinataire')->constrained('users')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('agent')->nullable()->constrained('users')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('exp')->constrained('users')->onDelete('no action')->onUpdate('no action');
            $table->foreignId('type_id')->constrained('type')->onDelete('no action')->onUpdate('no action');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transaction');
    }
};