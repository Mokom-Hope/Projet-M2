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
        Schema::create('security_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('event_type'); // login, transfer, failed_attempt, etc.
            $table->string('ip_address');
            $table->string('user_agent');
            $table->string('country')->nullable();
            $table->string('city')->nullable();
            $table->json('details')->nullable();
            $table->enum('risk_level', ['low', 'medium', 'high'])->default('low');
            $table->boolean('is_suspicious')->default(false);
            $table->timestamps();
            
            $table->index(['user_id', 'event_type']);
            $table->index(['ip_address', 'created_at']);
            $table->index(['is_suspicious', 'risk_level']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('security_logs');
    }
};
