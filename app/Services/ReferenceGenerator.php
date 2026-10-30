<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Str;

class ReferenceGenerator
{
    private const TEMPORARY_PREFIX = 'TMP-';

    /**
     * Valeur provisoire insérée en base quand la référence est laissée vide :
     * le numéro définitif dépend de l'id, connu seulement après l'insertion.
     */
    public function placeholder(): string
    {
        return self::TEMPORARY_PREFIX.Str::upper(Str::random(20));
    }

    /**
     * Remplace une référence provisoire par PREFIXE-0000 (préfixe de la catégorie + id).
     */
    public function assignIfMissing(Product $product): void
    {
        if (! str_starts_with($product->reference, self::TEMPORARY_PREFIX)) {
            return;
        }

        $product->reference = sprintf('%s-%04d', $product->category->prefix, $product->id);
        $product->save();
    }
}
