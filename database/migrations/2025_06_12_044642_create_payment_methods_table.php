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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('type'); // mobile_money, bank_account, card
            $table->string('provider'); // orange_money, mtn, visa, etc.
            $table->string('account_number');
            $table->string('account_name');
            $table->string('country_code', 3);
            $table->json('metadata')->nullable(); // Infos supplémentaires
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_default')->default(false);
            $table->enum('status', ['active', 'inactive', 'blocked'])->default('active');
            $table->timestamps();
            
            $table->index(['user_id', 'is_default']);
            $table->index(['type', 'provider']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
