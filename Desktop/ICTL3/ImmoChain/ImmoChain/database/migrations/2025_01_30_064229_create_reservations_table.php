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
        Schema::create('reservations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_bien');
            $table->unsignedBigInteger('id_client');
            $table->dateTime('date_reservation');
            $table->dateTime('date_visite');
            $table->text('message')->nullable();
            $table->enum('statut', ['pending', 'accepted', 'rejected', 'completed']);
            $table->float('montant_paye')->default(null)->change();
            $table->string('contact_proprietaire');
            $table->foreign('id_bien')->references('id')->on('biens')->onDelete('cascade');
            $table->foreign('id_client')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            //ajout des deux colones pour integrer la blockchain
            $table->boolean('blockchain_registered')->default(false);
            $table->string('blockchain_tx')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reservations');
    }
};
