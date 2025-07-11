<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Wishlist;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create admin user
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        // Create regular users
        $users = User::factory(5)->create();

        // Create products
        $products = Product::factory(20)->create();

        // Create one wishlist for each user and attach random products
        $users->each(function ($user) use ($products) {
            Wishlist::create([
                'user_id' => $user->id,
                'name' => $user->name . "'s Wishlist"
            ])->products()->attach(
                $products->random(rand(3, 8))->pluck('id')->toArray()
            );
        });
    }
}


