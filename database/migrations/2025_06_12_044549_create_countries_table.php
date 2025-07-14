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
        Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code', 3)->unique();
            $table->string('currency', 3);
            $table->string('phone_prefix', 5);
            $table->boolean('is_active')->default(true);
            $table->json('supported_payment_methods');
            $table->decimal('min_transfer_amount', 15, 2)->default(200);
            $table->decimal('max_transfer_amount', 15, 2)->default(1000000);
            $table->timestamps();
            
            $table->index('code');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('countries');
    }
};
