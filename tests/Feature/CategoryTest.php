<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('a category is created in the space of the connected account', function () {
    $this->actingAs($this->user)
        ->post('/categories', ['name' => 'Boissons', 'prefix' => 'boi', 'description' => null])
        ->assertRedirect(route('categories.index'));

    $category = $this->user->categories()->firstOrFail();

    expect($category->prefix)->toBe('BOI');
});

test('category name and prefix are unique per shop only', function () {
    Category::factory()->for($this->user)->create(['name' => 'Boissons', 'prefix' => 'BOI']);

    $this->actingAs($this->user)
        ->post('/categories', ['name' => 'Boissons', 'prefix' => 'BOI'])
        ->assertSessionHasErrors(['name', 'prefix']);

    $otherUser = User::factory()->create();

    $this->actingAs($otherUser)
        ->post('/categories', ['name' => 'Boissons', 'prefix' => 'BOI'])
        ->assertSessionHasNoErrors();
});

test('a category containing products, even deleted ones, cannot be removed', function () {
    $category = Category::factory()->for($this->user)->create();
    Product::factory()->for($this->user)->for($category)->create()->delete();

    $this->actingAs($this->user)
        ->delete("/categories/{$category->id}")
        ->assertSessionHas('error');

    expect($category->fresh())->not->toBeNull();
});

test('an empty category is deleted', function () {
    $category = Category::factory()->for($this->user)->create();

    $this->actingAs($this->user)
        ->delete("/categories/{$category->id}")
        ->assertRedirect(route('categories.index'));

    expect($category->fresh())->toBeNull();
});

test('another shop cannot edit or delete a category', function () {
    $category = Category::factory()->for($this->user)->create();
    $intruder = User::factory()->create();

    $this->actingAs($intruder)->get("/categories/{$category->id}/edit")->assertForbidden();
    $this->actingAs($intruder)->delete("/categories/{$category->id}")->assertForbidden();
    $this->actingAs($intruder)
        ->put("/categories/{$category->id}", ['name' => 'Piraté', 'prefix' => 'PIR'])
        ->assertForbidden();
});
