<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class WishlistFactory extends Factory
{
    public function definition(): array
    {
        $user = User::factory()->create();
        return [
            'name' => $user->name . "'s Wishlist",
            'user_id' => $user->id,
        ];
    }
}
