<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->product = Product::factory()->for($this->user)->create(['quantity' => 10]);
});

test('an exit is recorded and updates the stock', function () {
    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $this->product->id, 'type' => 'sortie', 'quantity' => 3])
        ->assertRedirect(route('products.show', $this->product));

    expect($this->product->fresh()->quantity)->toBe(7);
});

test('an exit larger than the stock is rejected with a message on quantity', function () {
    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $this->product->id, 'type' => 'sortie', 'quantity' => 50])
        ->assertSessionHasErrors('quantity');

    expect($this->product->fresh()->quantity)->toBe(10);
});

test('an adjustment requires a note and accepts a zero count', function () {
    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $this->product->id, 'type' => 'ajustement', 'quantity' => 0])
        ->assertSessionHasErrors('note');

    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $this->product->id, 'type' => 'ajustement', 'quantity' => 0, 'note' => 'Casse'])
        ->assertSessionHasNoErrors();

    expect($this->product->fresh()->quantity)->toBe(0);
});

test('entry and exit quantities must be at least one', function () {
    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $this->product->id, 'type' => 'entree', 'quantity' => 0])
        ->assertSessionHasErrors('quantity');
});

test('a movement cannot target a product of another shop or a deleted product', function () {
    $foreignProduct = Product::factory()->create(['quantity' => 10]);
    $deletedProduct = Product::factory()->for($this->user)->create(['quantity' => 10]);
    $deletedProduct->delete();

    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $foreignProduct->id, 'type' => 'entree', 'quantity' => 1])
        ->assertSessionHasErrors('product_id');

    $this->actingAs($this->user)
        ->post('/movements', ['product_id' => $deletedProduct->id, 'type' => 'entree', 'quantity' => 1])
        ->assertSessionHasErrors('product_id');

    expect($foreignProduct->fresh()->quantity)->toBe(10);
});

test('movements have no update or delete route', function () {
    $this->actingAs($this->user)->put('/movements/1')->assertNotFound();
    $this->actingAs($this->user)->delete('/movements/1')->assertNotFound();
});
