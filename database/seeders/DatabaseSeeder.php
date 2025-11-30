<?php

// database/seeders/DatabaseSeeder.php
// Tiyakin na ang mga imports ay nasa taas ng file
namespace Database\Seeders;
use App\Models\User;
use App\Models\Category;
use App\Models\Product; 
use Illuminate\Database\Seeder;

// ... (Ibang imports tulad ng use Illuminate\Database\Seeder;)

class DatabaseSeeder extends Seeder 
{
    // ...
    public function run(): void
    {
        // 1. Gumawa ng Users at Categories muna
        User::factory(10)->create(); 
        Category::factory(5)->create();

        // 2. Kumuha ng lahat ng IDs na nalikha
        $userIds = User::pluck('id');
        $categoryIds = Category::pluck('id');

        // 3. Gumawa ng Products at ipasa ang random IDs
        Product::factory(50)->create([
            // Gumamit ng function para pumili ng random na existing ID
            'user_id' => function () use ($userIds) {
                return fake()->randomElement($userIds);
            },
            'category_id' => function () use ($categoryIds) {
                return fake()->randomElement($categoryIds);
            },
        ]);
        
        // Maaari mo ring idagdag ang iyong Test User ulit, pero mas maganda kung 
        // gagamitin mo ang Model::factory()->create()
    }
}
