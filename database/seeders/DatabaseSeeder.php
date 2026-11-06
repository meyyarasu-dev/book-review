<?php

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        Book::factory(30)->create()->each(function($book){
            $reviewCount = random_int(5,30);
            Review::factory(33)->count($reviewCount)
            ->good()
            ->for($book)
            ->create();
        });

        Book::factory(30)->create()->each(function($book){
            $reviewCount = random_int(5,30);
            Review::factory(33)->count($reviewCount)
            ->average()
            ->for($book)
            ->create();
        });

        Book::factory(30)->create()->each(function($book){
            $reviewCount = random_int(5,30);
            Review::factory(33)->count($reviewCount)
            ->bad()
            ->for($book)
            ->create();
        });
    }
}
