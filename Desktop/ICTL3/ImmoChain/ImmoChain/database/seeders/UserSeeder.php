<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Créer plusieurs utilisateurs de démonstration
        User::create([
            'name' => 'Alice Dupont',
            'telephone' => '690123456',
            'telephone_verified_at' => now(),
            'password' => Hash::make('password123'),
            'adresse' => '123 rue Principale, Yaoundé',
            'type_utilisateur' => 'expéditeur',
            'date_naissance' => Carbon::parse('1990-01-01'),
            'cni_front' => 'path/to/cni_front.jpg',
            'cni_back' => 'path/to/cni_back.jpg',
            'facial_scan' => 'path/to/facial_scan.jpg',
            'is_verified' => true,
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Bob Martin',
            'telephone' => '690654321',
            'telephone_verified_at' => now(),
            'password' => Hash::make('password456'),
            'adresse' => '456 rue Secondaire, Douala',
            'type_utilisateur' => 'transporteur',
            'date_naissance' => Carbon::parse('1985-05-15'),
            'cni_front' => 'path/to/cni_front.jpg',
            'cni_back' => 'path/to/cni_back.jpg',
            'facial_scan' => 'path/to/facial_scan.jpg',
            'is_verified' => true,
            'remember_token' => Str::random(10),
        ]);

        User::create([
            'name' => 'Charlie Ndi',
            'telephone' => '690789012',
            'telephone_verified_at' => now(),
            'password' => Hash::make('password789'),
            'adresse' => '789 rue Tertiaire, Garoua',
            'type_utilisateur' => 'administrateur',
            'date_naissance' => Carbon::parse('1975-09-20'),
            'cni_front' => null,
            'cni_back' => null,
            'facial_scan' => null,
            'is_verified' => false,
            'remember_token' => Str::random(10),
        ]);

        // Ajouter des utilisateurs supplémentaires si nécessaire
        for ($i = 0; $i < 10; $i++) {
            User::create([
                'name' => 'Utilisateur ' . $i,
                'telephone' => '690' . str_pad($i + 1000, 6, '0', STR_PAD_LEFT),
                'telephone_verified_at' => now(),
                'password' => Hash::make('password' . $i),
                'adresse' => 'Adresse ' . $i . ', Ville Exemple',
                'type_utilisateur' => collect(['expéditeur', 'transporteur', 'administrateur'])->random(),
                'date_naissance' => Carbon::now()->subYears(rand(20, 50)),
                'cni_front' => null,
                'cni_back' => null,
                'facial_scan' => null,
                'is_verified' => (bool) rand(0, 1),
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
