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
        Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0);
            $table->string('currency', 3);
            $table->enum('status', ['active', 'suspended', 'frozen'])->default('active');
            $table->decimal('daily_limit', 15, 2)->default(1000000); // 1M FCFA
            $table->decimal('monthly_limit', 15, 2)->default(10000000); // 10M FCFA
            $table->decimal('daily_spent', 15, 2)->default(0);
            $table->decimal('monthly_spent', 15, 2)->default(0);
            $table->date('last_daily_reset')->nullable();
            $table->date('last_monthly_reset')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'currency']);
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wallets');
    }
};
