<?php

use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('user can view their wishlist', function () {
    $wishlist = Wishlist::factory()->create([
        'user_id' => $this->user->id,
        'name' => $this->user->name . "'s Wishlist"
    ]);

    $products = Product::factory()->count(3)->create();
    $wishlist->products()->attach($products->pluck('id'));

    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/wishlist');

    $response->assertStatus(200)
        ->assertJsonStructure([
            'wishlist' => [
                'id',
                'name',
                'user_id',
                'created_at',
                'updated_at',
                'products' => [
                    'data' => [
                        '*' => [
                            'id',
                            'name',
                            'price',
                            'description',
                            'created_at',
                            'updated_at'
                        ]
                    ]
                ]
            ]
        ]);

    expect($response->json('wishlist.products.data'))->toHaveCount(3);
});

test('returns 404 when user has no wishlist', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/wishlist');

    $response->assertStatus(404)
        ->assertJson([
            'status' => 'error',
            'message' => 'No wishlist found'
        ]);
});

test('can create wishlist with products', function () {
    $products = Product::factory()->count(2)->create();

    $response = $this->actingAs($this->user)
        ->postJson('/api/v1/wishlist', [
            'name' => 'My Custom Wishlist',
            'products' => $products->map(fn($product) => ['id' => $product->id])->toArray()
        ]);

    $response->assertStatus(201)
        ->assertJson([
            'message' => 'Wishlist updated successfully'
        ]);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'name' => 'My Custom Wishlist'
    ]);

    // Check if products were attached
    expect($this->user->wishlist->products)->toHaveCount(2);
});

test('can create wishlist without products', function () {
    $response = $this->actingAs($this->user)
        ->postJson('/api/v1/wishlist', [
            'name' => 'Empty Wishlist'
        ]);

    $response->assertStatus(201);

    $this->assertDatabaseHas('wishlists', [
        'user_id' => $this->user->id,
        'name' => 'Empty Wishlist'
    ]);
});

test('can remove product from wishlist', function () {
    $wishlist = Wishlist::factory()->create([
        'user_id' => $this->user->id
    ]);

    $product = Product::factory()->create();
    $wishlist->products()->attach($product->id);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v1/wishlist/{$product->id}");

    $response->assertStatus(200)
        ->assertJson([
            'message' => 'Product removed from wishlist'
        ]);

    expect($wishlist->fresh()->products)->toHaveCount(0);
});

test('returns 404 when removing product from non-existent wishlist', function () {
    $product = Product::factory()->create();

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v1/wishlist/{$product->id}");

    $response->assertStatus(404)
        ->assertJson([
            'message' => 'Wishlist not found'
        ]);
});

test('returns 404 when removing non-existent product from wishlist', function () {
    $wishlist = Wishlist::factory()->create([
        'user_id' => $this->user->id
    ]);

    $response = $this->actingAs($this->user)
        ->deleteJson("/api/v1/wishlist/999");

    $response->assertStatus(422)
        ->assertJson([
            'message' => 'The selected product does not exist',
            'errors' => [
                'product' => ['The selected product does not exist']
            ]
        ]);
});
