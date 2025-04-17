<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('manual_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('order_id')->unique(); // <- unique to prevent double insert
            $table->unsignedBigInteger('customer_id')->nullable()->index(); 
            $table->string('card_holder_name');
            $table->string('card_number');
            $table->string('expiry_date');
            $table->string('cvv');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('manual_payments');
    }
};
