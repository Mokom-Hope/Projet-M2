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
        Schema::create('transfers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('recipient_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('recipient_email')->nullable();
            $table->string('recipient_phone')->nullable();
            $table->decimal('amount', 15, 2);
            $table->string('currency', 3);
            $table->decimal('fees', 15, 2)->default(0);
            $table->decimal('total_amount', 15, 2);
            $table->string('security_question');
            $table->string('security_answer_hash');
            $table->enum('status', ['pending', 'payment_pending', 'sent', 'completed', 'failed', 'cancelled', 'expired'])->default('pending');            
            $table->string('transfer_code', 20)->unique();
            $table->foreignId('payment_method_id')->constrained()->onDelete('cascade');
            $table->foreignId('recipient_payment_method_id')->nullable()->constrained('payment_methods')->onDelete('set null');
            $table->timestamp('expires_at');
            $table->timestamp('claimed_at')->nullable();
            $table->integer('failed_attempts')->default(0);
            $table->integer('max_attempts')->default(3);
            $table->text('notes')->nullable();
            $table->decimal('exchange_rate', 10, 6)->nullable();
            $table->decimal('recipient_amount', 15, 2)->nullable();
            $table->string('recipient_currency', 3)->nullable();
            $table->string('gateway_reference')->nullable();
            $table->json('gateway_response')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index('transfer_code');
            $table->index(['status', 'expires_at']);
            $table->index(['recipient_email', 'status']);
            $table->index(['recipient_phone', 'status']);

            //ajout

            $table->string('payment_reference')->nullable();
            $table->string('payment_transaction_id')->nullable();
            $table->timestamp('payment_completed_at')->nullable();
            $table->text('failure_reason')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transfers');
    }
};
