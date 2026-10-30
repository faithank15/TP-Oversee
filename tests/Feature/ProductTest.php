<?php

use App\Enums\MovementType;
use App\Enums\Unit;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
    $this->category = Category::factory()->for($this->user)->create(['prefix' => 'ALI']);
});

function productData(Category $category, array $overrides = []): array
{
    return array_merge([
        'name' => 'Riz parfumé 25 kg',
        'category_id' => $category->id,
        'reference' => '',
        'unit' => Unit::Sac->value,
        'price' => 17500,
        'alert_threshold' => 5,
        'initial_quantity' => 12,
    ], $overrides);
}

test('creating a product records the initial stock as a movement', function () {
    $response = $this->actingAs($this->user)->post('/products', productData($this->category, ['reference' => ' riz-25kg ']));

    $product = $this->user->products()->firstOrFail();

    $response->assertRedirect(route('products.show', $product));
    expect($product->quantity)->toBe(12)
        ->and($product->reference)->toBe('RIZ-25KG')
        ->and($product->movements)->toHaveCount(1)
        ->and($product->movements->first()->type)->toBe(MovementType::Entree)
        ->and($product->movements->first()->note)->toBe('Stock initial');
});

test('an empty reference is generated from the category prefix and the id', function () {
    $this->actingAs($this->user)->post('/products', productData($this->category));

    $product = $this->user->products()->firstOrFail();

    expect($product->reference)->toBe(sprintf('ALI-%04d', $product->id));
});

test('a product without initial stock has no movement', function () {
    $this->actingAs($this->user)->post('/products', productData($this->category, ['initial_quantity' => 0]));

    expect($this->user->products()->firstOrFail()->movements)->toBeEmpty();
});

test('quantity cannot be set through the form', function () {
    $this->actingAs($this->user)->post('/products', productData($this->category, ['initial_quantity' => 0, 'quantity' => 999]));

    expect($this->user->products()->firstOrFail()->quantity)->toBe(0);
});

test('a product cannot use a category of another shop', function () {
    $foreignCategory = Category::factory()->create();

    $this->actingAs($this->user)
        ->post('/products', productData($foreignCategory))
        ->assertSessionHasErrors('category_id');
});

test('a reference is unique per shop, deleted products included', function () {
    Product::factory()->for($this->user)->for($this->category)->create(['reference' => 'DUP-1'])->delete();

    $this->actingAs($this->user)
        ->post('/products', productData($this->category, ['reference' => 'dup-1']))
        ->assertSessionHasErrors('reference');

    $otherUser = User::factory()->create();
    $otherCategory = Category::factory()->for($otherUser)->create();

    $this->actingAs($otherUser)
        ->post('/products', productData($otherCategory, ['reference' => 'DUP-1']))
        ->assertSessionHasNoErrors();
});

test('updating a product keeps its own reference valid', function () {
    $product = Product::factory()->for($this->user)->for($this->category)->create(['reference' => 'KEEP-1']);

    $this->actingAs($this->user)
        ->put("/products/{$product->id}", productData($this->category, ['reference' => 'KEEP-1', 'price' => 900]))
        ->assertRedirect(route('products.show', $product))
        ->assertSessionHasNoErrors();

    expect($product->fresh()->price)->toBe(900);
});

test('another shop cannot see, edit, update or delete a product', function () {
    $product = Product::factory()->for($this->user)->for($this->category)->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->get("/products/{$product->id}")->assertForbidden();
    $this->actingAs($intruder)->get("/products/{$product->id}/edit")->assertForbidden();
    $this->actingAs($intruder)->delete("/products/{$product->id}")->assertForbidden();
    $this->actingAs($intruder)
        ->put("/products/{$product->id}", productData(Category::factory()->for($intruder)->create()))
        ->assertForbidden();

    expect($product->fresh()->trashed())->toBeFalse();
});

test('deleting a product soft deletes it and keeps its history', function () {
    $this->actingAs($this->user)->post('/products', productData($this->category));
    $product = $this->user->products()->firstOrFail();

    $this->actingAs($this->user)->delete("/products/{$product->id}")->assertRedirect(route('products.index'));

    expect($this->user->products()->count())->toBe(0)
        ->and($this->user->products()->onlyTrashed()->count())->toBe(1)
        ->and($product->movements()->count())->toBe(1);
});

test('a trashed product can be restored by its owner only', function () {
    $product = Product::factory()->for($this->user)->for($this->category)->create();
    $product->delete();

    $this->actingAs(User::factory()->create())
        ->patch("/products/{$product->id}/restore")
        ->assertNotFound();

    $this->actingAs($this->user)
        ->patch("/products/{$product->id}/restore")
        ->assertRedirect(route('products.show', $product));

    expect($product->fresh()->trashed())->toBeFalse();
});
