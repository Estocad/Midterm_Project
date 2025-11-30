<?php

namespace Database\Factories;
// ... imports
use Illuminate\Database\Eloquent\Factories\Factory;

class CategoryFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->word(), // Mag-generate ng unique na pangalan
            'description' => fake()->sentence(), // Mag-generate ng maikling paglalarawan
        ];
    }
}