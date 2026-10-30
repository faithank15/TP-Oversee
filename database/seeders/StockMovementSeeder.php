<?php

namespace Database\Seeders;

use App\Enums\MovementType;
use App\Models\Product;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class StockMovementSeeder extends Seeder
{
    /**
     * Historique sur 30 jours, toujours via StockService pour garder
     * stock_before, stock_after et products.quantity cohérents.
     */
    public function run(StockService $stock): void
    {
        mt_srand(42);

        $counts = ['adele@oversee.test' => 80, 'carrefour@oversee.test' => 20];

        foreach (User::all() as $user) {
            Auth::setUser($user);
            $products = $user->products()->get();

            if ($products->isEmpty()) {
                continue;
            }

            $total = $counts[$user->email] ?? 20;

            for ($i = 0; $i < $total; $i++) {
                $date = now()->subDays(29)->addMinutes((int) round($i * (29 * 24 * 60 - 1) / max($total - 1, 1)));
                $this->recordRandomMovement($stock, $products->random(), $date);
            }

            if ($user->email === 'adele@oversee.test') {
                $this->createVisibleAlerts($stock, $user);
                $this->softDeleteProductsWithHistory($user);
            }
        }

        Auth::forgetUser();
        mt_srand();
    }

    private function recordRandomMovement(StockService $stock, Product $product, Carbon $date): void
    {
        $product->refresh();
        $roll = mt_rand(1, 100);

        Carbon::setTestNow($date);

        if ($roll <= 40 || $product->quantity === 0) {
            $stock->record($product, MovementType::Entree, mt_rand(2, 12), 'Réapprovisionnement');
        } elseif ($roll <= 92) {
            $stock->record($product, MovementType::Sortie, mt_rand(1, max(1, min($product->quantity, 6))));
        } elseif ($product->quantity > 1) {
            $stock->record($product, MovementType::Ajustement, $product->quantity - 1, '1 article abîmé');
        }

        Carbon::setTestNow();
    }

    /**
     * Quelques ruptures et stocks faibles visibles dès l'ouverture du tableau de bord.
     */
    private function createVisibleAlerts(StockService $stock, User $user): void
    {
        foreach (['BOI-0002' => 0, 'ALI-0003' => 3, 'PAP-0001' => 4] as $reference => $counted) {
            $product = $user->products()->where('reference', $reference)->first();

            if ($product && $product->quantity !== $counted) {
                $stock->record($product, MovementType::Ajustement, $counted, 'Inventaire du jour');
            }
        }
    }

    /**
     * Des produits supprimés après avoir reçu des mouvements : historique conservé, corbeille remplie.
     */
    private function softDeleteProductsWithHistory(User $user): void
    {
        $user->products()->whereIn('reference', ['ENT-0004', 'ELE-0004'])->get()->each->delete();
    }
}
