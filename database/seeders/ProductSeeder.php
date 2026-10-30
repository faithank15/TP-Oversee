<?php

namespace Database\Seeders;

use App\Enums\MovementType;
use App\Enums\Unit;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ProductSeeder extends Seeder
{
    /**
     * Produits écrits à la main : [référence, nom, préfixe de catégorie, unité, prix FCFA, stock initial, seuil].
     *
     * @var array<string, array<int, array{0: string, 1: string, 2: string, 3: Unit, 4: int, 5: int, 6: int}>>
     */
    private function catalogue(): array
    {
        return [
            'adele@oversee.test' => [
                ['ALI-0001', 'Riz parfumé 25 kg', 'ALI', Unit::Sac, 17500, 12, 5],
                ['ALI-0002', 'Huile végétale 5 L', 'ALI', Unit::Piece, 6500, 20, 6],
                ['ALI-0003', 'Sucre en morceaux 1 kg', 'ALI', Unit::Paquet, 900, 3, 10],
                ['ALI-0004', 'Farine de blé 1 kg', 'ALI', Unit::Paquet, 700, 30, 10],
                ['ALI-0005', 'Spaghetti 500 g', 'ALI', Unit::Paquet, 450, 40, 12],
                ['ALI-0006', 'Tomate concentrée 400 g', 'ALI', Unit::Piece, 600, 25, 8],
                ['ALI-0007', 'Sel fin 1 kg', 'ALI', Unit::Sachet, 250, 35, 10],
                ['ALI-0008', 'Lait en poudre 400 g', 'ALI', Unit::Piece, 2800, 15, 6],
                ['BOI-0001', 'Eau minérale 1,5 L (pack de 6)', 'BOI', Unit::Carton, 2400, 25, 8],
                ['BOI-0002', 'Jus de fruits 1 L', 'BOI', Unit::Piece, 1200, 0, 5],
                ['BOI-0003', 'Soda cola 33 cl (casier)', 'BOI', Unit::Carton, 5500, 10, 4],
                ['BOI-0004', 'Thé vert (boîte de 25)', 'BOI', Unit::Paquet, 800, 18, 6],
                ['BOI-0005', 'Café soluble 200 g', 'BOI', Unit::Piece, 2200, 9, 5],
                ['HYG-0001', 'Savon de toilette 200 g', 'HYG', Unit::Piece, 350, 60, 15],
                ['HYG-0002', 'Dentifrice 75 ml', 'HYG', Unit::Piece, 900, 28, 8],
                ['HYG-0003', 'Papier toilette (lot de 4)', 'HYG', Unit::Paquet, 1000, 22, 8],
                ['HYG-0004', 'Shampoing 250 ml', 'HYG', Unit::Piece, 1800, 12, 5],
                ['HYG-0005', 'Serviettes hygiéniques', 'HYG', Unit::Paquet, 700, 16, 6],
                ['ENT-0001', 'Détergent en poudre 1 kg', 'ENT', Unit::Sachet, 1500, 14, 5],
                ['ENT-0002', 'Eau de Javel 1 L', 'ENT', Unit::Litre, 800, 20, 6],
                ['ENT-0003', 'Liquide vaisselle 500 ml', 'ENT', Unit::Piece, 1100, 17, 6],
                ['ENT-0004', 'Éponges (lot de 3)', 'ENT', Unit::Paquet, 500, 24, 8],
                ['PAP-0001', 'Cahier 200 pages', 'PAP', Unit::Piece, 500, 4, 10],
                ['PAP-0002', 'Stylos bille (boîte de 10)', 'PAP', Unit::Paquet, 1500, 13, 5],
                ['PAP-0003', 'Ramette papier A4', 'PAP', Unit::Paquet, 3500, 8, 3],
                ['PAP-0004', 'Crayons de papier (lot de 12)', 'PAP', Unit::Paquet, 1200, 11, 4],
                ['ELE-0001', 'Piles AA (lot de 4)', 'ELE', Unit::Paquet, 1000, 18, 5],
                ['ELE-0002', 'Ampoule LED 9 W', 'ELE', Unit::Piece, 1300, 21, 6],
                ['ELE-0003', 'Câble USB-C 1 m', 'ELE', Unit::Piece, 2500, 7, 4],
                ['ELE-0004', 'Multiprise 4 prises', 'ELE', Unit::Piece, 4500, 6, 3],
            ],
            'carrefour@oversee.test' => [
                ['ALI-0001', 'Riz brisé 50 kg', 'ALI', Unit::Sac, 27500, 8, 3],
                ['ALI-0002', 'Haricots secs 1 kg', 'ALI', Unit::Kg, 900, 30, 10],
                ['ALI-0003', 'Gari 1 kg', 'ALI', Unit::Kg, 500, 2, 8],
                ['BOI-0001', 'Bière locale 65 cl (casier)', 'BOI', Unit::Carton, 7500, 12, 4],
                ['BOI-0002', 'Eau en sachet (paquet de 30)', 'BOI', Unit::Paquet, 1500, 20, 6],
                ['HYG-0001', 'Savon de ménage', 'HYG', Unit::Piece, 300, 45, 12],
                ['HYG-0002', 'Lessive liquide 1 L', 'HYG', Unit::Litre, 1700, 9, 5],
                ['ENT-0001', 'Balai en paille', 'ENT', Unit::Piece, 800, 0, 4],
                ['PAP-0001', 'Cahier 100 pages', 'PAP', Unit::Piece, 300, 50, 15],
                ['ELE-0001', 'Piles R6 (lot de 2)', 'ELE', Unit::Paquet, 500, 26, 8],
            ],
        ];
    }

    /**
     * Le stock initial passe par StockService, daté d'il y a 30 jours.
     */
    public function run(StockService $stock): void
    {
        $catalogue = $this->catalogue();

        foreach (User::all() as $user) {
            Auth::setUser($user);
            $categories = $user->categories()->pluck('id', 'prefix');

            Carbon::setTestNow(now()->subDays(30));

            foreach ($catalogue[$user->email] ?? [] as [$reference, $name, $prefix, $unit, $price, $quantity, $threshold]) {
                $product = $user->products()->create([
                    'category_id' => $categories[$prefix],
                    'reference' => $reference,
                    'name' => $name,
                    'unit' => $unit,
                    'price' => $price,
                    'alert_threshold' => $threshold,
                ]);

                if ($quantity > 0) {
                    $stock->record($product, MovementType::Entree, $quantity, 'Stock initial');
                }
            }

            Carbon::setTestNow();
        }

        Auth::forgetUser();
    }
}
