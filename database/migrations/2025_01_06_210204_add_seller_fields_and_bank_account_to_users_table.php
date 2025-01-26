<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('seller_address')->nullable()->after('email'); // Adres sprzedawcy
            $table->string('seller_nip')->nullable()->after('seller_address'); // NIP sprzedawcy
            $table->string('bank_account_number')->nullable()->after('seller_nip'); // Numer rachunku
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['seller_address', 'seller_nip', 'bank_account_number']);
        });
    }
};
