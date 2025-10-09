<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('mode_transit');
            $table->string('pays_expedition');
            $table->string('agence_expedition');
            $table->string('agence_destination');
            $table->string('nom_expediteur');
            $table->string('prenom_expediteur');
            $table->string('email_expediteur')->nullable();
            $table->string('tel_expediteur')->nullable();
            $table->text('adresse_expediteur');
            $table->string('devise', 10); 
            $table->decimal('montant', 10, 2)->nullable();
            $table->enum('etat', [
                'Devis',
                'Fermé',
                'En attente',
                'Validé',
                'En entrepot',
                'Chargé',
                'En transit',
                'Déchargé',
                'Livré',
                'Annulé'
            ])->default('Devis')->after('user_id');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chauffeur_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devis');
    }
};
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('devis', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('mode_transit'); // 'maritime' ou 'aerien'
            $table->string('pays_expedition');
            $table->string('agence_expedition');
            $table->string('agence_destination'); // Nom unifié pour la destination
            // --- Informations sur l'expéditeur (le client) ---
            $table->string('nom_expediteur');
            $table->string('prenom_expediteur');
            $table->string('email_expediteur')->nullable();
            $table->string('tel_expediteur')->nullable();
            $table->text('adresse_expediteur');
            // --- Information financière globale ---
            $table->string('devise', 10); 
            $table->decimal('montant', 10, 2)->nullable(); // <-- AJOUTEZ ->nullable()
            $table->string('etat')->default('Devis'); // <-- AJOUTEZ CETTE LIGNE
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chauffeur_id')->nullable()->constrained()->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('devis');
    }
};
