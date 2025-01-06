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
        Schema::table('users', function (Blueprint $table) {
            $table->enum('employment_type', ['umowa_o_prace', 'b2b', 'umowa_zlecenie'])->nullable()->after('email');
            $table->integer('hourly_rate')->nullable()->after('employment_type'); // Stawka w groszach
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('employment_type');
            $table->dropColumn('hourly_rate');
        });
    }
};
