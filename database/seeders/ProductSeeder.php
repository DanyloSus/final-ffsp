<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = \App\Models\User::all();
        $categories = \App\Models\Category::all();

        foreach ($users as $user) {
            $randomCategory = $categories->shuffle()->first();

            \App\Models\Product::factory()->for($user)->for($randomCategory)->create();
        }
    }
}
