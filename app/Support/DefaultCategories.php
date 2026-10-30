<?php

namespace App\Support;

use App\Models\User;

class DefaultCategories
{
    /**
     * @var array<int, array{name: string, prefix: string}>
     */
    public const LIST = [
        ['name' => 'Alimentation', 'prefix' => 'ALI'],
        ['name' => 'Boissons', 'prefix' => 'BOI'],
        ['name' => 'Hygiène', 'prefix' => 'HYG'],
        ['name' => 'Entretien', 'prefix' => 'ENT'],
        ['name' => 'Papeterie', 'prefix' => 'PAP'],
        ['name' => 'Électronique', 'prefix' => 'ELE'],
    ];

    public static function createFor(User $user): void
    {
        $user->categories()->createMany(self::LIST);
    }
}
