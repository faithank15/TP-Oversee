<?php

namespace App\Services;

use App\Enums\MovementType;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockService
{
    /**
     * Seul point d'écriture du stock : met à jour la quantité et écrit l'historique
     * dans une même transaction. Pour un ajustement, $value est le stock réel compté.
     *
     * @throws ValidationException
     */
    public function record(Product $product, MovementType $type, int $value, ?string $note = null): StockMovement
    {
        return DB::transaction(function () use ($product, $type, $value, $note) {
            $product = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();
            $before = $product->quantity;

            $change = match ($type) {
                MovementType::Entree => $value,
                MovementType::Sortie => -$value,
                MovementType::Ajustement => $value - $before,
            };

            if ($before + $change < 0) {
                throw ValidationException::withMessages([
                    'quantity' => "Stock insuffisant : {$before} {$product->unit->label()} disponible(s).",
                ]);
            }

            if ($type === MovementType::Ajustement && $change === 0) {
                throw ValidationException::withMessages([
                    'quantity' => 'Le stock compté est identique au stock actuel : aucun ajustement nécessaire.',
                ]);
            }

            $product->forceFill(['quantity' => $before + $change])->save();

            return $product->movements()->create([
                'user_id' => auth()->id(),
                'type' => $type,
                'quantity' => $change,
                'stock_before' => $before,
                'stock_after' => $before + $change,
                'note' => $note,
            ]);
        });
    }
}
