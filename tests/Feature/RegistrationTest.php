<?php

use App\Models\User;
use App\Support\DefaultCategories;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function registrationData(array $overrides = []): array
{
    return array_merge([
        'shop_name' => 'Boutique Test',
        'name' => 'Gérant Test',
        'email' => 'gerant@oversee.test',
        'password' => 'password-1234',
        'password_confirmation' => 'password-1234',
    ], $overrides);
}

test('registering creates the account with its six default categories', function () {
    $response = $this->post('/register', registrationData());

    $response->assertRedirect('/dashboard');
    $this->assertAuthenticated();

    $user = User::where('email', 'gerant@oversee.test')->firstOrFail();

    expect($user->shop_name)->toBe('Boutique Test')
        ->and($user->categories()->pluck('prefix')->all())
        ->toEqualCanonicalizing(array_column(DefaultCategories::LIST, 'prefix'));
});

test('the shop name is required to register', function () {
    $this->post('/register', registrationData(['shop_name' => '']))
        ->assertSessionHasErrors('shop_name');

    expect(User::count())->toBe(0);
});

test('a duplicate email is refused', function () {
    User::factory()->create(['email' => 'gerant@oversee.test']);

    $this->post('/register', registrationData())->assertSessionHasErrors('email');
});

test('business routes require authentication', function () {
    $this->get('/products')->assertRedirect('/login');
    $this->get('/dashboard')->assertRedirect('/login');
});
