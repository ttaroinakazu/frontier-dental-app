<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('user can register', function () {
    $userData = [
        'name' => fake()->name(),
        'email' => fake()->unique()->safeEmail(),
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];

    $response = postJson('/api/register', $userData);

    $response->assertStatus(201)
        ->assertJsonStructure([
            'message',
            'user',
            'token'
        ]);

    expect(User::where('email', $userData['email'])->exists())->toBeTrue();
});

test('user cannot register with existing email', function () {
    $existingUser = User::factory()->create();

    $response = postJson('/api/register', [
        'name' => fake()->name(),
        'email' => $existingUser->email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['email']);
});

test('user can login', function () {
    $user = User::factory()->create([
        'password' => bcrypt('password123'),
    ]);

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'password123',
    ]);

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'message',
            'data' => [
                'user',
                'token'
            ]
        ]);
});

test('user cannot login with invalid credentials', function () {
    $user = User::factory()->create();

    $response = postJson('/api/login', [
        'email' => $user->email,
        'password' => 'wrong_password',
    ]);

    $response->assertStatus(401)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ]);

});

test('user can logout', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = postJson('/api/v1/logout');

    $response->assertOk()
        ->assertJson([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);

    // Replace db() with DB facade
    expect(\Illuminate\Support\Facades\DB::table('personal_access_tokens')->count())->toBe(0);
});


test('user can get their profile', function () {
    $user = User::factory()->create();
    Sanctum::actingAs($user);

    $response = getJson('/api/v1/user');

    $response->assertOk()
        ->assertJsonStructure([
            'status',
            'data' => [
                'user'
            ]
        ]);
});

test('unauthenticated user cannot access protected routes', function () {
    $response = getJson('/api/v1/user');

    $response->assertUnauthorized();
});

test('registration validates required fields', function () {
    $response = postJson('/api/register', [
        'name' => '',
        'email' => 'not-an-email',
        'password' => 'short',
        'password_confirmation' => 'different',
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['name', 'email', 'password']);
});

test('login is rate limited after too many attempts', function () {
    $user = User::factory()->create();

    for ($i = 0; $i < 6; $i++) {
        $response = postJson('/api/login', [
            'email' => $user->email,
            'password' => 'wrong_password',
        ]);
    }

    $response->assertStatus(401)
        ->assertJson([
            'status' => 'error',
            'message' => 'Invalid credentials',
        ]);
});

