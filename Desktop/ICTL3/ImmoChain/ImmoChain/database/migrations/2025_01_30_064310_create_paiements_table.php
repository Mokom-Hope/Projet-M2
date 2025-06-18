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
        Schema::create('paiements', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_reservation');
            $table->float('montant');
            $table->enum('methode_paiement', ['CarteBancaire', 'MobileMoney']);
            $table->dateTime('date_paiement');
            $table->enum('statut_paiement', ['Réussi', 'Échoué', 'EnAttente']);
            $table->foreign('id_reservation')->references('id')->on('reservations')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('paiements');
    }
};
