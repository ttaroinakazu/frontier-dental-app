<?php

use App\Models\Product;
use App\Models\User;

beforeEach(function () {
    $this->user = User::factory()->create();
});

test('can get paginated products', function () {
    Product::factory()->count(30)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/products?page=1&per_page=10');  // Updated path

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message',
            'products' => [
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'price',
                        'description',
                    ]
                ],
            ],
            'meta' => [
                'current_page',
                'last_page',
                'per_page',
                'total'
            ]
        ]);

    expect($response->json('products.data'))->toBeArray()
        ->toHaveCount(10);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(10);
    expect($response->json('meta.total'))->toBe(30);
});

test('uses default pagination when no per_page specified', function () {
    Product::factory()->count(30)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/products');  // Updated path

    expect($response->json('products.data'))->toBeArray()
        ->toHaveCount(15);
    expect($response->json('meta.current_page'))->toBe(1);
    expect($response->json('meta.per_page'))->toBe(15);
});

test('can get second page of products', function () {
    Product::factory()->count(30)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/products?page=2&per_page=10');

    expect($response->json('products.data'))->toBeArray()
        ->toHaveCount(10);
    expect($response->json('meta.current_page'))->toBe(2);
    expect($response->json('meta.per_page'))->toBe(10);

    $firstProductOnPageTwo = $response->json('products.data.0.id');
    expect($firstProductOnPageTwo)->toBeGreaterThan(10);
});

test('returns empty products when no products exist', function () {
    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/products');

    expect($response->json('products.data'))->toBeArray()
        ->toHaveCount(0);
    expect($response->json('meta.total'))->toBe(0);
});

test('invalid page number returns appropriate response', function () {
    Product::factory()->count(10)->create();

    $response = $this->actingAs($this->user)
        ->getJson('/api/v1/products?page=999');

    expect($response->json('products.data'))->toBeArray()
        ->toHaveCount(0);
    expect($response->json('meta.current_page'))->toBe(999);
    expect($response->json('meta.total'))->toBe(10);
});
