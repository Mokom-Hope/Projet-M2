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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('wallet_id')->constrained()->onDelete('cascade');
            $table->foreignId('transfer_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('type', ['credit', 'debit', 'fee', 'refund']);
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->string('description');
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('pending');
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_response')->nullable();
            $table->decimal('balance_after', 15, 2);
            $table->decimal('fees', 15, 2)->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->index(['user_id', 'type']);
            $table->index(['reference']);
            $table->index(['status', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
