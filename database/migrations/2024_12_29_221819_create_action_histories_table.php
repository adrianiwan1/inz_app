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
        Schema::create('action_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('action_id')->constrained('actions')->onDelete('cascade'); // Powiązanie z akcją
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Powiązanie z użytkownikiem
            $table->timestamp('start_time')->nullable(); // Czas rozpoczęcia
            $table->timestamp('end_time')->nullable(); // Czas zakończenia
            $table->integer('elapsed_time')->default(0); // Czas trwania w sekundach
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('action_histories');
    }
};
