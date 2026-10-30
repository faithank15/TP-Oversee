<?php

use App\Enums\MovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\User;
use App\Services\StockService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Validation\ValidationException;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->actingAs($this->user);
    $this->product = Product::factory()->for($this->user)->create(['quantity' => 10]);
    $this->stock = app(StockService::class);
});

test('an entry increases the stock and writes the history', function () {
    $movement = $this->stock->record($this->product, MovementType::Entree, 5);

    expect($this->product->fresh()->quantity)->toBe(15)
        ->and($movement->quantity)->toBe(5)
        ->and($movement->stock_before)->toBe(10)
        ->and($movement->stock_after)->toBe(15)
        ->and($movement->user_id)->toBe($this->user->id);
});

test('an exit decreases the stock and is stored as a negative change', function () {
    $movement = $this->stock->record($this->product, MovementType::Sortie, 3);

    expect($this->product->fresh()->quantity)->toBe(7)
        ->and($movement->quantity)->toBe(-3)
        ->and($movement->stock_after)->toBe(7);
});

test('an exit larger than the stock is refused and nothing is written', function () {
    expect(fn () => $this->stock->record($this->product, MovementType::Sortie, 11))
        ->toThrow(ValidationException::class, 'Stock insuffisant');

    expect($this->product->fresh()->quantity)->toBe(10)
        ->and(StockMovement::count())->toBe(0);
});

test('an adjustment records the difference with the counted stock', function () {
    $movement = $this->stock->record($this->product, MovementType::Ajustement, 8, '2 articles abîmés');

    expect($this->product->fresh()->quantity)->toBe(8)
        ->and($movement->quantity)->toBe(-2)
        ->and($movement->note)->toBe('2 articles abîmés');
});

test('an adjustment equal to the current stock is refused', function () {
    expect(fn () => $this->stock->record($this->product, MovementType::Ajustement, 10, 'Inventaire'))
        ->toThrow(ValidationException::class, 'aucun ajustement nécessaire');

    expect(StockMovement::count())->toBe(0);
});

test('a movement can neither be updated nor deleted', function () {
    $movement = $this->stock->record($this->product, MovementType::Entree, 1);

    expect(fn () => $movement->update(['note' => 'x']))->toThrow(LogicException::class)
        ->and(fn () => $movement->delete())->toThrow(LogicException::class);
});

test('the history survives the soft delete of the product', function () {
    $movement = $this->stock->record($this->product, MovementType::Entree, 1);

    $this->product->delete();

    expect($movement->fresh()->product->trashed())->toBeTrue();
});
