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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('property_id')->constrained('biens');
            $table->decimal('amount', 10, 2);
            $table->string('reference')->unique();
            $table->enum('status', ['pending', 'completed', 'failed', 'canceled'])->default('pending');
            $table->enum('payment_type', ['owner_info', 'reservation'])->default('owner_info');
            $table->text('metadata')->nullable();
            $table->timestamps();
        });

        // Ajouter une colonne payment_id à la table reservations
        Schema::table('reservations', function (Blueprint $table) {
            $table->foreignId('payment_id')->nullable()->constrained('payments');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('reservations', function (Blueprint $table) {
            $table->dropForeign(['payment_id']);
            $table->dropColumn('payment_id');
        });
        
        Schema::dropIfExists('payments');
    }
};

