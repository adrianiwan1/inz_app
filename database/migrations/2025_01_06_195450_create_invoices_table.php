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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Pracownik wystawiający fakturę
            $table->string('seller_name');
            $table->string('seller_address');
            $table->string('seller_nip');
            $table->string('buyer_name');
            $table->string('buyer_address');
            $table->string('buyer_nip');
            $table->string('service_name');
            $table->string('invoice_number');
            $table->decimal('net_value', 10, 2);
            $table->decimal('tax_rate', 5, 2); // w procentach
            $table->decimal('gross_value', 10, 2);
            $table->string('bank_account_number');
            $table->date('issue_date');
            $table->date('sale_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
