<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Comptes de démonstration (mot de passe : « password »).
     */
    public function run(): void
    {
        User::create([
            'shop_name' => 'Boutique Chez Adèle',
            'name' => 'Adèle Houngbo',
            'email' => 'adele@oversee.test',
            'password' => 'password',
        ]);

        User::create([
            'shop_name' => 'Superette Le Carrefour',
            'name' => 'Marc Dossou',
            'email' => 'carrefour@oversee.test',
            'password' => 'password',
        ]);
    }
}
