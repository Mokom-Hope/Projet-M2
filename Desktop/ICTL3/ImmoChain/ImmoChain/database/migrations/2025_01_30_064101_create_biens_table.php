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
        Schema::create('biens', function (Blueprint $table) {
            $table->id();
            $table->string('titre');
            $table->text('description');
            $table->enum('type', ['Maison', 'Terrain', 'LocalCommercial', 'Studio', 'Chambre', 'Meublé', 'Hotel']);
            $table->string('adresse');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->float('prix');
            $table->float('superficie');
            $table->json('images');
            $table->string('video')->nullable();
            $table->dateTime('date_visite')->nullable();
            $table->enum('statut', ['Disponible', 'Réservé', 'Supprimé']);
            $table->string('transaction_type')->default('vente');
            $table->unsignedBigInteger('id_proprietaire');
            $table->foreign('id_proprietaire')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();

            // ajoute des colone pour integrer la blokchain
            $table->boolean('blockchain_registered')->default(false);
            $table->string('blockchain_tx')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biens');
    }
};
