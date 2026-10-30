<?php

namespace Database\Seeders;

use App\Models\User;
use App\Support\DefaultCategories;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Les 6 catégories par défaut pour chaque compte, comme à l'inscription.
     */
    public function run(): void
    {
        User::all()->each(fn (User $user) => DefaultCategories::createFor($user));
    }
}
