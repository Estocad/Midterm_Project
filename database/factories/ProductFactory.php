<?php

namespace Database\Factories;

use App\Models\User;     // Kailangan i-import ang User Model
use App\Models\Category; // Kailangan i-import ang Category Model
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    // ... iba pang code ...
    
    public function definition(): array
    {
        return [
            // Awtomatikong lilikha ng User at Category para magamit sa Product
            'user_id' => User::factory(), 
            'category_id' => Category::factory(),
            'name' => fake()->words(3, true), // Pangalan ng Produkto
            'description' => fake()->paragraph, 
            'price' => fake()->randomFloat(2, 1, 1000), // Presyo
        ];
    }
}